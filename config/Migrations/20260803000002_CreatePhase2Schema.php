<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Phase 2 schema — Admissions & Documents (document.md §3.6 / §3.8).
 *
 * Same conventions as the baseline: `ems_` prefix, CHAR(36) UUID PKs, a
 * `school_id` on every tenant table. Adds the private-bucket abstraction
 * (`ems_document_objects` holds the bytes, never served except through a
 * redeemed grant) and a per-school sequence table so applicationNumber /
 * admissionNumber never collide after a deletion (§3.9 #3).
 */
class CreatePhase2Schema extends BaseMigration
{
    public function up(): void
    {
        // --- Admission cycles (bounded reference set) -----------------------
        $this->table('ems_admission_cycles', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('name', 'string', ['limit' => 190, 'null' => false])
            ->addColumn('session', 'string', ['limit' => 20, 'null' => false])
            ->addColumn('opens_on', 'date', ['null' => false])
            ->addColumn('closes_on', 'date', ['null' => false])
            ->addColumn('status', 'string', ['limit' => 10, 'null' => false, 'default' => 'open'])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id'])
            ->create();

        // --- Admission applications -----------------------------------------
        // guardian + offer are JSON value objects; student_id is set at enrolment.
        $this->table('ems_admission_applications', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('cycle_id', 'uuid', ['null' => false])
            ->addColumn('application_number', 'string', ['limit' => 20, 'null' => false])
            ->addColumn('first_name', 'string', ['limit' => 120, 'null' => false])
            ->addColumn('last_name', 'string', ['limit' => 120, 'null' => false])
            ->addColumn('date_of_birth', 'date', ['null' => true, 'default' => null])
            ->addColumn('gender', 'string', ['limit' => 10, 'null' => false, 'default' => 'other'])
            ->addColumn('desired_level', 'string', ['limit' => 40, 'null' => false, 'default' => ''])
            ->addColumn('previous_school', 'string', ['limit' => 190, 'null' => false, 'default' => ''])
            ->addColumn('guardian', 'json', ['null' => false])
            ->addColumn('note', 'text', ['null' => false, 'default' => ''])
            ->addColumn('submitted_on', 'date', ['null' => false])
            ->addColumn('status', 'string', ['limit' => 15, 'null' => false, 'default' => 'submitted'])
            ->addColumn('offer', 'json', ['null' => true, 'default' => null])
            ->addColumn('student_id', 'uuid', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'status'])
            ->addIndex(['school_id', 'application_number'], ['unique' => true])
            ->create();

        // --- Application reviews (append-only decision trail) ---------------
        $this->table('ems_application_reviews', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('application_id', 'uuid', ['null' => false])
            ->addColumn('reviewer', 'string', ['limit' => 190, 'null' => false, 'default' => ''])
            ->addColumn('action', 'string', ['limit' => 15, 'null' => false])
            ->addColumn('note', 'text', ['null' => false, 'default' => ''])
            ->addColumn('decided_on', 'date', ['null' => false])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'application_id'])
            ->create();

        // --- Documents (metadata; bytes live in ems_document_objects) -------
        $this->table('ems_documents', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('owner', 'string', ['limit' => 12, 'null' => false])
            ->addColumn('owner_id', 'uuid', ['null' => false])
            ->addColumn('name', 'string', ['limit' => 190, 'null' => false])
            ->addColumn('type', 'string', ['limit' => 20, 'null' => false, 'default' => 'other'])
            ->addColumn('content_type', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('size_bytes', 'integer', ['null' => false, 'default' => 0])
            ->addColumn('storage_path', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('uploaded_by', 'string', ['limit' => 190, 'null' => false, 'default' => ''])
            ->addColumn('uploaded_on', 'date', ['null' => false])
            ->addColumn('verification', 'string', ['limit' => 10, 'null' => false, 'default' => 'pending'])
            ->addColumn('verified_by', 'string', ['limit' => 190, 'null' => true, 'default' => null])
            ->addColumn('verified_on', 'date', ['null' => true, 'default' => null])
            ->addColumn('verification_note', 'text', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'owner', 'owner_id'])
            ->create();

        // --- Private object store (the "bucket") ----------------------------
        // Bytes as a data URL, keyed by storage_path. Never returned except by
        // a redeemed signed grant.
        $this->table('ems_document_objects', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('storage_path', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('content_type', 'string', ['limit' => 100, 'null' => false])
            ->addColumn('size_bytes', 'integer', ['null' => false, 'default' => 0])
            ->addColumn('body', 'text', ['limit' => 4294967295, 'null' => false]) // LONGTEXT
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['storage_path'], ['unique' => true])
            ->create();

        // --- Signed-link grants (short-TTL, reader-bound) -------------------
        $this->table('ems_document_grants', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('token', 'string', ['limit' => 64, 'null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('document_id', 'uuid', ['null' => false])
            ->addColumn('storage_path', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('issued_user_id', 'uuid', ['null' => false])
            ->addColumn('issued_role', 'string', ['limit' => 20, 'null' => false])
            ->addColumn('issued_school_id', 'uuid', ['null' => false])
            ->addColumn('filename', 'string', ['limit' => 190, 'null' => false])
            ->addColumn('issued_at', 'biginteger', ['null' => false])   // epoch ms
            ->addColumn('expires_at', 'biginteger', ['null' => false])  // epoch ms
            ->addColumn('revoked', 'boolean', ['null' => false, 'default' => false])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['token'], ['unique' => true])
            ->addIndex(['storage_path'])
            ->create();

        // --- Per-school monotonic sequences (application/admission numbers) --
        $this->table('ems_sequences', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('name', 'string', ['limit' => 40, 'null' => false])
            ->addColumn('value', 'integer', ['null' => false, 'default' => 0])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'name'], ['unique' => true])
            ->create();
    }

    public function down(): void
    {
        foreach ([
            'ems_sequences', 'ems_document_grants', 'ems_document_objects',
            'ems_documents', 'ems_application_reviews',
            'ems_admission_applications', 'ems_admission_cycles',
        ] as $table) {
            $this->table($table)->drop()->save();
        }
    }
}
