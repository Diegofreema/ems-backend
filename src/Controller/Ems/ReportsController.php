<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Messages;
use App\Ems\Reports;
use App\Ems\Serializer\ReportSerializer;
use App\Ems\Serializer\Wire;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;

/**
 * Async CSV exports (document.md §3.21). Staff-only at the door; each report
 * definition additionally names the roles that may run it, re-checked at
 * download. A run is a JOB the worker settles over real time — the frontend
 * polls the jobs list while anything is queued/running. A finished file expires
 * after 7 days (its download 410s), and every download writes an audit event so
 * an auditor can answer "who took a copy of this, and when".
 */
class ReportsController extends AppController
{
    /** GET /reports/jobs — newest first; settles the worker on read. */
    public function jobs(): Response
    {
        $params = $this->pageParams();
        $engine = $this->reportsEngine();
        $jobsTable = $this->fetchTable('EmsReportJobs');
        $rows = $this->tenant()->query('EmsReportJobs')->all()->toList();
        foreach ($rows as $job) {
            if ($engine->settle($job, $this->elapsedMs($job))) {
                $jobsTable->saveOrFail($job);
            }
        }
        usort($rows, fn($a, $b) => strcmp((string)$b->requested_on, (string)$a->requested_on)
            ?: strcmp((string)$b->created, (string)$a->created));

        $total = count($rows);
        $page = array_slice($rows, ($params['page'] - 1) * $params['pageSize'], $params['pageSize']);

        return $this->paginated(
            array_map([ReportSerializer::class, 'job'], $page),
            $total,
            $params['page'],
            $params['pageSize'],
        );
    }

    /** POST /reports/jobs — { reportType, filters }. Queued for the worker. */
    public function add(): Response
    {
        $body = $this->body();
        $reportType = (string)($body['reportType'] ?? '');
        $filters = is_array($body['filters'] ?? null) ? $body['filters'] : [];
        $this->assertPermitted($reportType);

        $jobs = $this->fetchTable('EmsReportJobs');
        $job = $jobs->newEntity([
            'school_id' => $this->viewer->schoolId,
            'report_type' => $reportType,
            'requested_by' => $this->viewer->name,
            'requested_on' => FrozenDate::today(),
            'filters' => $filters,
            'status' => 'queued',
            'downloads' => [],
        ], ['validate' => false]);
        $jobs->saveOrFail($job);

        return $this->json(ReportSerializer::job($job), 201);
    }

    /** POST /reports/jobs/{id}/download — permission re-checked; 410/409; audited. */
    public function download(string $id): Response
    {
        $jobs = $this->fetchTable('EmsReportJobs');
        $job = $this->tenant()->query('EmsReportJobs')->where(['id' => $id])->first();
        if ($job === null) {
            $this->fail(404, Messages::REPORT_NOT_FOUND);
        }
        $engine = $this->reportsEngine();
        if ($engine->settle($job, $this->elapsedMs($job))) {
            $jobs->saveOrFail($job);
        }
        $this->assertPermitted((string)$job->report_type);
        if ((string)$job->status === 'expired') {
            $this->fail(410, Messages::REPORT_EXPIRED);
        }
        if ((string)$job->status !== 'ready') {
            $this->fail(409, Messages::REPORT_NOT_READY);
        }

        $definition = Reports::DEFINITIONS[(string)$job->report_type];
        $rows = $engine->build((string)$job->report_type, $this->filtersOf($job));
        $rowCount = count($rows['rows']);
        $job->row_count = $rowCount;
        $downloads = $this->downloadsOf($job);
        $downloads[] = ['by' => $this->viewer->name, 'at' => FrozenDate::today()->format('Y-m-d')];
        $job->downloads = $downloads;
        $jobs->saveOrFail($job);

        $school = $this->fetchTable('EmsSchools')->find()
            ->where(['id' => $this->viewer->schoolId])->first();
        $content = $engine->labelled($school, $job, $rows);

        $this->audit()->log(
            $this->viewer,
            'report.downloaded',
            'report',
            (string)$job->id,
            sprintf('Downloaded the %s export (%d rows)', strtolower($definition['title']), $rowCount),
        );

        return $this->json([
            'filename' => (string)$job->report_type . '-' . Wire::date($job->requested_on) . '.csv',
            'content' => $content,
            'rowCount' => $rowCount,
        ]);
    }

    // --- helpers -------------------------------------------------------------

    private function assertPermitted(string $reportType): void
    {
        if (!Reports::isKnownType($reportType)) {
            $this->fail(404, Messages::REPORT_NOT_FOUND);
        }
        $definition = Reports::DEFINITIONS[$reportType];
        $this->requireRole(
            $definition['roles'],
            sprintf('Your role cannot run the %s report.', strtolower($definition['title'])),
        );
    }

    private function elapsedMs(EntityInterface $job): int
    {
        $created = $job->created instanceof FrozenTime ? $job->created : FrozenTime::now();

        return max(0, (FrozenTime::now()->getTimestamp() - $created->getTimestamp()) * 1000);
    }

    private function filtersOf(EntityInterface $job): array
    {
        $f = $job->filters;
        if (is_string($f)) {
            $f = json_decode($f, true);
        }

        return is_array($f) ? $f : [];
    }

    private function downloadsOf(EntityInterface $job): array
    {
        $d = $job->downloads;
        if (is_string($d)) {
            $d = json_decode($d, true);
        }

        return is_array($d) ? $d : [];
    }
}
