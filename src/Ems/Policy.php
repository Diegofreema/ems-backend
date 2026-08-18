<?php
declare(strict_types=1);

namespace App\Ems;

/**
 * The one place that answers "may this ROLE invoke this ACTION" (the coarse
 * capability gate of the EMS contract API). Evaluated once, for every
 * authenticated request, in Ems\AppController::beforeFilter — before any action
 * body runs — so a missing gate is impossible: an action absent from the table
 * is DENIED (fail-closed, §1.4 hardening).
 *
 * Two layers, each with one home:
 *  - Policy (here) = coarse CAPABILITY by role. "Can a parent invite users at
 *    all?" No — before the action runs.
 *  - Scope (App\Ems\Scope) + DocumentPolicy = row-level REACH, asserted INSIDE
 *    the action. "Which students may this teacher see?" Capability having
 *    passed, reach still narrows the rows.
 *
 * This replaces the per-controller role gates that used to be scattered across
 * ~15 controllers (and forgotten in 15 more, where membership was wrongly
 * treated as authority). The whole authorization posture is now a single
 * auditable table; PolicyCompletenessTest proves every routed action appears
 * here, and PolicyMatrixTest proves no write leaks to a family role.
 *
 * Roles (App\Ems\Viewer): administrator, registrar, bursar, teacher, parent,
 * student. Public actions (Auth/*, Public::intake/apply, Files::download) never
 * reach here — they short-circuit on $publicActions earlier in beforeFilter.
 */
final class Policy
{
    // Named capability tiers. A tier is just a role set; the names carry the
    // intent so the TABLE below reads as policy, not as anonymous arrays.
    private const OFFICER = ['administrator', 'registrar', 'bursar'];
    private const TIERS = [
        // Whole-school configuration & governance.
        'ADMIN' => ['administrator'],
        // The registry office: student/teacher records, admissions, calendar,
        // question bank, exam definition, imports/merge/promotion.
        'MANAGE' => ['administrator', 'registrar'],
        // The bursary: fee structures, awards, invoices, payments, refunds.
        'FINANCE' => ['administrator', 'bursar'],
        'FINANCE_FAMILY' => ['administrator', 'bursar', 'parent', 'student'],
        // Academic entry: teachers plus the registry office. Reach still
        // narrowed to the teacher's own classes by Scope (S:cls).
        'ACADEMIC' => ['administrator', 'registrar', 'teacher'],
        // Any whole-school officer (analytics, reports, reconciliation).
        'OFFICER' => self::OFFICER,
        // Any authenticated member; row reach narrowed in-action by Scope.
        'ALL' => ['administrator', 'registrar', 'bursar', 'teacher', 'parent', 'student'],
        // The acting user, on their own record (contact preferences). Same role
        // set as ALL — the self-narrowing lives in the action; the distinct
        // name documents that this is a personal, not a shared, surface.
        'SELF' => ['administrator', 'registrar', 'bursar', 'teacher', 'parent', 'student'],
    ];

