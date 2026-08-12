<?php
declare(strict_types=1);

use Migrations\BaseMigration;

/**
 * Phase 5 schema — Portals, communication, reporting & governance (document.md
 * §3.19–§3.24). Nine stored entities:
 *
 *  - Communication: announcements (the notice board), notifications (the
 *    outbound send log), message_recipients (one person/channel/attempt trail
 *    per delivery — addresses stored MASKED, §3.20) and contact_preferences
 *    (consent-aware channels; "silence is never a yes").
 *  - Reports: report_jobs (async CSV export lifecycle; the file lives in the
 *    private bucket, never a public URL, and is deleted after 7 days).
 *  - Governance: privacy_requests (the DSAR workflow) and incidents (the
 *    breach register, readable only by the responders named on a case).
 *  - Imports: import_batches / import_rows (staging — the register is untouched
 *    until a person commits the batch; discard deletes the staged names).
 *
 * Conventions match earlier phases: `ems_` prefix, CHAR(36) UUID PKs, a
 * `school_id` on every tenant table. "On"/"at" business timestamps are plain
 * DATE (the mock stamps them from a date-only clock — TODAY_ISO), stored as
 * DATE and echoed as YYYY-MM-DD; the framework `created` datetime tie-breaks
 * same-day rows in newest-first orderings. Value objects (line trails,
 * responders, filters, download logs, staged cells) ride as JSON.
 *
 * `import_rows` renames two columns off their wire names — `values`→`row_values`,
 * `check`→`row_check` — because both are MySQL reserved words; the serializer
 * maps them back.
 */
class CreatePhase5Schema extends BaseMigration
{
    public function up(): void
    {
        // --- Announcements (the notice board) --------------------------------
        $this->table('ems_announcements', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('title', 'string', ['limit' => 190, 'null' => false])
            ->addColumn('body', 'text', ['null' => false])
            ->addColumn('audience', 'string', ['limit' => 10, 'null' => false])
            ->addColumn('category', 'string', ['limit' => 10, 'null' => false])
            ->addColumn('status', 'string', ['limit' => 10, 'null' => false, 'default' => 'draft'])
            ->addColumn('author_name', 'string', ['limit' => 190, 'null' => false, 'default' => ''])
            ->addColumn('created_on', 'date', ['null' => false])
            ->addColumn('published_on', 'date', ['null' => true, 'default' => null])
            ->addColumn('pinned', 'boolean', ['null' => false, 'default' => false])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'status'])
            ->create();

