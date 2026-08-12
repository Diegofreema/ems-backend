<?php
declare(strict_types=1);

namespace App\Test\TestCase\Ems;

use Cake\Datasource\ConnectionInterface;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Locator\LocatorInterface;
use Cake\TestSuite\TestCase;
use Cake\Utility\Text;

/**
 * Base for module-level (non-HTTP) EMS tests that still need the real schema.
 *
 * Scope, Tenant and DocumentPolicy make live ORM reads through their own
 * interface — tenant isolation and RBAC narrowing are only meaningful against
 * real tables — so they are exercised against the migrated `test` database
 * rather than mocked. This is the read-model sibling of EmsIntegrationTestCase:
 * the same seed-a-tenant-and-clean-up independence discipline, but without the
 * HTTP integration trait, since a module is tested through its PHP interface.
 *
 * Seeding and cleanup go through the explicit `test` connection, and the modules
 * under test read through the default table locator (aliased to `test` for the
 * test run) — the same two-connection arrangement the integration base uses.
 * No DB-level foreign keys exist on the EMS schema (isolation is enforced in the
 * app layer, which is exactly what these tests verify), so seed order is free.
 */
abstract class EmsDbTestCase extends TestCase
{
    protected string $schoolId = '';
    protected string $adminId = '';
    protected ConnectionInterface $db;
    protected LocatorInterface $locator;

    /** Tables these tests seed, cleared before and after each test. */
    protected const CLEANUP_TABLES = [
        'ems_finance_adjustment_payouts',
        'ems_receipts',
        'ems_finance_ledger_events',
        'ems_finance_decisions',
        'ems_finance_adjustment_requests',
        'ems_finance_evidence',
        'ems_payment_submissions',
        'ems_refunds',
        'ems_payments',
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
        'ems_fee_awards',
        'ems_sequences',
        'ems_subject_allocations',
        'ems_timetable_slots',
        'ems_subjects',
        'ems_class_groups',
        'ems_guardians',
        'ems_enrolments',
        'ems_students',
        'ems_users',
        'ems_schools',
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = ConnectionManager::get('test');
        $this->locator = $this->getTableLocator();
        $this->clearTables();

        $this->schoolId = $this->seedSchool('Test School');
        $this->adminId = $this->seedUser($this->schoolId, 'administrator', ['name' => 'Ada Admin']);
    }

    protected function tearDown(): void
    {
        $this->clearTables();
        parent::tearDown();
    }

    // --- seed helpers -------------------------------------------------------

    protected function seedSchool(string $name = 'Other School', ?string $shortName = null): string
    {
        $id = Text::uuid();
        $row = [
            'id' => $id,
            'slug' => 's-' . substr($id, 0, 8),
            'name' => $name,
        ];
        // short_name drives Fees::prefix(); set it only when a test cares.
        if ($shortName !== null) {
            $row['short_name'] = $shortName;
        }
        $this->insertRow('ems_schools', $row);

        return $id;
    }

    /**
     * Seed an active user. `link_teacher_id`, `link_student_id` and
     * `link_student_ids` (an array, stored as JSON) may be supplied via $over to
     * arrange a teacher / student / parent principal for Scope.
     */
    protected function seedUser(string $schoolId, string $role, array $over = []): string
    {
        $id = $over['id'] ?? Text::uuid();
        $row = [
            'id' => $id,
            'school_id' => $schoolId,
            'name' => $over['name'] ?? ucfirst($role),
            'email' => 'u-' . substr($id, 0, 12) . '@seed.test',
            'role' => $role,
            'status' => 'active',
            'added_on' => $this->now(),
            'link_teacher_id' => $over['link_teacher_id'] ?? null,
            'link_student_id' => $over['link_student_id'] ?? null,
            'link_student_ids' => isset($over['link_student_ids'])
                ? json_encode(array_values($over['link_student_ids']))
                : null,
        ];

        $this->insertRow('ems_users', $row);

        return $id;
    }

    protected function seedStudent(string $schoolId, array $over = []): string
    {
        $id = $over['id'] ?? Text::uuid();
        $this->insertRow('ems_students', [
            'id' => $id,
            'school_id' => $schoolId,
            'admission_number' => $over['admission_number'] ?? 'ADM-' . substr($id, 0, 6),
            'first_name' => $over['first_name'] ?? 'Kid',
            'last_name' => $over['last_name'] ?? 'Test',
            'date_of_birth' => '2015-01-01',
            'gender' => 'male',
            'class_group' => $over['class_group'] ?? 'JSS 1A',
            'status' => $over['status'] ?? 'active',
            'enrolled_on' => '2025-01-01',
        ]);

        return $id;
    }