    /**
     * `Controller.action` => tier, for every authenticated route. Grouped to
     * mirror config/routes.php so the two stay legibly in step.
     *
     * @var array<string, string>
     */
    private const TABLE = [
        // Slug → school resolution (any member of that school).
        'Schools.bySlug' => 'ALL',

        // --- Subjects (§ Stage 0) — administrator-only catalogue ---
        'Subjects.index' => 'ADMIN',
        'Subjects.add' => 'ADMIN',
        'Subjects.edit' => 'ADMIN',
        'Subjects.deactivate' => 'ADMIN',
        'Subjects.reactivate' => 'ADMIN',
        'Subjects.delete' => 'ADMIN',

        // --- Students & guardians (§3.10) ---
        // Reads are ALL, narrowed to the viewer's ward(s)/roster by Scope;
        // writes are the registry office's (flipped off the family surface).
        'Students.classGroups' => 'ALL',
        'Students.index' => 'ALL',
        'Students.view' => 'ALL',
        'Students.attendance' => 'ALL',
        'Students.academics' => 'ALL',
        'Students.enrolments' => 'ALL',
        'Students.guardians' => 'ALL',
        'Students.add' => 'MANAGE',
        'Students.admit' => 'MANAGE',
        'Students.edit' => 'MANAGE',
        'Students.delete' => 'MANAGE',
        'Students.withdraw' => 'MANAGE',
        'Students.assignClass' => 'MANAGE',
        'Students.addGuardian' => 'MANAGE',
        'Guardians.edit' => 'MANAGE',
        'Guardians.setPrimary' => 'MANAGE',
        'Guardians.delete' => 'MANAGE',

        // --- Teachers (§3.11) — directory reads open; records managed ---
        'Teachers.subjects' => 'ALL',
        'Teachers.index' => 'ALL',
        'Teachers.view' => 'ALL',
        'Teachers.assignments' => 'ALL',
        'Teachers.day' => 'ALL',
        'Teachers.add' => 'MANAGE',
        'Teachers.edit' => 'MANAGE',
        'Teachers.delete' => 'MANAGE',
        'Teachers.markFormer' => 'MANAGE',

        // --- Classes, timetable & registers (§3.12) ---
        'Classes.levels' => 'ALL',
        'Classes.options' => 'ALL',
        'Classes.index' => 'ALL',
        'Classes.view' => 'ALL',
        'Classes.roster' => 'ALL',
        'Classes.allocations' => 'ALL',
        'Classes.timetable' => 'ALL',
        // Register (whole-class attendance) is academic work, not a family read.
        'Classes.register' => 'ACADEMIC',
        'Classes.saveRegister' => 'ACADEMIC',
        'Classes.add' => 'MANAGE',
        'Classes.edit' => 'MANAGE',
        'Classes.delete' => 'MANAGE',
        'Classes.addAllocation' => 'MANAGE',
        'Classes.editAllocation' => 'MANAGE',
        'Classes.deleteAllocation' => 'MANAGE',
        'Classes.addSlot' => 'MANAGE',
        'Classes.editSlot' => 'MANAGE',
        'Classes.deleteSlot' => 'MANAGE',

        // --- Calendar (§3.13) — term dates readable school-wide ---
        'Calendar.sessions' => 'ALL',
        'Calendar.calendarAudit' => 'ALL',
        'Calendar.createSession' => 'MANAGE',
        'Calendar.updateSession' => 'MANAGE',
        'Calendar.createTerm' => 'MANAGE',
        'Calendar.closeSession' => 'MANAGE',
        'Calendar.reopenSession' => 'MANAGE',
        'Calendar.updateTermDates' => 'MANAGE',
        'Calendar.closeTerm' => 'MANAGE',
        'Calendar.reopenTerm' => 'MANAGE',

        // --- Campuses (§3.13) ---
        'Campuses.index' => 'ALL',
        'Campuses.add' => 'MANAGE',
        'Campuses.edit' => 'MANAGE',
        'Campuses.setActive' => 'MANAGE',

        // --- School profile & accounts (§3.14) ---
        'School.view' => 'ALL',
        'School.update' => 'MANAGE',
        'Users.index' => 'ADMIN',
        'Users.invite' => 'ADMIN',
        'Users.updateRole' => 'ADMIN',
        'Users.updateLink' => 'ADMIN',
        'Users.updateStatus' => 'ADMIN',
        'Users.revokeInvite' => 'ADMIN',

        // --- Admissions (§3.6) — applicant PII is the registry office's ---
        'Admissions.openCycle' => 'ALL',
        'Admissions.cycles' => 'MANAGE',
        'Admissions.summary' => 'MANAGE',
        'Admissions.index' => 'MANAGE',
        'Admissions.view' => 'MANAGE',
        'Admissions.addCycle' => 'MANAGE',
        'Admissions.editCycle' => 'MANAGE',
        'Admissions.closeCycle' => 'MANAGE',
        'Admissions.review' => 'MANAGE',
        'Admissions.enrol' => 'MANAGE',

        // --- Documents (§3.8) — capability open; DocumentPolicy owns reach ---
        // A family member may attach/read documents on their OWN record;
        // verification is office-only. DocumentPolicy enforces both in-action,
        // so Policy stays out of its way (ALL) and DocPol is authoritative.
        'Documents.index' => 'ALL',
        'Documents.add' => 'ALL',
        'Documents.verify' => 'ALL',
        'Documents.link' => 'ALL',
        'Documents.remove' => 'ALL',

        // --- Exams (§3.1) ---
        // Metadata & the sitting timetable are student-facing; the paper,
        // grade sheets and release workflow are academic; defining/releasing
        // exams is the registry office's.
        'Exams.index' => 'ALL',
        'Exams.view' => 'ALL',
        'Exams.schedules' => 'ALL',
        'Exams.reportCard' => 'ALL',
        'Exams.paper' => 'ACADEMIC',
        'Exams.gradesheet' => 'ACADEMIC',
        'Exams.broadsheet' => 'ACADEMIC',
        'Exams.saveGrades' => 'ACADEMIC',
        'Exams.releases' => 'ACADEMIC',
        'Exams.releasePreview' => 'ACADEMIC',
        'Exams.add' => 'MANAGE',
        'Exams.edit' => 'MANAGE',
        'Exams.delete' => 'MANAGE',
        'Exams.addSchedule' => 'MANAGE',
        'Exams.removeSchedule' => 'MANAGE',
        'Exams.savePaper' => 'MANAGE',
        'Exams.startGrading' => 'MANAGE',
        'Exams.release' => 'MANAGE',
        'Exams.reopen' => 'MANAGE',

        // --- Assessments / derived CA (§3.2) — academic entry, S:cls reach ---
        'Assessments.index' => 'ALL',
        'Assessments.view' => 'ALL',
        'Assessments.add' => 'ACADEMIC',
        'Assessments.edit' => 'ACADEMIC',
        'Assessments.delete' => 'ACADEMIC',
        'Assessments.setStatus' => 'ACADEMIC',
        'Assessments.scoresheet' => 'ACADEMIC',
        'Assessments.saveScores' => 'ACADEMIC',

        // --- Grading schemes (§3.3) — scale readable; edited by the office ---
        'Grading.active' => 'ALL',
        'Grading.history' => 'ALL',
        'Grading.update' => 'MANAGE',

        // --- Question bank (§3.4) — exam integrity: office-only ---
        'Questions.subjects' => 'MANAGE',
        'Questions.index' => 'MANAGE',
        'Questions.view' => 'MANAGE',
        'Questions.add' => 'MANAGE',
        'Questions.edit' => 'MANAGE',
        'Questions.delete' => 'MANAGE',

        // --- Transcripts (§3.5) — family reads own child (S:stu) ---
        'Transcripts.show' => 'ALL',

        // --- Fees: structures & awards (§3.7) — the bursary's ---
        'FeeStructures.index' => 'FINANCE',
        'FeePlanVersions.index' => 'FINANCE',
        'FeePlanVersions.add' => 'FINANCE',
        'FeePlanVersions.decide' => 'ADMIN',
        'FeeStructures.view' => 'FINANCE',
        'FeeStructures.add' => 'FINANCE',
        'FeeStructures.edit' => 'FINANCE',
        'FeeStructures.delete' => 'FINANCE',
        'FeeAwards.index' => 'FINANCE',
        'FeeAwards.add' => 'FINANCE',
        'FeeAwards.decide' => 'ADMIN',
        'FeeAwards.endAward' => 'FINANCE',
        'FeeAwards.endRequests' => 'FINANCE',
        'FeeAwards.decideEnd' => 'ADMIN',

        // --- Fees: invoices (§3.7) — ledger is the one family read (S:stu) ---
        'Invoices.index' => 'FINANCE',
        'Invoices.preview' => 'FINANCE',
        'Invoices.add' => 'FINANCE',
        'Invoices.cancel' => 'FINANCE',
        'Invoices.reschedule' => 'FINANCE',
        'Invoices.addPayment' => 'FINANCE',
        'Invoices.submitPayment' => 'FINANCE',
        'Invoices.requestChange' => 'FINANCE',
        'InvoiceChangeRequests.index' => 'FINANCE',
        'InvoiceChangeRequests.decide' => 'ADMIN',
        // Bulk invoicing: bursar drafts a batch, a different administrator issues.
        'InvoiceBatches.preview' => 'FINANCE',
        'InvoiceBatches.add' => 'FINANCE',
        'InvoiceBatches.index' => 'FINANCE',
        'InvoiceBatches.view' => 'FINANCE',
        'InvoiceBatches.decide' => 'ADMIN',
        // Fee reminders: the bursary sends overdue / due-soon nudges to families.
        'FeeReminders.preview' => 'FINANCE',
        'FeeReminders.send' => 'FINANCE',
        'FeeReminders.index' => 'FINANCE',
        'Invoices.checkout' => 'FINANCE',
        'Invoices.view' => 'FINANCE',
        'Invoices.ledger' => 'FINANCE_FAMILY',

        // --- Fees: payments, checkout confirm & refunds (§3.7) ---
        'Payments.reverse' => 'FINANCE',
        'Payments.requestAdjustment' => 'FINANCE',
        'FinanceAdjustments.index' => 'FINANCE',
        'FinanceAdjustments.decide' => 'ADMIN',
        'FinanceAdjustments.confirmPayout' => 'ADMIN',
        'Payments.receipt' => 'FINANCE_FAMILY',
        'Payments.confirm' => 'FINANCE',
        'PaymentSubmissions.index' => 'FINANCE',
        'PaymentSubmissions.evidence' => 'FINANCE',
        'PaymentSubmissions.decide' => 'ADMIN',
        'StatementBatches.add' => 'FINANCE',
        'StatementBatches.index' => 'FINANCE',
        'StatementBatches.rows' => 'FINANCE',
        'CashBatches.add' => 'FINANCE',
        'CashBatches.index' => 'FINANCE',
        'CashBatches.close' => 'ADMIN',
        'Refunds.request' => 'FINANCE',
        'Refunds.process' => 'ADMIN',
        'Refunds.reject' => 'ADMIN',

        // --- Fees: reconciliation & report (§3.7) — whole-school officers ---
        'Fees.reconciliation' => 'OFFICER',
        'Fees.report' => 'OFFICER',

        // --- Staff dashboard — same officer surface as Analytics ---
        'Dashboard.index' => 'OFFICER',

        // --- Analytics (§3.22) ---
        'Analytics.overview' => 'OFFICER',
        'Analytics.attendance' => 'OFFICER',
        'Analytics.academics' => 'OFFICER',
        'Analytics.enrolment' => 'OFFICER',

        // --- Portal (§3.19) — the family's own home (self / S:stu). The
        // dashboard is ALL at the capability layer; requireRole narrows it to
        // family roles in-action (staff have Dashboard.index instead).
        'Portal.identity' => 'ALL',
        'Portal.dashboard' => 'ALL',
        'Portal.wardOverview' => 'ALL',
        'Portal.notifications' => 'ALL',
        'Portal.markNotificationsRead' => 'ALL',
        // A linked guardian may declare and review their own offline payments;
        // Scope::assertStudentAccess narrows each call to the actual ward.
        'PortalPaymentClaims.index' => 'FINANCE_FAMILY',
        'PortalPaymentClaims.add' => 'FINANCE_FAMILY',

        // --- Audit & privacy requests (§3.23) — administrators only ---
        'Audit.events' => 'ADMIN',
        'PrivacyRequests.index' => 'ADMIN',
        'PrivacyRequests.add' => 'ADMIN',
        'PrivacyRequests.verify' => 'ADMIN',
        'PrivacyRequests.decide' => 'ADMIN',
        'PrivacyRequests.fulfil' => 'ADMIN',

        // --- Incidents (§3.24) — administrators only (responder seal in-action) ---
        'Incidents.index' => 'ADMIN',
        'Incidents.view' => 'ADMIN',
        'Incidents.add' => 'ADMIN',
        'Incidents.advance' => 'ADMIN',
        'Incidents.addNote' => 'ADMIN',
        'Incidents.addResponder' => 'ADMIN',

        // --- Communication (§3.20) — staff broadcast; family feed & prefs ---
        'Communication.feed' => 'ALL',
        'Communication.view' => 'ALL',
        'Communication.myPreferences' => 'SELF',
        'Communication.setPreference' => 'SELF',

        // --- Account & security (§3.18) — every member acts on their OWN
        // account; the action reads the viewer, never a body-supplied id. ---
        'Account.show' => 'SELF',
        'Account.updateProfile' => 'SELF',
        'Account.changePassword' => 'SELF',
        'Account.requestEmailChange' => 'SELF',
        'Account.enableTwoFactor' => 'SELF',
        'Account.confirmTwoFactor' => 'SELF',
        'Account.disableTwoFactor' => 'SELF',
        'Account.sessions' => 'SELF',
        'Account.revokeSession' => 'SELF',
        'Account.revokeOtherSessions' => 'SELF',
        'Account.forgetTrustedDevices' => 'SELF',
        'Communication.index' => 'OFFICER',
        'Communication.add' => 'OFFICER',
        'Communication.edit' => 'OFFICER',
        'Communication.delete' => 'OFFICER',
        'Communication.notifications' => 'OFFICER',
        'Communication.alerts' => 'OFFICER',
        'Communication.sendAlert' => 'OFFICER',
        'Communication.audiencePreview' => 'OFFICER',
        'Communication.delivery' => 'OFFICER',
        'Communication.retry' => 'OFFICER',
        'Communication.deliver' => 'OFFICER',
        'Communication.publish' => 'OFFICER',

        // --- Reports (§3.21) — officers; per-report-type roles in-action ---
        'Reports.jobs' => 'OFFICER',
        'Reports.add' => 'OFFICER',
        'Reports.download' => 'OFFICER',

        // --- Register merge (§3.15) ---
        'Merge.candidates' => 'MANAGE',
        'Merge.preview' => 'MANAGE',
        'Merge.merge' => 'MANAGE',

        // --- Promotion (§3.16) ---
        'Promotion.preview' => 'MANAGE',
        'Promotion.commit' => 'MANAGE',

        // --- CSV imports (§3.17) ---
        'Imports.template' => 'MANAGE',
        'Imports.index' => 'MANAGE',
        'Imports.add' => 'MANAGE',
        'Imports.setDecision' => 'MANAGE',
        'Imports.skipFlagged' => 'MANAGE',
        'Imports.commit' => 'MANAGE',
        'Imports.discard' => 'MANAGE',
        'Imports.view' => 'MANAGE',
    ];

