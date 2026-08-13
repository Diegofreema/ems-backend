<?php
declare(strict_types=1);

namespace App\Command;

use App\Ems\Audit;
use App\Ems\FinanceChain;
use App\Ems\FinanceKeys;
use App\Ems\Viewer;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Datasource\ConnectionInterface;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\Utility\Text;
use RuntimeException;

/** Verifies finance chains and locks a school when any invariant fails. */
final class FinanceVerifyCommand extends Command
{
    /**
     * Describe the command.
     *
     * @return string Human readable command description.
     */
    public static function getDescription(): string
    {
        return 'Verify finance chains and optionally clear a verified lock with school administrator approval.';
    }

    /**
     * Define supported verification and clearance options.
     *
     * @param \Cake\Console\ConsoleOptionParser $parser Parser to configure.
     * @return \Cake\Console\ConsoleOptionParser Configured parser.
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->addOption('school', [
                'help' => 'Verify only this school identifier.',
            ])
            ->addOption('clear-by', [
                'help' => 'Clear this school lock after success using this active administrator identifier.',
            ]);
    }

    /**
     * Run finance verification.
     *
     * @param \Cake\Console\Arguments $args Parsed command arguments.
     * @param \Cake\Console\ConsoleIo $io Console input and output.
     * @return int Process exit code.
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $db = ConnectionManager::get('default');
        try {
            $keys = FinanceKeys::verificationKeys();
        } catch (RuntimeException $exception) {
            $io->err($exception->getMessage());

            return self::CODE_ERROR;
        }

        $schoolFilter = trim((string)$args->getOption('school'));
        $clearBy = trim((string)$args->getOption('clear-by'));
        if ($clearBy !== '' && $schoolFilter === '') {
            $io->err('--clear-by requires --school so an administrator can clear only their own school.');

            return self::CODE_ERROR;
        }

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

        $failed = 0;
        foreach ($schools as $school) {
            $id = (string)$school['id'];
            $fault = $this->verifySchool($db, $id, $keys);
            if ($fault !== null) {
                $failed++;
                $this->recordFault($db, $id, $fault);
                $io->err($id . ': ' . $fault);
                continue;
            }
            if ($clearBy !== '' && !$this->clearVerifiedLock($db, $id, $clearBy, $io)) {
                $failed++;
                continue;
            }
            $io->out($id . ': verified');
        }

        return $failed === 0 ? self::CODE_SUCCESS : self::CODE_ERROR;
    }

    /**
     * Verify all financial invariants for one school.
     *
     * @param \Cake\Datasource\ConnectionInterface $db Database connection.
     * @param string $school School identifier.
     * @param array<string, string> $keys Retained verification keys.
     * @return string|null First integrity fault, or null when healthy.
     */
    private function verifySchool(ConnectionInterface $db, string $school, array $keys): ?string
    {
        return $this->verifyLedger($db, $school, $keys)
            ?? $this->verifyBalances($db, $school)
            ?? $this->verifyAudit($db, $school, $keys);
    }

    /**
     * Verify the ledger in signed link order, independent of timestamps.
     *
     * @param \Cake\Datasource\ConnectionInterface $db Database connection.
     * @param string $school School identifier.
     * @param array<string, string> $keys Retained verification keys.
     * @return string|null First ledger fault, or null when healthy.
     */
    private function verifyLedger(ConnectionInterface $db, string $school, array $keys): ?string
    {
        $rows = $db->execute(
            'SELECT * FROM ems_finance_ledger_events WHERE school_id=?',
            [$school],
        )->fetchAll('assoc');
        try {
            $rows = FinanceChain::order($rows);
        } catch (RuntimeException $exception) {
            return $exception->getMessage();
        }

        $previous = FinanceChain::GENESIS_HASH;
        foreach ($rows as $row) {
            $key = $keys[(string)$row['key_id']] ?? null;
            if ($key === null) {
                return 'Ledger signing key unavailable: ' . $row['key_id'];
            }
            $at = (string)$row['occurred_at'];
            if ((string)$row['provenance'] === 'legacy_migration') {
                $canonical = implode('|', [
                    $row['id'], $school, $row['invoice_id'], $row['student_id'],
                    $row['payment_id'], $row['event_type'], $row['amount'], $at, $previous,
                ]);
            } else {
                $canonical = implode('|', [
                    $row['id'], $school, $row['invoice_id'], $row['student_id'],
                    $row['payment_id'] ?? '', $row['adjustment_request_id'] ?? '',
                    $row['event_type'], $row['amount'], $row['provenance'], $at, $previous,
                ]);
            }
            $hash = hash_hmac('sha256', $canonical, $key);
            if (!hash_equals($hash, (string)$row['event_hash'])) {
                return 'Ledger event seal mismatch at ' . $row['id'];
            }
            $previous = $hash;
        }

        return null;
    }