        // --- Notifications (the outbound send log) ---------------------------
        $this->table('ems_notifications', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('channel', 'string', ['limit' => 8, 'null' => false])
            ->addColumn('kind', 'string', ['limit' => 20, 'null' => false])
            ->addColumn('subject', 'string', ['limit' => 190, 'null' => false, 'default' => ''])
            ->addColumn('body', 'text', ['null' => false])
            ->addColumn('audience_label', 'string', ['limit' => 190, 'null' => false, 'default' => ''])
            ->addColumn('recipient_count', 'integer', ['null' => false, 'default' => 0])
            ->addColumn('sent_on', 'date', ['null' => false])
            ->addColumn('sent_by', 'string', ['limit' => 190, 'null' => false, 'default' => ''])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'channel'])
            ->create();

        // --- Message recipients (one person/channel/attempt trail) -----------
        $this->table('ems_message_recipients', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('announcement_id', 'uuid', ['null' => false])
            ->addColumn('person_id', 'uuid', ['null' => false])
            ->addColumn('person_name', 'string', ['limit' => 190, 'null' => false, 'default' => ''])
            ->addColumn('person_kind', 'string', ['limit' => 10, 'null' => false])
            ->addColumn('about_student_name', 'string', ['limit' => 190, 'null' => true, 'default' => null])
            ->addColumn('channel', 'string', ['limit' => 8, 'null' => false])
            // Stored MASKED — the full address never leaves the contact record (§3.20).
            ->addColumn('address', 'string', ['limit' => 190, 'null' => false, 'default' => ''])
            ->addColumn('status', 'string', ['limit' => 12, 'null' => false, 'default' => 'queued'])
            ->addColumn('attempts', 'integer', ['null' => false, 'default' => 0])
            ->addColumn('provider_ref', 'string', ['limit' => 60, 'null' => true, 'default' => null])
            ->addColumn('failure_reason', 'string', ['limit' => 190, 'null' => true, 'default' => null])
            ->addColumn('suppressed_reason', 'string', ['limit' => 190, 'null' => true, 'default' => null])
            ->addColumn('updated_on', 'date', ['null' => false])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'announcement_id'])
            ->create();

        // --- Contact preferences (consent-aware channels) --------------------
        $this->table('ems_contact_preferences', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('person_id', 'uuid', ['null' => false])
            ->addColumn('person_name', 'string', ['limit' => 190, 'null' => false, 'default' => ''])
            ->addColumn('channel', 'string', ['limit' => 8, 'null' => false])
            ->addColumn('purpose', 'string', ['limit' => 16, 'null' => false])
            ->addColumn('enabled', 'boolean', ['null' => false, 'default' => false])
            ->addColumn('source', 'string', ['limit' => 60, 'null' => false, 'default' => ''])
            ->addColumn('recorded_on', 'date', ['null' => false])
            ->addColumn('withdrawn_on', 'date', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'person_id'])
            ->create();

        // --- Report jobs (async CSV export lifecycle) ------------------------
        $this->table('ems_report_jobs', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('report_type', 'string', ['limit' => 30, 'null' => false])
            ->addColumn('requested_by', 'string', ['limit' => 190, 'null' => false, 'default' => ''])
            ->addColumn('requested_on', 'date', ['null' => false])
            ->addColumn('filters', 'json', ['null' => false])
            ->addColumn('status', 'string', ['limit' => 10, 'null' => false, 'default' => 'queued'])
            ->addColumn('storage_path', 'string', ['limit' => 190, 'null' => true, 'default' => null])
            ->addColumn('row_count', 'integer', ['null' => true, 'default' => null])
            ->addColumn('ready_on', 'date', ['null' => true, 'default' => null])
            ->addColumn('expires_on', 'date', ['null' => true, 'default' => null])
            ->addColumn('error', 'string', ['limit' => 190, 'null' => true, 'default' => null])
            ->addColumn('downloads', 'json', ['null' => false])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'status'])
            ->create();

        // --- Privacy requests (the DSAR workflow) ----------------------------
        $this->table('ems_privacy_requests', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('reference', 'string', ['limit' => 20, 'null' => false])
            ->addColumn('kind', 'string', ['limit' => 12, 'null' => false])
            ->addColumn('subject_name', 'string', ['limit' => 190, 'null' => false])
            ->addColumn('subject_student_id', 'uuid', ['null' => true, 'default' => null])
            ->addColumn('requested_by', 'string', ['limit' => 190, 'null' => false, 'default' => ''])
            ->addColumn('contact', 'string', ['limit' => 190, 'null' => false, 'default' => ''])
            ->addColumn('requested_on', 'date', ['null' => false])
            ->addColumn('detail', 'text', ['null' => false])
            ->addColumn('status', 'string', ['limit' => 10, 'null' => false, 'default' => 'received'])
            ->addColumn('identity_verified_by', 'string', ['limit' => 190, 'null' => true, 'default' => null])
            ->addColumn('identity_verified_on', 'date', ['null' => true, 'default' => null])
            ->addColumn('identity_evidence', 'text', ['null' => true, 'default' => null])
            ->addColumn('decided_by', 'string', ['limit' => 190, 'null' => true, 'default' => null])
            ->addColumn('decided_on', 'date', ['null' => true, 'default' => null])
            ->addColumn('decision_note', 'text', ['null' => true, 'default' => null])
            ->addColumn('fulfilled_on', 'date', ['null' => true, 'default' => null])
            ->addColumn('fulfilment_note', 'text', ['null' => true, 'default' => null])
            ->addColumn('retention_note', 'text', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'status'])
            ->create();

        // --- Incidents (breach register, responder-sealed) -------------------
        $this->table('ems_incidents', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('reference', 'string', ['limit' => 20, 'null' => false])
            ->addColumn('title', 'string', ['limit' => 190, 'null' => false])
            ->addColumn('description', 'text', ['null' => false])
            ->addColumn('severity', 'string', ['limit' => 10, 'null' => false])
            ->addColumn('data_categories', 'json', ['null' => false])
            ->addColumn('status', 'string', ['limit' => 14, 'null' => false, 'default' => 'recorded'])
            ->addColumn('discovered_on', 'date', ['null' => false])
            ->addColumn('recorded_on', 'date', ['null' => false])
            ->addColumn('recorded_by', 'string', ['limit' => 190, 'null' => false, 'default' => ''])
            ->addColumn('responders', 'json', ['null' => false])
            ->addColumn('containment_note', 'text', ['null' => true, 'default' => null])
            ->addColumn('report_evidence', 'text', ['null' => true, 'default' => null])
            ->addColumn('close_summary', 'text', ['null' => true, 'default' => null])
            ->addColumn('entries', 'json', ['null' => false])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'status'])
            ->create();

        // --- Import batches (staging header) ---------------------------------
        $this->table('ems_import_batches', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('kind', 'string', ['limit' => 12, 'null' => false])
            ->addColumn('filename', 'string', ['limit' => 190, 'null' => false, 'default' => ''])
            ->addColumn('uploaded_by', 'string', ['limit' => 190, 'null' => false, 'default' => ''])
            ->addColumn('uploaded_on', 'date', ['null' => false])
            ->addColumn('source_row_count', 'integer', ['null' => false, 'default' => 0])
            ->addColumn('ignored_columns', 'json', ['null' => false])
            ->addColumn('status', 'string', ['limit' => 10, 'null' => false, 'default' => 'review'])
            ->addColumn('committed_on', 'date', ['null' => true, 'default' => null])
            ->addColumn('result', 'json', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'status'])
            ->create();

        // --- Import rows (staged records; `values`/`check` renamed) ----------
        $this->table('ems_import_rows', ['id' => false, 'primary_key' => ['id']])
            ->addColumn('id', 'uuid', ['null' => false])
            ->addColumn('batch_id', 'uuid', ['null' => false])
            ->addColumn('school_id', 'uuid', ['null' => false])
            ->addColumn('line_number', 'integer', ['null' => false, 'default' => 0])
            ->addColumn('row_values', 'json', ['null' => false])
            ->addColumn('row_check', 'string', ['limit' => 10, 'null' => false, 'default' => 'valid'])
            ->addColumn('issues', 'json', ['null' => false])
            ->addColumn('matches', 'json', ['null' => false])
            ->addColumn('decision', 'string', ['limit' => 12, 'null' => false, 'default' => 'undecided'])
            ->addColumn('merge_target_id', 'string', ['limit' => 60, 'null' => true, 'default' => null])
            ->addColumn('outcome', 'string', ['limit' => 10, 'null' => true, 'default' => null])
            ->addColumn('result_id', 'uuid', ['null' => true, 'default' => null])
            ->addColumn('outcome_note', 'text', ['null' => true, 'default' => null])
            ->addColumn('created', 'datetime', ['null' => true, 'default' => null])
            ->addColumn('modified', 'datetime', ['null' => true, 'default' => null])
            ->addIndex(['school_id', 'batch_id'])
            ->create();
    }

    public function down(): void
    {
        foreach ([
            'ems_import_rows', 'ems_import_batches', 'ems_incidents',
            'ems_privacy_requests', 'ems_report_jobs', 'ems_contact_preferences',
            'ems_message_recipients', 'ems_notifications', 'ems_announcements',
        ] as $table) {
            $this->table($table)->drop()->save();
        }
    }
}