    protected function seedClassGroup(string $schoolId, array $over = []): string
    {
        $id = $over['id'] ?? Text::uuid();
        $this->insertRow('ems_class_groups', [
            'id' => $id,
            'school_id' => $schoolId,
            'name' => $over['name'] ?? 'JSS 1A',
            'level' => $over['level'] ?? 'JSS 1',
            'form_teacher_id' => $over['form_teacher_id'] ?? null,
        ]);

        return $id;
    }

    protected function seedSubject(string $schoolId, ?string $name = null): string
    {
        $id = Text::uuid();
        $this->insertRow('ems_subjects', [
            'id' => $id,
            'school_id' => $schoolId,
            // ems_subjects is unique on (school_id, name); default to a distinct
            // name per call so a test can seed several subjects for one school.
            'name' => $name ?? 'Subject ' . substr($id, 0, 8),
            'active' => 1,
        ]);

        return $id;
    }

    protected function seedAllocation(string $schoolId, string $classGroupId, string $teacherId): void
    {
        $this->insertRow('ems_subject_allocations', [
            'id' => Text::uuid(),
            'school_id' => $schoolId,
            'class_group_id' => $classGroupId,
            'teacher_id' => $teacherId,
            'subject_id' => $this->seedSubject($schoolId),
        ]);
    }

    protected function seedTimetableSlot(string $schoolId, string $classGroupId, string $teacherId): void
    {
        $this->insertRow('ems_timetable_slots', [
            'id' => Text::uuid(),
            'school_id' => $schoolId,
            'class_group_id' => $classGroupId,
            'day' => 'mon',
            'period' => 1,
            'teacher_id' => $teacherId,
            'subject_id' => $this->seedSubject($schoolId),
        ]);
    }

    // --- fees seeders -------------------------------------------------------

    /**
     * Seed an invoice. `line_items` (NOT NULL) and `instalments` are JSON
     * columns — pass PHP arrays and they are encoded here; the ORM decodes them
     * back on read. `total` defaults to match the default single Tuition line.
     */
    protected function seedInvoice(string $schoolId, array $over = []): string
    {
        $id = $over['id'] ?? Text::uuid();
        $studentId = $over['student_id'] ?? Text::uuid();
        $exists = (int)$this->db->selectQuery()->select(['n' => 'COUNT(*)'])->from('ems_students')->where(['id' => $studentId, 'school_id' => $schoolId])->execute()->fetch('assoc')['n'];
        if ($exists === 0) {
            $this->seedStudent($schoolId, ['id' => $studentId, 'first_name' => 'Kid', 'last_name' => 'Test', 'class_group' => $over['class_group'] ?? 'JSS 1A']);
        }
        $this->insertRow('ems_invoices', [
            'id' => $id,
            'school_id' => $schoolId,
            'invoice_number' => $over['invoice_number'] ?? 'INV-' . substr($id, 0, 6),
            'student_id' => $studentId,
            'student_name' => $over['student_name'] ?? 'Kid Test',
            'class_group' => $over['class_group'] ?? 'JSS 1A',
            'session' => $over['session'] ?? '2025/2026',
            'term' => $over['term'] ?? 'First',
            'issued_on' => $over['issued_on'] ?? '2025-09-01',
            'due_date' => $over['due_date'] ?? '2025-10-01',
            'line_items' => json_encode($over['line_items'] ?? [['name' => 'Tuition', 'amount' => 100000, 'kind' => 'charge']]),
            'total' => $over['total'] ?? 100000,
            'status' => $over['status'] ?? 'issued',
            'instalments' => isset($over['instalments']) ? json_encode($over['instalments']) : null,
        ]);

        return $id;
    }