    /**
     * Documented refusals (document.md) the old scattered gates emitted, kept
     * verbatim so a denial reads the same as before. Exact `Controller.action`
     * keys win over the per-controller fallback in DENY_BY_CONTROLLER; anything
     * with neither falls to Messages::ACTION_FORBIDDEN.
     *
     * @var array<string, string>
     */
    private const DENY_BY_ACTION = [
        'Exams.startGrading' => Messages::GRADING_NOT_TEACHER,
        'Exams.release' => Messages::RELEASE_NOT_TEACHER,
        'Exams.reopen' => Messages::REOPEN_NOT_TEACHER,
        'Refunds.request' => Messages::REFUND_ROLE_REQUEST,
        'Refunds.process' => Messages::REFUND_ROLE_PROCESS,
        'Refunds.reject' => Messages::REFUND_ROLE_DECIDE,
        'Invoices.reschedule' => Messages::RESCHEDULE_ROLE,
    ];

    /** @var array<string, string> */
    private const DENY_BY_CONTROLLER = [
        'Dashboard' => Messages::ANALYTICS_FORBIDDEN,
        'Analytics' => Messages::ANALYTICS_FORBIDDEN,
        'Subjects' => Messages::SUBJECTS_FORBIDDEN,
        'Audit' => Messages::GOVERNANCE_FORBIDDEN,
        'Incidents' => Messages::GOVERNANCE_FORBIDDEN,
        'PrivacyRequests' => Messages::GOVERNANCE_FORBIDDEN,
        'Imports' => Messages::IMPORT_FORBIDDEN,
        'Merge' => Messages::MERGE_FORBIDDEN,
        'Promotion' => Messages::PROMOTION_FORBIDDEN,
        'Classes' => Messages::CLASS_MANAGE_FORBIDDEN,
        'Admissions' => Messages::CYCLE_MANAGE_FORBIDDEN,
        'Communication' => Messages::COMMS_STAFF_ONLY,
        'Reports' => Messages::COMMS_STAFF_ONLY,
    ];

