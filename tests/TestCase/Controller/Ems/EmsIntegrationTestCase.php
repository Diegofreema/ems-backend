<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Ems;

use App\Api\Jwt;
use Cake\Datasource\ConnectionInterface;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Cake\Utility\Text;

/**
 * Base for EMS contract API integration tests — the first PHPUnit coverage of
 * the /api/ems surface. It provides:
 *
 *  - a seeded single tenant (one school, one administrator) created per test
 *    against the `test` connection, torn down after each test;
 *  - authAs()/token() helpers that mint a valid type==='ems' Bearer JWT so a
 *    test can call /api/ems/schools/{schoolId}/... as any role;
 *  - insertRow()/rowExists()/rowCount() thin DB helpers for arranging state and
 *    asserting on it directly (e.g. that a row was archived, not destroyed).
 *
 * No class-based fixtures: the `test` database carries the full migrated EMS
 * schema, and each test seeds only the rows it needs, deleting them again in
 * tearDown so tests stay independent without truncating the whole schema.
 */
abstract class EmsIntegrationTestCase extends TestCase
{
    use IntegrationTestTrait;

    protected string $schoolId = '';
    protected string $adminId = '';
    protected ConnectionInterface $db;

    /** Secure finance children must be cleared even when a test adds its own cleanup list. */
    private const FINANCE_CLEANUP_TABLES = [
        // Bulk-invoicing children first: batch rows foreign-key invoices/students.
        'ems_invoice_batch_rows',
        'ems_invoice_batches',
        'ems_fee_reminders',
        'ems_finance_adjustment_payouts',
        'ems_receipts',
        'ems_finance_ledger_events',
        'ems_payments',
        'ems_finance_decisions',
        'ems_finance_adjustment_requests',
        'ems_finance_evidence',
        'ems_document_objects',
        'ems_payment_submissions',
        'ems_bank_statement_rows',
        'ems_bank_statement_batches',
        'ems_cash_batches',
        'ems_invoice_events',
        'ems_invoice_change_requests',
        'ems_finance_idempotency',
        'ems_finance_integrity_locks',
        'ems_audit_events',
        'ems_invoices',
        'ems_fee_plan_versions',
    ];

    /**
     * Tables this base (and its subclasses) seed, cleared in FK-safe order
     * before and after each test. Child-most tables first.
     */
    protected const CLEANUP_TABLES = [
        'ems_finance_adjustment_payouts',
        'ems_receipts',
        'ems_finance_ledger_events',
        'ems_payments',
        'ems_finance_decisions',
        'ems_finance_adjustment_requests',
        'ems_finance_evidence',
        'ems_document_objects',
        'ems_payment_submissions',
        'ems_bank_statement_rows',
        'ems_bank_statement_batches',
        'ems_cash_batches',
        'ems_invoice_events',
        'ems_invoice_change_requests',
        'ems_finance_idempotency',
        'ems_finance_integrity_locks',
        'ems_audit_events',
        'ems_invoices',
        'ems_fee_plan_versions',
        'ems_guardians',
        'ems_enrolments',
        'ems_students',
        'ems_refresh_tokens',
        'ems_password_resets',
        'ems_email_verifications',
        'ems_users',
        'ems_schools',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = ConnectionManager::get('test');
        $this->clearTables();

        $this->schoolId = Text::uuid();
        $this->insertRow('ems_schools', [
            'id' => $this->schoolId,
            'slug' => 'test-' . substr($this->schoolId, 0, 8),
            'name' => 'Test School',
        ]);

        $this->adminId = Text::uuid();
        $this->insertRow('ems_users', [
            'id' => $this->adminId,
            'school_id' => $this->schoolId,
            'name' => 'Ada Admin',
            'email' => 'ada@test.school',
            'role' => 'administrator',
            'status' => 'active',
            'added_on' => $this->now(),
            // Mirrors production: every pre-verification account was
            // backfilled as verified, and invited accounts are stamped at
            // redemption — only fresh self-registrations start unverified.
            'email_verified_at' => $this->now(),
        ]);
    }

    protected function tearDown(): void
    {
        $this->clearTables();
        parent::tearDown();
    }

    /** Authenticate every subsequent request as the seeded administrator. */
    protected function authAsAdmin(): void
    {
        $this->authAs('administrator', $this->adminId, 'Ada Admin');
    }

    /**
     * Attach a valid EMS Bearer token for the given role to the next request.
     *
     * Authorization now reads the LIVE ems_users row (App\Ems\ViewerResolver),
     * so a token whose `sub` has no active row is refused before the action
     * runs. We therefore ensure an active user row exists for this principal —
     * mirroring production, where every real token corresponds to a real row.
     */
    protected function authAs(string $role, string $userId, string $name): void
    {
        $this->ensureUser($role, $userId, $name);
        $this->configRequest([
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token($role, $userId, $name),
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * Seed an active ems_users row for a principal if one is not already
     * present (the seeded administrator already has its row). Lets a test
     * authenticate as any role and have the live resolver find a real account.
     */
    protected function ensureUser(string $role, string $userId, string $name): void
    {
        if ($this->rowExists('ems_users', ['id' => $userId])) {
            return;
        }
        $this->insertRow('ems_users', [
            'id' => $userId,
            'school_id' => $this->schoolId,
            'name' => $name,
            'email' => 'u-' . substr($userId, 0, 12) . '@seed.test',
            'role' => $role,
            'status' => 'active',
            'added_on' => $this->now(),
            'email_verified_at' => $this->now(),
        ]);
    }

    /**
     * Mint a contract-valid access token. Carries ONLY `sub` + `type`, exactly
     * as the real token does now (token-slimming): authorization reads the live
     * ems_users row via ViewerResolver, so the token is trusted for identity
     * alone. The `$role`/`$name` args drive the seeded row (ensureUser), which is
     * what actually decides the request — not the token.
     */
    protected function token(string $role, string $userId, string $name): string
    {
        return Jwt::encode(
            [
                'sub' => $userId,
                'type' => 'ems',
            ],
            3600,
            time(),
        );
    }

    /** The tenant path prefix every scoped endpoint hangs off. */
    protected function schoolPath(string $suffix = ''): string
    {
        return '/api/ems/schools/' . $this->schoolId . $suffix;
    }

    /** The decoded JSON body of the last response. */
    protected function responseJson(): array
    {
        return (array)json_decode((string)$this->_response->getBody(), true);
    }

    // --- DB helpers ---------------------------------------------------------

    protected function insertRow(string $table, array $data): void
    {
        // insert() infers columns and types from the data map.
        $this->db->insert($table, $data + ['created' => $this->now(), 'modified' => $this->now()]);
    }

    protected function rowExists(string $table, array $conditions): bool
    {
        return $this->rowCount($table, $conditions) > 0;
    }

    protected function rowCount(string $table, array $conditions): int
    {
        $query = $this->db->selectQuery()
            ->select(['n' => 'COUNT(*)'])
            ->from($table)
            ->where($conditions);

        return (int)$query->execute()->fetch('assoc')['n'];
    }

    protected function now(): string
    {
        return date('Y-m-d H:i:s');
    }

    private function clearTables(): void
    {
        foreach (array_unique(array_merge(self::FINANCE_CLEANUP_TABLES, static::CLEANUP_TABLES)) as $table) {
            $this->db->execute('DELETE FROM ' . $table);
        }
    }
}