    protected function seedPayment(string $schoolId, string $invoiceId, string $studentId, array $over = []): string
    {
        $invoiceRow = $this->db->execute('SELECT student_id FROM ems_invoices WHERE school_id=? AND id=?', [$schoolId, $invoiceId])->fetch('assoc');
        $studentId = (string)$invoiceRow['student_id'];
        $id = $over['id'] ?? Text::uuid();
        $this->insertRow('ems_payments', [
            'id' => $id,
            'school_id' => $schoolId,
            'invoice_id' => $invoiceId,
            'student_id' => $studentId,
            'receipt_number' => $over['receipt_number'] ?? 'RCP-' . substr($id, 0, 6),
            'amount' => $over['amount'] ?? 0,
            'method' => $over['method'] ?? 'cash',
            'paid_on' => $over['paid_on'] ?? '2025-09-15',
            'state' => $over['state'] ?? 'completed',
            'channel' => $over['channel'] ?? null,
            'failed_on' => $over['failed_on'] ?? null,
        ]);
        $state = $over['state'] ?? 'completed';
        if (in_array($state, ['completed', 'reversed'], true)) {
            $this->seedLedgerEvent($schoolId, $invoiceId, $studentId, $id, 'payment', (int)($over['amount'] ?? 0));
            if ($state === 'reversed') {
                $this->seedLedgerEvent($schoolId, $invoiceId, $studentId, $id, 'reversal', -(int)($over['amount'] ?? 0));
            }
        }

        return $id;
    }

    protected function seedRefund(string $schoolId, string $paymentId, string $invoiceId, string $studentId, array $over = []): string
    {
        $paymentRow = $this->db->execute('SELECT invoice_id,student_id FROM ems_payments WHERE school_id=? AND id=?', [$schoolId, $paymentId])->fetch('assoc');
        $invoiceId = (string)$paymentRow['invoice_id'];
        $studentId = (string)$paymentRow['student_id'];
        $id = $over['id'] ?? Text::uuid();
        $this->insertRow('ems_refunds', [
            'id' => $id,
            'school_id' => $schoolId,
            'payment_id' => $paymentId,
            'invoice_id' => $invoiceId,
            'student_id' => $studentId,
            'amount' => $over['amount'] ?? 0,
            'reason' => $over['reason'] ?? 'Overpayment',
            'status' => $over['status'] ?? 'pending',
            'requested_by' => $over['requested_by'] ?? 'Bursar',
            'requested_on' => $over['requested_on'] ?? '2025-09-20',
        ]);
        if (($over['status'] ?? 'pending') === 'processed') {
            $this->seedLedgerEvent($schoolId, $invoiceId, $studentId, $paymentId, 'refund', -(int)($over['amount'] ?? 0));
        }

        return $id;
    }

    private function seedLedgerEvent(string $schoolId, string $invoiceId, string $studentId, string $paymentId, string $type, int $amount): void
    {
        $this->db->insert('ems_finance_ledger_events', [
            'id' => Text::uuid(), 'school_id' => $schoolId, 'invoice_id' => $invoiceId,
            'student_id' => $studentId, 'payment_id' => $paymentId, 'event_type' => $type,
            'amount' => $amount, 'provenance' => 'test', 'key_id' => 'test-key-1',
            'previous_hash' => str_repeat('0', 64), 'event_hash' => hash('sha256', Text::uuid()),
            'occurred_at' => $this->now(),
        ]);
    }

    /**
     * Seed a fee award. `basis` is 'percentage' or 'amount' (kobo); `scope` is
     * 'level' (set `level`) or 'student' (set `student_id`); `term` is 'all' or
     * a specific term name.
     */
    protected function seedFeeAward(string $schoolId, array $over = []): string
    {
        $id = $over['id'] ?? Text::uuid();
        $this->insertRow('ems_fee_awards', [
            'id' => $id,
            'school_id' => $schoolId,
            'name' => $over['name'] ?? 'Award',
            'kind' => $over['kind'] ?? 'discount',
            'basis' => $over['basis'] ?? 'percentage',
            'value' => $over['value'] ?? 10,
            'applies_to_item' => $over['applies_to_item'] ?? 'all',
            'scope' => $over['scope'] ?? 'level',
            'student_id' => $over['student_id'] ?? null,
            'level' => $over['level'] ?? null,
            'session' => $over['session'] ?? '2025/2026',
            'term' => $over['term'] ?? 'all',
            'status' => $over['status'] ?? 'active',
            'awarded_by' => $over['awarded_by'] ?? 'Admin',
            'awarded_on' => $over['awarded_on'] ?? '2025-08-01',
        ]);

        return $id;
    }

    // --- low-level ----------------------------------------------------------

    protected function insertRow(string $table, array $data): void
    {
        $this->db->insert($table, $data + ['created' => $this->now(), 'modified' => $this->now()]);
    }

    protected function now(): string
    {
        return date('Y-m-d H:i:s');
    }

    private function clearTables(): void
    {
        $this->db->execute('SET FOREIGN_KEY_CHECKS = 0');
        foreach (static::CLEANUP_TABLES as $table) {
            $this->db->execute('DELETE FROM ' . $table);
        }
        $this->db->execute('SET FOREIGN_KEY_CHECKS = 1');
    }
}