    /**
     * Verify invoice arithmetic and payment snapshot relationships.
     *
     * @param \Cake\Datasource\ConnectionInterface $db Database connection.
     * @param string $school School identifier.
     * @return string|null First accounting fault, or null when healthy.
     */
    private function verifyBalances(ConnectionInterface $db, string $school): ?string
    {
        $rows = $db->execute(
            'SELECT i.id,i.total,COALESCE(SUM(l.amount),0) paid '
            . 'FROM ems_invoices i LEFT JOIN ems_finance_ledger_events l '
            . 'ON l.school_id=i.school_id AND l.invoice_id=i.id '
            . 'WHERE i.school_id=? GROUP BY i.id,i.total',
            [$school],
        )->fetchAll('assoc');
        foreach ($rows as $row) {
            if ((int)$row['paid'] < 0 || (int)$row['paid'] > (int)$row['total']) {
                return 'Invoice balance invariant failed for ' . $row['id'];
            }
        }
        $bad = $db->execute(
            'SELECT p.id FROM ems_payments p '
            . 'LEFT JOIN ems_receipts r ON r.school_id=p.school_id AND r.payment_id=p.id '
            . 'LEFT JOIN ems_finance_ledger_events l ON l.school_id=p.school_id '
            . "AND l.payment_id=p.id AND l.event_type='payment' "
            . 'WHERE p.school_id=? AND (r.id IS NULL OR l.id IS NULL '
            . 'OR r.amount<>p.amount OR l.amount<>p.amount) LIMIT 1',
            [$school],
        )->fetch('assoc');

        return $bad ? 'Payment, receipt, and ledger relationship mismatch at ' . $bad['id'] : null;
    }

