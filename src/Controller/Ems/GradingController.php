<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Grading;
use Cake\Http\Response;
use Cake\I18n\FrozenDate;

/**
 * Grading schemes — the versioned scale (document.md §3.3). Editing never
 * mutates a version in place: an identical scale is a no-op, and any real change
 * retires the active version and appends a new one, so a released examination
 * keeps pointing at the scale it was graded on. This is what makes an issued
 * report card reproducible after the scale is later changed.
 */
class GradingController extends AppController
{
    /**
     * GET /grading/active — never 404s (synthesised default if none saved).
     */
    public function active(): Response
    {
        return $this->json($this->grading()->activeScheme());
    }

    /**
     * GET /grading/history — every version, newest first (empty for default only).
     */
    public function history(): Response
    {
        $rows = $this->tenant()->query('EmsGradingSchemes')
            ->orderByDesc('version')
            ->all();

        return $this->json(array_map([Grading::class, 'schemeWire'], $rows->toList()));
    }

    /**
     * PUT /grading — validate, no-op guard, else retire + append a new version.
     */
    public function update(): Response
    {
        $body = $this->body();
        $bands = is_array($body['bands'] ?? null) ? $body['bands'] : [];
        $note = trim((string)($body['note'] ?? ''));

        $error = Grading::validateBands($bands);
        if ($error !== null) {
            $this->fail(422, $error);
        }
        $clean = Grading::cleanBands($bands);

        $schemes = $this->fetchTable('EmsGradingSchemes');
        $active = $this->tenant()->query('EmsGradingSchemes')
            ->where(['status' => 'active'])
            ->first();

        // Nothing changed → don't spend a version.
        if ($active !== null) {
            $activeBands = $active->bands;
            if (is_string($activeBands)) {
                $activeBands = json_decode($activeBands, true);
            }
            if (Grading::bandsEqual(Grading::cleanBands(is_array($activeBands) ? $activeBands : []), $clean)) {
                return $this->json(Grading::schemeWire($active));
            }
        }

        $maxRow = $this->tenant()->query('EmsGradingSchemes')
            ->select(['m' => 'MAX(version)'])
            ->first();
        $version = (int)($maxRow->m ?? 0) + 1;

        $scheme = $schemes->newEntity([
            'school_id' => $this->viewer->schoolId,
            'version' => $version,
            'status' => 'active',
            'bands' => $clean,
            'effective_from' => FrozenDate::today(),
            'created_by' => $this->viewer->name,
            'note' => $note !== '' ? $note : null,
        ], ['validate' => false]);

        $schemes->getConnection()->transactional(function () use ($schemes, $active, $scheme) {
            if ($active !== null) {
                $active->status = 'retired';
                $schemes->saveOrFail($active);
            }
            $schemes->saveOrFail($scheme);
        });

        $this->audit()->log(
            $this->viewer,
            'grading.updated',
            'grading_scheme',
            (string)$scheme->id,
            sprintf('Updated the grading scale to version %d (%d grades)', $version, count($clean)),
            $note !== '' ? $note : null
        );

        return $this->json(Grading::schemeWire($scheme));
    }
}
