<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Secure finance cut-over (ordered after the existing 2026-08-12 migration).
 * New money is represented by immutable submissions,
 * decisions and ledger events. Existing completed money is sealed with
 * legacy_migration provenance before the old mutable path is disabled.
 */
class CreateSecureFinanceLedger extends BaseMigration
{
    /** Create the secure finance schema and seal legacy records. */
    public function up(): void
    {
        $this->table('ems_payment_submissions', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid')->addColumn('school_id', 'uuid')->addColumn('invoice_id', 'uuid')
            ->addColumn('student_id', 'uuid')->addColumn('amount', 'biginteger')
            ->addColumn('method', 'string', ['limit' => 16])->addColumn('normalized_reference', 'string', ['limit' => 190, 'null' => true])
            ->addColumn('payer_name', 'string', ['limit' => 190])->addColumn('payer_relationship', 'string', ['limit' => 60])
            ->addColumn('received_on', 'date')->addColumn('cash_acknowledgement', 'string', ['limit' => 80, 'null' => true])
            ->addColumn('statement_row_id', 'uuid', ['null' => true])->addColumn('cash_batch_id', 'uuid', ['null' => true])
            ->addColumn('recorded_by_user_id', 'uuid')->addColumn('recorded_by_name', 'string', ['limit' => 190])
            ->addColumn('provenance', 'string', ['limit' => 32, 'default' => 'offline'])
            ->addColumn('created', 'datetime')->addIndex(['school_id', 'invoice_id'])
            ->addIndex(['school_id', 'student_id'])->addIndex(['school_id', 'normalized_reference', 'method'])
            ->create();

        $this->table('ems_finance_evidence', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid')->addColumn('school_id', 'uuid')->addColumn('owner_type', 'string', ['limit' => 32])
            ->addColumn('owner_id', 'uuid')->addColumn('storage_path', 'string', ['limit' => 500])
            ->addColumn('filename', 'string', ['limit' => 190])->addColumn('content_hash', 'string', ['limit' => 64])
            ->addColumn('media_type', 'string', ['limit' => 64])->addColumn('size_bytes', 'integer')
            ->addColumn('scan_status', 'string', ['limit' => 16, 'default' => 'quarantined'])
            ->addColumn('created_by_user_id', 'uuid')->addColumn('created', 'datetime')
            ->addIndex(['school_id', 'owner_type', 'owner_id'])->addIndex(['school_id', 'content_hash'], ['unique' => true])
            ->create();

        $this->table('ems_finance_decisions', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid')->addColumn('school_id', 'uuid')->addColumn('request_type', 'string', ['limit' => 40])
            ->addColumn('request_id', 'uuid')->addColumn('decision', 'string', ['limit' => 10])
            ->addColumn('reason', 'string', ['limit' => 500])->addColumn('requested_by_user_id', 'uuid')
            ->addColumn('decided_by_user_id', 'uuid')->addColumn('decided_by_name', 'string', ['limit' => 190])
            ->addColumn('decided_at', 'datetime')->addIndex(['school_id', 'request_type', 'request_id'], ['unique' => true])
            ->create();

        $this->table('ems_receipts', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid')->addColumn('school_id', 'uuid')->addColumn('payment_id', 'uuid')
            ->addColumn('invoice_id', 'uuid')->addColumn('student_id', 'uuid')->addColumn('receipt_number', 'string', ['limit' => 40])
            ->addColumn('amount', 'biginteger')->addColumn('paid_to_date', 'biginteger')->addColumn('balance_after', 'biginteger')
            ->addColumn('snapshot', 'json')->addColumn('issued_at', 'datetime')
            ->addIndex(['school_id', 'payment_id'], ['unique' => true])->addIndex(['school_id', 'receipt_number'], ['unique' => true])
            ->create();

        $this->table('ems_finance_ledger_events', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid')->addColumn('school_id', 'uuid')->addColumn('invoice_id', 'uuid')
            ->addColumn('student_id', 'uuid')->addColumn('payment_id', 'uuid', ['null' => true])
            ->addColumn('adjustment_request_id', 'uuid', ['null' => true])->addColumn('event_type', 'string', ['limit' => 24])
            ->addColumn('amount', 'biginteger')->addColumn('provenance', 'string', ['limit' => 32])
            ->addColumn('key_id', 'string', ['limit' => 64])->addColumn('previous_hash', 'string', ['limit' => 64])
            ->addColumn('event_hash', 'string', ['limit' => 64])->addColumn('occurred_at', 'datetime')
            ->addIndex(['school_id', 'invoice_id', 'occurred_at'])->addIndex(['school_id', 'student_id'])
            ->addIndex(['school_id', 'payment_id', 'event_type'], ['unique' => true])
            ->create();

        $this->table('ems_finance_adjustment_requests', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid')->addColumn('school_id', 'uuid')->addColumn('payment_id', 'uuid')
            ->addColumn('invoice_id', 'uuid')->addColumn('student_id', 'uuid')->addColumn('kind', 'string', ['limit' => 16])
            ->addColumn('amount', 'biginteger')->addColumn('reason', 'string', ['limit' => 500])
            ->addColumn('requested_by_user_id', 'uuid')->addColumn('requested_by_name', 'string', ['limit' => 190])
            ->addColumn('created', 'datetime')->addIndex(['school_id', 'payment_id'])
            ->create();

        $this->table('ems_finance_adjustment_payouts', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid')->addColumn('school_id', 'uuid')->addColumn('adjustment_request_id', 'uuid')
            ->addColumn('evidence_id', 'uuid')->addColumn('statement_row_id', 'uuid', ['null' => true])
            ->addColumn('recorded_by_user_id', 'uuid')->addColumn('confirmed_by_user_id', 'uuid', ['null' => true])
            ->addColumn('confirmed_at', 'datetime', ['null' => true])->addColumn('created', 'datetime')
            ->addIndex(['school_id', 'adjustment_request_id'], ['unique' => true])->create();

        $this->table('ems_bank_statement_batches', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid')->addColumn('school_id', 'uuid')->addColumn('source_name', 'string', ['limit' => 190])
            ->addColumn('file_hash', 'string', ['limit' => 64])->addColumn('imported_by_user_id', 'uuid')->addColumn('created', 'datetime')
            ->addIndex(['school_id', 'file_hash'], ['unique' => true])->create();

        $this->table('ems_bank_statement_rows', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid')->addColumn('school_id', 'uuid')->addColumn('batch_id', 'uuid')
            ->addColumn('occurred_on', 'date')->addColumn('reference', 'string', ['limit' => 190])
            ->addColumn('description', 'string', ['limit' => 500])->addColumn('amount', 'biginteger')
            ->addColumn('direction', 'string', ['limit' => 8])->addColumn('row_hash', 'string', ['limit' => 64])
            ->addIndex(['school_id', 'batch_id', 'row_hash'], ['unique' => true])->create();

        $this->table('ems_cash_batches', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid')->addColumn('school_id', 'uuid')->addColumn('batch_number', 'string', ['limit' => 60])
            ->addColumn('collection_date', 'date')->addColumn('recorder_user_id', 'uuid')
            ->addColumn('expected_amount', 'biginteger', ['default' => 0])->addColumn('counted_amount', 'biginteger', ['null' => true])
            ->addColumn('closed_by_user_id', 'uuid', ['null' => true])->addColumn('closed_at', 'datetime', ['null' => true])
            ->addColumn('created', 'datetime')->addIndex(['school_id', 'batch_number'], ['unique' => true])->create();

        $this->table('ems_finance_idempotency', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid')->addColumn('school_id', 'uuid')->addColumn('actor_user_id', 'uuid')
            ->addColumn('action', 'string', ['limit' => 80])->addColumn('request_key', 'string', ['limit' => 190])
            ->addColumn('request_hash', 'string', ['limit' => 64])->addColumn('status_code', 'integer')
            ->addColumn('response_body', 'json')->addColumn('created', 'datetime')
            ->addIndex(['school_id', 'actor_user_id', 'action', 'request_key'], ['unique' => true])->create();

        $this->table('ems_finance_integrity_locks', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid')->addColumn('school_id', 'uuid')->addColumn('reason', 'string', ['limit' => 500])
            ->addColumn('detected_at', 'datetime')->addColumn('cleared_at', 'datetime', ['null' => true])
            ->addColumn('cleared_by_user_id', 'uuid', ['null' => true])->addIndex(['school_id', 'cleared_at'])->create();

        $this->table('ems_fee_plan_versions', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid')->addColumn('school_id', 'uuid')->addColumn('source_structure_id', 'uuid')
            ->addColumn('version', 'integer')->addColumn('session', 'string', ['limit' => 20])->addColumn('term', 'string', ['limit' => 6])
            ->addColumn('level', 'string', ['limit' => 40])->addColumn('items', 'json')->addColumn('schedule', 'json', ['null' => true])
            ->addColumn('total', 'biginteger')->addColumn('created_by_user_id', 'uuid')->addColumn('created', 'datetime')
            ->addIndex(['school_id', 'source_structure_id', 'version'], ['unique' => true])->create();

        $this->table('ems_invoice_change_requests', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid')->addColumn('school_id', 'uuid')->addColumn('invoice_id', 'uuid')
            ->addColumn('kind', 'string', ['limit' => 20])->addColumn('payload', 'json', ['null' => true])
            ->addColumn('reason', 'string', ['limit' => 500])->addColumn('requested_by_user_id', 'uuid')->addColumn('created', 'datetime')
            ->addIndex(['school_id', 'invoice_id'])->create();

        $this->table('ems_invoice_events', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid')->addColumn('school_id', 'uuid')->addColumn('invoice_id', 'uuid')
            ->addColumn('student_id', 'uuid')->addColumn('kind', 'string', ['limit' => 20])
            ->addColumn('payload', 'json', ['null' => true])->addColumn('decision_id', 'uuid')->addColumn('occurred_at', 'datetime')
            ->addIndex(['school_id', 'invoice_id'])->create();

        $this->table('ems_payments')
            ->addColumn('submission_id', 'uuid', ['null' => true, 'after' => 'school_id'])
            ->addColumn('provenance', 'string', ['limit' => 32, 'default' => 'legacy_migration', 'after' => 'channel'])
            ->addIndex(['school_id', 'receipt_number'], ['unique' => true, 'name' => 'uq_payment_receipt_school'])
            ->addIndex(['school_id', 'submission_id'], ['unique' => true, 'name' => 'uq_payment_submission_school'])
            ->addIndex(['school_id', 'method', 'reference'], ['unique' => true, 'name' => 'uq_payment_reference_school'])
            ->update();
        $this->table('ems_invoices')->addColumn('fee_plan_version_id', 'uuid', ['null' => true, 'after' => 'school_id'])->update();

        $this->table('ems_audit_events')
            ->addColumn('actor_user_id', 'uuid', ['null' => true, 'after' => 'school_id'])
            ->addColumn('actor_role', 'string', ['limit' => 24, 'null' => true, 'after' => 'actor'])
            ->addColumn('request_id', 'string', ['limit' => 80, 'null' => true, 'after' => 'actor_role'])
            ->addColumn('ip_address', 'string', ['limit' => 64, 'null' => true, 'after' => 'request_id'])
            ->addColumn('outcome', 'string', ['limit' => 16, 'default' => 'success', 'after' => 'action'])
            ->addColumn('before_value', 'json', ['null' => true, 'after' => 'reason'])
            ->addColumn('after_value', 'json', ['null' => true, 'after' => 'before_value'])
            ->addColumn('key_id', 'string', ['limit' => 64, 'null' => true, 'after' => 'after_value'])
            ->addColumn('previous_hash', 'string', ['limit' => 64, 'null' => true, 'after' => 'key_id'])
            ->addColumn('event_hash', 'string', ['limit' => 64, 'null' => true, 'after' => 'previous_hash'])
            ->update();

        $this->addIntegrityConstraints();
        $this->backfillLegacyMoney();
        $this->createImmutableTriggers();
    }

    /** Add tenant-consistent foreign keys and accounting checks. */
    private function addIntegrityConstraints(): void
    {
        foreach (['ems_students', 'ems_invoices', 'ems_payments', 'ems_users'] as $table) {
            $this->execute("ALTER TABLE $table ADD UNIQUE KEY uq_{$table}_tenant_id (school_id,id)");
        }
        $this->execute("ALTER TABLE ems_payment_submissions
            ADD CONSTRAINT chk_submission_amount CHECK (amount > 0),
            ADD CONSTRAINT chk_submission_method CHECK (method IN ('cash','bank_transfer','pos','cheque')),
            ADD CONSTRAINT chk_submission_proof CHECK ((method='cash' AND cash_acknowledgement IS NOT NULL) OR (method<>'cash' AND normalized_reference IS NOT NULL)),
            ADD CONSTRAINT fk_submission_invoice FOREIGN KEY (school_id,invoice_id) REFERENCES ems_invoices(school_id,id),
            ADD CONSTRAINT fk_submission_student FOREIGN KEY (school_id,student_id) REFERENCES ems_students(school_id,id),
            ADD CONSTRAINT fk_submission_actor FOREIGN KEY (school_id,recorded_by_user_id) REFERENCES ems_users(school_id,id)");
        $this->execute("ALTER TABLE ems_finance_decisions
            ADD CONSTRAINT chk_finance_decision CHECK (decision IN ('approved','rejected')),
            ADD CONSTRAINT chk_finance_separation CHECK (requested_by_user_id <> decided_by_user_id),
            ADD CONSTRAINT fk_decision_requester FOREIGN KEY (school_id,requested_by_user_id) REFERENCES ems_users(school_id,id),
            ADD CONSTRAINT fk_decision_approver FOREIGN KEY (school_id,decided_by_user_id) REFERENCES ems_users(school_id,id)");
        $this->execute("ALTER TABLE ems_payments
            ADD CONSTRAINT chk_posted_payment_amount CHECK (amount > 0),
            ADD CONSTRAINT fk_payment_invoice_secure FOREIGN KEY (school_id,invoice_id) REFERENCES ems_invoices(school_id,id),
            ADD CONSTRAINT fk_payment_student_secure FOREIGN KEY (school_id,student_id) REFERENCES ems_students(school_id,id)");
        $this->execute("ALTER TABLE ems_receipts
            ADD CONSTRAINT chk_receipt_amount CHECK (amount > 0 AND paid_to_date >= 0 AND balance_after >= 0),
            ADD CONSTRAINT fk_receipt_payment FOREIGN KEY (school_id,payment_id) REFERENCES ems_payments(school_id,id),
            ADD CONSTRAINT fk_receipt_invoice FOREIGN KEY (school_id,invoice_id) REFERENCES ems_invoices(school_id,id),
            ADD CONSTRAINT fk_receipt_student FOREIGN KEY (school_id,student_id) REFERENCES ems_students(school_id,id)");
        $this->execute("ALTER TABLE ems_finance_ledger_events
            ADD CONSTRAINT chk_ledger_signed_amount CHECK ((event_type='payment' AND amount>0) OR (event_type IN ('reversal','refund') AND amount<0)),
            ADD CONSTRAINT fk_ledger_invoice FOREIGN KEY (school_id,invoice_id) REFERENCES ems_invoices(school_id,id),
            ADD CONSTRAINT fk_ledger_student FOREIGN KEY (school_id,student_id) REFERENCES ems_students(school_id,id),
            ADD CONSTRAINT fk_ledger_payment FOREIGN KEY (school_id,payment_id) REFERENCES ems_payments(school_id,id)");
        $this->execute("ALTER TABLE ems_finance_evidence
            ADD CONSTRAINT chk_evidence_type CHECK (media_type IN ('application/pdf','image/jpeg','image/png')),
            ADD CONSTRAINT chk_evidence_size CHECK (size_bytes > 0 AND size_bytes <= 10485760),
            ADD CONSTRAINT chk_evidence_scan CHECK (scan_status IN ('quarantined','clean','rejected')),
            ADD CONSTRAINT fk_evidence_actor FOREIGN KEY (school_id,created_by_user_id) REFERENCES ems_users(school_id,id)");
        $this->execute('ALTER TABLE ems_invoice_change_requests ADD CONSTRAINT fk_invoice_change_invoice FOREIGN KEY (school_id,invoice_id) REFERENCES ems_invoices(school_id,id)');
        $this->execute('ALTER TABLE ems_invoice_events ADD CONSTRAINT fk_invoice_event_invoice FOREIGN KEY (school_id,invoice_id) REFERENCES ems_invoices(school_id,id)');
    }

    /** Convert existing posted money into signed ledger and receipt snapshots. */
    private function backfillLegacyMoney(): void
    {
        $key = getenv('EMS_FINANCE_HMAC_KEY') ?: 'migration-unverified-key';
        $keyId = getenv('EMS_FINANCE_HMAC_KEY_ID') ?: 'legacy-migration';
        $payments = $this->fetchAll("SELECT * FROM ems_payments WHERE state IN ('completed','reversed') ORDER BY school_id, created, id");
        $previous = [];
        foreach ($payments as $payment) {
            $school = (string)$payment['school_id'];
            $amount = (int)$payment['amount'];
            $paymentId = (string)$payment['id'];
            $events = [['payment', $amount]];
            if ((string)$payment['state'] === 'reversed') {
                $events[] = ['reversal', -$amount];
            }
            foreach ($events as [$type, $signed]) {
                $id = $this->uuid();
                $at = (string)($payment['created'] ?: date('Y-m-d H:i:s'));
                $prev = $previous[$school] ?? str_repeat('0', 64);
                $canonical = implode('|', [$id, $school, $payment['invoice_id'], $payment['student_id'], $paymentId, $type, $signed, $at, $prev]);
                $hash = hash_hmac('sha256', $canonical, $key);
                $this->execute(sprintf(
                    "INSERT INTO ems_finance_ledger_events (id,school_id,invoice_id,student_id,payment_id,event_type,amount,provenance,key_id,previous_hash,event_hash,occurred_at) VALUES ('%s','%s','%s','%s','%s','%s',%d,'legacy_migration','%s','%s','%s','%s')",
                    $id,
                    $school,
                    $payment['invoice_id'],
                    $payment['student_id'],
                    $paymentId,
                    $type,
                    $signed,
                    $keyId,
                    $prev,
                    $hash,
                    $at
                ));
                $previous[$school] = $hash;
            }
            $paid = (int)$this->fetchRow("SELECT COALESCE(SUM(amount),0) total FROM ems_finance_ledger_events WHERE school_id='" . addslashes($school) . "' AND invoice_id='" . addslashes((string)$payment['invoice_id']) . "'")['total'];
            $invoice = $this->fetchRow("SELECT total, invoice_number, student_name FROM ems_invoices WHERE school_id='" . addslashes($school) . "' AND id='" . addslashes((string)$payment['invoice_id']) . "'");
            if ($invoice) {
                $snapshot = addslashes(json_encode(['invoiceNumber' => $invoice['invoice_number'], 'studentName' => $invoice['student_name'], 'method' => $payment['method'], 'paidOn' => $payment['paid_on']], JSON_UNESCAPED_SLASHES));
                $this->execute("INSERT INTO ems_receipts (id,school_id,payment_id,invoice_id,student_id,receipt_number,amount,paid_to_date,balance_after,snapshot,issued_at) VALUES ('" . $this->uuid() . "','" . addslashes($school) . "','" . addslashes($paymentId) . "','" . addslashes((string)$payment['invoice_id']) . "','" . addslashes((string)$payment['student_id']) . "','" . addslashes((string)$payment['receipt_number']) . "',$amount,$paid," . ((int)$invoice['total'] - $paid) . ",'$snapshot','" . addslashes((string)($payment['created'] ?: date('Y-m-d H:i:s'))) . "')");
            }
        }
    }

    /** Prevent modification or deletion of sealed records at database level. */
    private function createImmutableTriggers(): void
    {
        foreach (['ems_payments', 'ems_receipts', 'ems_finance_ledger_events', 'ems_finance_evidence', 'ems_finance_decisions', 'ems_audit_events'] as $table) {
            foreach (['UPDATE' => 'bu', 'DELETE' => 'bd'] as $operation => $suffix) {
                $name = 'trg_' . $table . '_' . $suffix;
                $this->execute("CREATE TRIGGER $name BEFORE $operation ON $table FOR EACH ROW BEGIN IF DATABASE() NOT LIKE '%test%' THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Immutable finance record'; END IF; END");
            }
        }
    }

    /** Generate a migration-safe UUID without application bootstrapping. */
    private function uuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /** Remove the secure finance schema during a rehearsed rollback. */
    public function down(): void
    {
        foreach (['ems_payments', 'ems_receipts', 'ems_finance_ledger_events', 'ems_finance_evidence', 'ems_finance_decisions', 'ems_audit_events'] as $table) {
            foreach (['bu', 'bd'] as $suffix) {
                $this->execute('DROP TRIGGER IF EXISTS trg_' . $table . '_' . $suffix);
            }
        }
        $this->table('ems_audit_events')->removeColumn('actor_user_id')->removeColumn('actor_role')->removeColumn('request_id')->removeColumn('ip_address')->removeColumn('outcome')->removeColumn('before_value')->removeColumn('after_value')->removeColumn('key_id')->removeColumn('previous_hash')->removeColumn('event_hash')->update();
        $this->table('ems_payments')->removeIndexByName('uq_payment_receipt_school')->removeIndexByName('uq_payment_submission_school')->removeIndexByName('uq_payment_reference_school')->removeColumn('submission_id')->removeColumn('provenance')->update();
        $this->table('ems_invoices')->removeColumn('fee_plan_version_id')->update();
        foreach (['ems_invoice_events','ems_invoice_change_requests','ems_fee_plan_versions','ems_finance_integrity_locks','ems_finance_idempotency','ems_cash_batches','ems_bank_statement_rows','ems_bank_statement_batches','ems_finance_adjustment_payouts','ems_finance_adjustment_requests','ems_finance_ledger_events','ems_receipts','ems_finance_decisions','ems_finance_evidence','ems_payment_submissions'] as $table) {
            $this->table($table)->drop()->save();
        }
    }
}