    private function __construct()
    {
    }

    /**
     * True when `$role` may invoke `$controller::$action`. An action absent
     * from the table is denied — fail-closed.
     */
    public static function allows(string $controller, string $action, string $role): bool
    {
        $roles = self::rolesFor($controller, $action);

        return $roles !== null && in_array($role, $roles, true);
    }

    /**
     * The roles allowed to invoke `$controller::$action`, or null when the
     * action is not in the table (an unlisted action is denied).
     *
     * @return array<int, string>|null
     */
    public static function rolesFor(string $controller, string $action): ?array
    {
        $tier = self::TABLE[$controller . '.' . $action] ?? null;

        return $tier === null ? null : self::TIERS[$tier];
    }

    /**
     * The refusal sentence to show when `$controller::$action` is denied — the
     * surface's own documented wording where one exists, else the generic.
     */
    public static function messageFor(string $controller, string $action): string
    {
        return self::DENY_BY_ACTION[$controller . '.' . $action]
            ?? self::DENY_BY_CONTROLLER[$controller]
            ?? Messages::ACTION_FORBIDDEN;
    }

    /**
     * Every `Controller.action` key in the table — for PolicyCompletenessTest,
     * which asserts this set equals the routed authenticated action set.
     *
     * @return array<int, string>
     */
    public static function keys(): array
    {
        return array_keys(self::TABLE);
    }
}