    /**
     * Verify the sealed audit chain.
     *
     * @param \Cake\Datasource\ConnectionInterface $db Database connection.
     * @param string $school School identifier.
     * @param array<string, string> $keys Retained verification keys.
     * @return string|null First audit fault, or null when healthy.
     */
    private function verifyAudit(ConnectionInterface $db, string $school, array $keys): ?string
    {
        $rows = $db->execute(
            'SELECT * FROM ems_audit_events WHERE school_id=? '
            . 'AND event_hash IS NOT NULL ORDER BY seq',
            [$school],
        )->fetchAll('assoc');
        $previous = FinanceChain::GENESIS_HASH;
        foreach ($rows as $row) {
            if (!hash_equals($previous, (string)$row['previous_hash'])) {
                return 'Audit chain link mismatch at ' . $row['id'];
            }
            $key = $keys[(string)$row['key_id']] ?? null;
            if ($key === null) {
                return 'Audit verification key unavailable: ' . $row['key_id'];
            }
            $canonical = json_encode([
                'schoolId' => $school,
                'actorUserId' => $row['actor_user_id'],
                'actor' => $row['actor'],
                'role' => $row['actor_role'],
                'requestId' => $row['request_id'] ?? '',
                'ipAddress' => $row['ip_address'] ?? '',
                'action' => $row['action'],
                'outcome' => $row['outcome'],
                'entityType' => $row['entity_type'],
                'entityId' => $row['entity_id'],
                'summary' => $row['summary'],
                'reason' => $row['reason'],
                'before' => $row['before_value'] ? json_decode($row['before_value'], true) : null,
                'after' => $row['after_value'] ? json_decode($row['after_value'], true) : null,
                'at' => (string)$row['at'],
                'previousHash' => $previous,
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $hash = hash_hmac('sha256', (string)$canonical, $key);
            if (!hash_equals($hash, (string)$row['event_hash'])) {
                return 'Audit event seal mismatch at ' . $row['id'];
            }
            $previous = $hash;
        }

        return null;
    }

    /**
     * Create one lock and alert, or refresh the diagnostic on an open lock.
     *
     * @param \Cake\Datasource\ConnectionInterface $db Database connection.
     * @param string $school School identifier.
     * @param string $fault Verified integrity fault.
     * @return void
     */
    private function recordFault(ConnectionInterface $db, string $school, string $fault): void
    {
        $lock = $db->execute(
            'SELECT id,reason FROM ems_finance_integrity_locks '
            . 'WHERE school_id=? AND cleared_at IS NULL',
            [$school],
        )->fetch('assoc');
        if ($lock) {
            if ((string)$lock['reason'] !== $fault) {
                $db->execute(
                    'UPDATE ems_finance_integrity_locks SET reason=? WHERE id=?',
                    [$fault, $lock['id']],
                );
            }

            return;
        }

        $db->insert('ems_finance_integrity_locks', [
            'id' => Text::uuid(),
            'school_id' => $school,
            'reason' => $fault,
            'detected_at' => date('Y-m-d H:i:s'),
        ]);
        $db->insert('ems_notifications', [
            'id' => Text::uuid(),
            'school_id' => $school,
            'channel' => 'in_app',
            'kind' => 'security',
            'subject' => 'Finance writes locked',
            'body' => $fault,
            'audience_label' => 'School administrators and platform operators',
            'recipient_count' => 0,
            'sent_on' => date('Y-m-d'),
            'sent_by' => 'Finance integrity verifier',
            'created' => date('Y-m-d H:i:s'),
            'modified' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Clear a healthy lock only for an active administrator in that school.
     *
     * @param \Cake\Datasource\ConnectionInterface $db Database connection.
     * @param string $school School identifier.
     * @param string $administratorId Clearing administrator identifier.
     * @param \Cake\Console\ConsoleIo $io Console input and output.
     * @return bool True when no open lock remains.
     */
    private function clearVerifiedLock(
        ConnectionInterface $db,
        string $school,
        string $administratorId,
        ConsoleIo $io,
    ): bool {
        $administrator = $db->execute(
            'SELECT id,name,role,status FROM ems_users WHERE school_id=? AND id=? '
            . "AND role='administrator' AND status='active'",
            [$school, $administratorId],
        )->fetch('assoc');
        if (!$administrator) {
            $io->err($school . ': lock clearance requires an active administrator from this school.');

            return false;
        }
        $open = $db->execute(
            'SELECT COUNT(*) total FROM ems_finance_integrity_locks '
            . 'WHERE school_id=? AND cleared_at IS NULL',
            [$school],
        )->fetch('assoc');
        if ((int)$open['total'] === 0) {
            return true;
        }

        try {
            $db->transactional(function (ConnectionInterface $connection) use ($school, $administrator): void {
                $now = date('Y-m-d H:i:s');
                $connection->execute(
                    'UPDATE ems_finance_integrity_locks SET cleared_at=?,cleared_by_user_id=? '
                    . 'WHERE school_id=? AND cleared_at IS NULL',
                    [$now, $administrator['id'], $school],
                );
                $viewer = new Viewer(
                    $school,
                    (string)$administrator['id'],
                    (string)$administrator['role'],
                    (string)$administrator['name'],
                );
                $audit = new Audit(TableRegistry::getTableLocator(), 'finance-verify-cli', 'cli');
                $audit->log(
                    $viewer,
                    'finance.integrity_lock_cleared',
                    'school',
                    $school,
                    'The finance lock was cleared after a successful full verification.',
                    'Administrator approved clearance after successful verification.',
                );
            });
        } catch (RuntimeException $exception) {
            $io->err($school . ': ' . $exception->getMessage());

            return false;
        }

        return true;
    }
}
