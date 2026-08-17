<?php
declare(strict_types=1);

namespace App\Command;

use App\Ems\Comms;
use App\Ems\FeeReminders;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;

/**
 * The scheduled seam for fee reminders (document.md §3.7). Runs the SAME
 * App\Ems\FeeReminders service the bursar's manual send uses, across every
 * school, for both overdue and due-soon reminders. There is no cron or queue in
 * this app: to run nightly, an external scheduler must invoke
 * `bin/cake send_fee_reminders` — this command does not schedule itself. The
 * per-instalment cooldown makes a repeated run safe (already-reminded families
 * are suppressed), so it is idempotent within the cooldown window.
 */
final class SendFeeRemindersCommand extends Command
{
    public static function getDescription(): string
    {
        return 'Send overdue and due-soon fee reminders to owing families across every school.';
    }

    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->addOption('school', ['help' => 'Send reminders only for this school identifier.'])
            ->addOption('kind', ['help' => 'Limit to a single kind: overdue or due_soon.']);
    }

    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $db = ConnectionManager::get('default');
        $today = date('Y-m-d');
        $frontendBaseUrl = (string)Configure::read('Ems.frontendBaseUrl', '');
        $locator = TableRegistry::getTableLocator();

        $kindFilter = trim((string)$args->getOption('kind'));
        if ($kindFilter !== '' && !FeeReminders::isKind($kindFilter)) {
            $io->err('Unknown kind: ' . $kindFilter . ' (use overdue or due_soon).');

            return self::CODE_ERROR;
        }
        $kinds = $kindFilter !== '' ? [$kindFilter] : [FeeReminders::OVERDUE, FeeReminders::DUE_SOON];

        $schoolFilter = trim((string)$args->getOption('school'));
        $sql = 'SELECT id FROM ems_schools';
        $params = [];
        if ($schoolFilter !== '') {
            $sql .= ' WHERE id=?';
            $params[] = $schoolFilter;
        }
        $schools = $db->execute($sql, $params)->fetchAll('assoc');
        if ($schoolFilter !== '' && $schools === []) {
            $io->err('School not found: ' . $schoolFilter);

            return self::CODE_ERROR;
        }

        $totalReminded = 0;
        foreach ($schools as $school) {
            $schoolId = (string)$school['id'];
            $reminders = new FeeReminders($locator, $schoolId, $today);
            $comms = new Comms($locator, $schoolId, $today);
            foreach ($kinds as $kind) {
                $summary = $reminders->send($kind, $comms, $frontendBaseUrl, 'Fee reminder scheduler');
                $totalReminded += (int)$summary['remindedCount'];
                $io->out(sprintf(
                    '%s %s: %d reminded, %d suppressed, %d emailed',
                    $schoolId,
                    $kind,
                    (int)$summary['remindedCount'],
                    (int)$summary['suppressedCount'],
                    (int)$summary['emailed'],
                ));
            }
        }
        $io->out(sprintf('Done: %d reminder(s) sent across %d school(s).', $totalReminded, count($schools)));

        return self::CODE_SUCCESS;
    }
}
