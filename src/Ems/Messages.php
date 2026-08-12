<?php
declare(strict_types=1);

namespace App\Ems;

/**
 * Every user-facing message of the EMS contract, verbatim from document.md.
 *
 * These strings are rendered in the frontend UI exactly as sent, so they are
 * part of the wire contract (document.md §1.3 / §6.3). Keep them in this one
 * file so a diff against the document is a single-file audit.
 *
 * Placeholders use sprintf: call e.g. sprintf(Messages::SESSION_EXISTS, $name).
 */
final class Messages
{
    // --- Canonical 403s (§1.3 — enumeration-safe) -----------------------
    public const STUDENT_FORBIDDEN = 'This record belongs to another family.';
    public const CLASS_FORBIDDEN = 'This class is not assigned to you.';

    // Not doc-specified: tenant membership refusal (school-scope gate).
    public const SCHOOL_FORBIDDEN = 'You do not have access to this school.';

    // Generic capability refusal — the App\Ems\Policy tier gate emits this when
    // a role may not perform an action and the surface has no more specific
    // documented refusal of its own.
    public const ACTION_FORBIDDEN = 'You do not have permission to do that.';

    // --- Auth (§3.18) ---------------------------------------------------
    public const BAD_CREDENTIALS = 'Incorrect e-mail or password.';
    public const ACCOUNT_INVITED = 'This account has a pending invitation. Redeem your invite code to finish setting up.';
    public const ACCOUNT_DISABLED = 'This account has been disabled. Contact the school administrator.';
    public const SCHOOL_NAME_REQUIRED = 'The school needs a name.';
    public const SCHOOL_NAME_TAKEN = 'A school with this name is already registered.';
    public const SCHOOL_SHORT_NAME_REQUIRED = 'Give the school a short name.';
    public const ACCOUNT_NAME_REQUIRED = 'Enter the account holder name.';
    public const EMAIL_INVALID = 'Enter a valid e-mail address.';
    public const EMAIL_EXISTS = 'An account with this e-mail already exists.';
    public const PASSWORD_MIN = 'The password needs at least 8 characters.';
    public const INVITE_CODE_REQUIRED = 'Enter your invite code.';
    public const INVITE_CODE_UNKNOWN = 'That invite code was not recognised. Check it with your school.';
    public const INVITE_DELIVERY_FAILED = 'The invitation could not be delivered. Check the e-mail configuration and try again.';
    public const RESET_INVALID = 'That reset code is not valid any more. Ask for a new one and try again.';
    public const SCHOOL_NOT_FOUND = 'That school could not be found.';
    public const RATE_LIMITED = 'Too many attempts. Please wait a moment and try again.';

    // --- Students & guardians (§3.10) ------------------------------------
    public const STUDENT_NOT_FOUND = 'That student record could not be found.';
    public const GUARDIAN_NOT_FOUND = 'That guardian could not be found.';

    // --- Teachers (§3.11) -------------------------------------------------
    public const TEACHER_NOT_FOUND = 'That teacher record could not be found.';

    // --- Classes & registers (§3.12) -------------------------------------
    public const CLASS_NOT_FOUND = 'That class could not be found.';
    public const REGISTER_CORRECTION_REASON = 'This register is already submitted. A correction needs a reason for the record.';

    // --- Calendar (§3.13) -------------------------------------------------
    public const SESSION_NAME_FORMAT = 'Name a session like 2026/2027.';
    public const SESSION_DATES_REQUIRED = 'Both a start and an end date are needed.';
    public const SESSION_DATE_ORDER = 'The start date must come before the end date.';
    public const SESSION_EXISTS = 'The %s session already exists.';
    public const SESSION_OVERLAP = 'Those dates overlap the %s session.';
    public const SESSION_CLOSED_EDIT = 'This session is closed. Reopen it, with a reason, before changing it.';
    public const TERM_CLOSED_EDIT = 'This term is closed. Reopen it, with a reason, before changing it.';
    // %1$s = term names joined with ' and '; %2$s = '' or 's' (mock parity).
    public const SESSION_CLOSE_TERMS_FIRST = 'Close the %1$s term%2$s first.';
    public const SESSION_REOPEN_ONLY_CLOSED = 'Only a closed session can be reopened.';
    public const SESSION_REOPEN_REASON = 'Reopening a closed session needs a reason for the audit history.';
    public const TERM_DATES_INSIDE = 'Term dates must fall inside the %s session.';
    public const TERM_EXISTS = 'The %s term already exists in %s.';
    public const TERM_REOPEN_ONLY_CLOSED = 'Only a closed term can be reopened.';
    public const TERM_REOPEN_REASON = 'Reopening a closed term needs a reason for the audit history.';
    public const TERM_NOT_FOUND = 'That term could not be found.';
    public const CALENDAR_SESSION_NOT_FOUND = 'That session could not be found.';

    // --- Campuses (§3.13) -------------------------------------------------
    public const CAMPUS_NAME_REQUIRED = 'The campus needs a name.';
    public const CAMPUS_EXISTS = 'A campus called %s already exists.';
    public const CAMPUS_NOT_FOUND = 'That campus could not be found.';
    public const CAMPUS_LAST_ACTIVE = 'A school needs at least one active campus.';

    // --- Settings / users (§3.14) -----------------------------------------
    public const LAST_ADMIN = 'A school needs at least one active administrator.';
    public const SELF_ROLE_CHANGE_FORBIDDEN = 'Administrators cannot change their own role.';
    public const INVITE_ONLY_PENDING = 'Only a pending invitation can be revoked.';
    public const USER_NOT_FOUND = 'That account could not be found.';
    public const USER_ROLE_INVALID = 'Choose a valid account role.';
    public const USER_LINK_REQUIRED = 'Choose the school record this account belongs to.';
    public const USER_LINK_INVALID = 'That account link does not match the selected role.';
    public const USER_LINK_TARGET_INVALID = 'Choose an enrolled student from this school.';
    public const STUDENT_ACCOUNT_EXISTS = 'That student already has a portal account.';
    public const STUDENT_GUARDIAN_REQUIRED = 'Add at least one guardian before admitting the student.';
    public const GUARDIAN_DETAILS_REQUIRED = 'Every guardian needs a first name, last name, and phone number.';

    // --- Admissions (§3.6) ------------------------------------------------
    public const APPLICATION_NOT_FOUND = 'That application could not be found.';
    public const ADMISSIONS_CLOSED = 'Admissions are closed at the moment. Please check back later.';
    public const APPLICANT_NAME_REQUIRED = 'The applicant needs a first and last name.';
    // %s = current status with underscores replaced by spaces (mock parity).
    public const APPLICATION_ILLEGAL_TRANSITION = 'A %s application cannot take this action.';
    public const OFFER_EXPIRY_REQUIRED = 'An offer needs an expiry date.';
    public const OFFER_EXPIRY_FUTURE = 'The offer expiry must be a future date.';
    public const APPLICATION_DECLINE_REASON = 'Declining an application needs a reason for the record.';
    public const OFFER_NOT_EXPIRED = 'This offer has not passed its expiry date yet.';
    public const ENROL_ONLY_ACCEPTED = 'Only an accepted application can be enrolled.';
    public const ENROL_CLASS_UNKNOWN = 'Choose the class the student joins.';
    public const ENROL_SESSION_REQUIRED = 'The admission cycle needs an academic session before enrolment.';
    // %1$s = class name, %2$s = class level, %3$s = applicant's desired level.
    public const ENROL_LEVEL_MISMATCH = '%1$s is a %2$s class — this applicant applied for %3$s.';

    // --- Documents & signed links (§1.7 / §3.8) ---------------------------
    public const DOCUMENT_NOT_FOUND = 'That document could not be found.';
    public const DOCUMENT_NAME_REQUIRED = 'Give the document a name.';
    public const DOCUMENT_REJECT_NOTE = 'Say what is wrong with it, so the family can send a better copy.';
    public const DOCUMENT_ADMISSION_OFFICE = 'Admission documents are for the admissions office.';
    public const DOCUMENT_VERIFY_OFFICE_ONLY = 'Only the admissions office can check documents.';
    public const FILE_TYPE_REJECTED = 'That file type cannot be accepted. Please send a PDF, JPEG or PNG, up to 2 MB.';
    public const FILE_TOO_LARGE = 'That file is larger than 2 MB. Please send a smaller copy.';
    public const FILE_EMPTY = 'That file is empty.';
    public const LINK_EXPIRED = 'This link has expired. Open the document again from the record to get a fresh one.';
    public const LINK_INVALID = 'This link is not valid. Open the document from the record instead.';
    public const LINK_SIGN_IN = 'Sign in to open this document.';
    public const LINK_WRONG_READER = 'This link was issued to somebody else.';
    public const LINK_REFUSED_SUMMARY = 'A document link was opened by somebody it was not issued to, and was refused.';

    // --- Exams (§3.1) -----------------------------------------------------
    public const EXAM_NOT_FOUND = 'That examination could not be found.';
    // %1$s = subject, %2$s = level.
    public const SCHEDULE_DUPLICATE = '%1$s is already scheduled for %2$s.';
    public const SCHEDULE_OUTSIDE_WINDOW = 'The sitting must fall inside the examination window.';
    public const SCHEDULE_NOT_FOUND = 'That sitting could not be found.';
    public const PAPER_EMPTY = 'A paper needs at least one question.';
    public const PAPER_UNKNOWN_QUESTION = 'The paper references a question that no longer exists.';
    public const GRADES_PUBLISHED = 'Results for this examination are released. Reopen it for correction first.';
    public const GRADING_NOT_TEACHER = 'A teacher cannot start grading — that needs the academic lead.';
    public const GRADING_ONLY_BEFORE_RELEASE = 'Only a draft or scheduled examination can start grading.';
    public const RELEASE_NOT_TEACHER = 'A teacher cannot release results — that needs the academic lead.';
    public const RELEASE_ONLY_GRADING = 'Only an examination in grading can release its results.';
    public const REOPEN_NOT_TEACHER = 'A teacher cannot reopen released results.';
    public const REOPEN_ONLY_PUBLISHED = 'Only released results can be reopened for correction.';
    public const REOPEN_REASON = 'Reopening released results needs a reason for the record.';

    // --- Assessments (§3.2) -----------------------------------------------
    public const ASSESSMENT_NOT_FOUND = 'That assessment could not be found.';
    public const ASSESSMENT_NAME_REQUIRED = 'An assessment needs a name.';
    public const ASSESSMENT_MAX_RANGE = 'The maximum score must be between 1 and 100.';
    // %1$s = from status, %2$s = to status.
    public const ASSESSMENT_ILLEGAL_TRANSITION = 'An assessment cannot go from %1$s to %2$s.';
    public const ASSESSMENT_REOPEN_REASON = 'Reopening a closed assessment needs a reason for the record.';
    public const ASSESSMENT_SCORES_LOCKED = 'This assessment is closed. Reopen it for correction before changing scores.';
    public const SCORE_NOT_ENROLLED = 'A score was entered for a student not enrolled in this class.';
    public const SCORE_NEGATIVE = 'A score cannot be negative.';
    // %1$s = the offending score, %2$s = the assessment's maximum.
    public const SCORE_ABOVE_MAX = 'A score of %1$s is above this assessment\'s maximum of %2$s.';

    // --- Grading schemes (§3.3) -------------------------------------------
    public const GRADING_MIN_BANDS = 'A grading scale needs at least two grades.';
    public const GRADING_LETTER_REQUIRED = 'Every grade needs a letter.';
    // %s = letter.
    public const GRADING_LABEL_REQUIRED = 'Grade %s needs a name.';
    public const GRADING_MIN_RANGE = 'The mark for grade %s must be a whole number from 0 to 100.';
    public const GRADING_DESCENDING = 'Each grade must start below the one above it.';
    public const GRADING_LAST_ZERO = 'The lowest grade must start at 0 so every mark has a grade.';
    public const GRADING_DISTINCT_LETTERS = 'Two grades share a letter — each letter must be distinct.';

    // --- Question bank (§3.4) ---------------------------------------------
    public const QUESTION_NOT_FOUND = 'That question could not be found.';

    // --- Fees: structures (§3.7) ------------------------------------------
    public const FEE_STRUCTURE_NOT_FOUND = 'That fee structure could not be found.';
    // %d = percent the rows come to.
    public const FEE_SCHEDULE_PERCENT = 'The instalments come to %d%% of the bill. A payment schedule has to account for all of it.';

    // --- Fees: awards (§3.7) ----------------------------------------------
    public const AWARD_NAME_REQUIRED = 'Give the award a name families will recognise.';
    public const AWARD_VALUE_REQUIRED = 'Enter how much the award is worth.';
    public const AWARD_PERCENT_RANGE = 'A percentage award cannot be more than 100% of the fee.';
    public const AWARD_STUDENT_REQUIRED = 'Choose the student this award is for.';
    public const AWARD_LEVEL_REQUIRED = 'Choose the level this discount applies to.';
    public const AWARD_NOT_FOUND = 'That award could not be found.';
    public const AWARD_ALREADY_ENDED = 'This award has already ended.';
    public const AWARD_END_REASON = 'Say why the award is ending.';

    // --- Fees: invoices (§3.7) --------------------------------------------
    public const INVOICE_NOT_FOUND = 'That invoice could not be found.';
    public const FEE_STUDENT_NOT_FOUND = 'That student could not be found.';
    public const INVOICE_NO_LINE_ITEMS = 'An invoice needs at least one line item.';
    public const INVOICE_ALREADY_CANCELLED = 'This invoice has already been cancelled.';
    public const INVOICE_HAS_PAYMENTS = 'This invoice has payments recorded against it. Reverse them before cancelling.';
    public const INVOICE_CANCELLED_NO_RESCHEDULE = 'A cancelled invoice cannot be rescheduled.';
    public const RESCHEDULE_AGREED_WITH = 'Record who agreed the new arrangement.';
    public const RESCHEDULE_REASON = 'Record why the schedule is being changed.';
    public const RESCHEDULE_NEEDS_INSTALMENT = 'A reschedule needs at least one instalment.';
    public const RESCHEDULE_ROLE = 'Rescheduling a payment plan needs a bursar or administrator.';
    public const INVOICE_CANCELLED_NO_PAYMENTS = 'This invoice has been cancelled and cannot take payments.';

    // --- Fees: instalment validation (§3.7) -------------------------------
    public const INSTALMENT_NEEDS_DATE = 'Every instalment needs a date.';
    public const INSTALMENT_NEEDS_AMOUNT = 'Every instalment needs an amount greater than zero.';
    // %1$s = the instalments' sum (formatted), %2$s = the invoice total (formatted).
    public const INSTALMENT_SUM_MISMATCH = 'The instalments add up to %1$s, but this invoice comes to %2$s once scholarships and discounts are applied.';

    // --- Fees: payments & checkout (§3.7) ---------------------------------
    public const PAYMENT_NOT_FOUND = 'That payment could not be found.';
    public const PAYMENT_ALREADY_REVERSED = 'This payment has already been reversed.';
    public const PAYMENT_REVERSE_ONLY_COMPLETED = 'Only a completed payment can be reversed.';
    public const PAYMENT_REVERSAL_REASON = 'Give a reason for reversing this payment.';
    public const PAYMENT_AMOUNT_REQUIRED = 'Enter an amount greater than zero.';
    public const PAYMENT_AMOUNT_OVER_BALANCE = 'The payment is more than the outstanding balance.';
    public const PAYMENT_METHOD_INVALID = 'Choose cash, bank transfer, POS or cheque.';
    public const PAYMENT_REFERENCE_REQUIRED = 'Enter the bank, POS or cheque reference.';
    public const PAYMENT_DATE_REQUIRED = 'Enter a valid payment date.';
    public const PAYMENT_DATE_FUTURE = 'A payment date cannot be in the future.';
    public const PAYMENT_ALREADY_SETTLED = 'This invoice is already settled.';
    public const ONLINE_PAYMENTS_UNAVAILABLE = 'Online payments are not available yet. Record an offline payment instead.';
    public const CHECKOUT_ALREADY_SETTLED = 'This invoice is already settled.';
    public const CHECKOUT_AMOUNT_REQUIRED = 'Enter the amount to pay.';
    public const CHECKOUT_AMOUNT_OVER_BALANCE = 'The amount is more than the outstanding balance.';
    public const CHECKOUT_ONLY_PENDING = 'Only a pending checkout can take a provider verdict.';
    public const RECEIPT_NOT_FOUND = 'That receipt could not be found.';
    public const RECEIPT_NOT_CONFIRMED = 'A receipt is issued only after the payment is confirmed.';

    // --- Fees: refunds (§3.7) ---------------------------------------------
    public const REFUND_ROLE_REQUEST = 'Only a bursar or administrator can request a refund.';
    public const REFUND_PAYMENT_NOT_COMPLETED = 'Only a confirmed payment can be refunded.';
    public const REFUND_REASON_REQUIRED = 'A refund needs a reason for the record.';
    public const REFUND_AMOUNT_REQUIRED = 'Enter a refund amount greater than zero.';
    // %s = the payment amount (formatted).
    public const REFUND_OVER_PAYMENT = 'A refund cannot exceed the %s paid.';
    // %s = the remaining refundable amount (formatted).
    public const REFUND_OVER_REMAINING = 'Only %s of this payment is left to refund.';
    public const REFUND_NOT_FOUND = 'That refund could not be found.';
    public const REFUND_ROLE_PROCESS = 'Processing a refund needs an administrator.';
    public const REFUND_ONLY_PENDING_PROCESS = 'Only a refund awaiting approval can be processed.';
    public const REFUND_SEPARATION_OF_DUTIES = 'A refund must be approved by someone other than the person who requested it.';
    public const REFUND_ROLE_DECIDE = 'Deciding a refund needs an administrator.';
    public const REFUND_ONLY_PENDING_REJECT = 'Only a refund awaiting approval can be rejected.';
    public const REFUND_REJECT_REASON = 'Rejecting a refund needs a reason for the record.';

    // --- Analytics (§3.22) ------------------------------------------------
    public const ANALYTICS_FORBIDDEN = 'These dashboards are only available to school staff.';

    // --- Portal (§3.19) ---------------------------------------------------
    public const PORTAL_STUDENT_NOT_FOUND = 'That student could not be found.';

    // --- Audit & privacy requests (§3.23) ---------------------------------
    public const GOVERNANCE_FORBIDDEN = 'This area is only available to administrators.';
    public const PRIVACY_NOT_FOUND = 'That request could not be found.';
    public const PRIVACY_PAST_IDENTITY = 'This request has already moved past identity checks.';
    public const PRIVACY_EVIDENCE_REQUIRED = 'Record how the requester proved who they are.';
    public const PRIVACY_VERIFY_FIRST = 'Verify who is asking before deciding. A record must never go to the wrong person.';
    public const PRIVACY_DECISION_REASON = 'Record the reason for this decision.';
    public const PRIVACY_RETENTION_REQUIRED = 'Name what must be kept regardless. Financial and academic records have their own retention rules.';
    public const PRIVACY_ONLY_APPROVED_FULFIL = 'Only an approved request can be marked fulfilled.';
    public const PRIVACY_FULFIL_NOTE = 'Record what was handed over or changed.';

    // --- Incidents (§3.24) ------------------------------------------------
    public const INCIDENT_NOT_FOUND = 'That incident could not be found.';
    public const INCIDENT_SEALED = 'Incident detail is restricted to the responders named on this case.';
    public const INCIDENT_TITLE = 'Give the incident a short title.';
    public const INCIDENT_DESCRIPTION = 'Describe what happened, as far as it is known.';
    public const INCIDENT_CATEGORY = 'Name at least one category of data that may be affected.';
    public const INCIDENT_DISCOVERED = 'Record when the incident was discovered.';
    public const INCIDENT_RECORD_RESPONDER = 'Only a signed in responder can record an incident.';
    public const INCIDENT_STEP_NOTE = 'Record what was done at this step before moving the case on.';
    public const INCIDENT_CLOSED = 'This case is closed. Its record can no longer be added to.';
    public const INCIDENT_NOTE_REQUIRED = 'Write the decision or note to log.';
    public const INCIDENT_ALREADY_RESPONDER = 'That person is already a responder on this case.';
    public const INCIDENT_RESPONDER_MUST_ADMIN = 'A responder must be an active administrator at this school.';

    // --- Communication (§3.20) --------------------------------------------
    public const ANNOUNCEMENT_NOT_FOUND = 'That announcement could not be found.';
    public const ANNOUNCEMENT_TITLE_REQUIRED = 'Give the announcement a title.';
    public const ANNOUNCEMENT_TITLE_TOO_LONG = 'Keep the announcement title to 190 characters or fewer.';
    public const ANNOUNCEMENT_BODY_REQUIRED = 'Write the announcement.';
    public const ANNOUNCEMENT_AUDIENCE_INVALID = 'Choose a valid announcement audience.';
    public const ANNOUNCEMENT_CATEGORY_INVALID = 'Choose a valid announcement category.';
    public const ANNOUNCEMENT_ALREADY_PUBLISHED = 'This announcement is already published.';
    public const ANNOUNCEMENT_FORBIDDEN = 'You cannot open this announcement.';
    public const COMMS_STAFF_ONLY = 'This is only available to school staff.';
    public const COMMS_CHANNEL_INVALID = 'Choose email or SMS.';
    public const COMMS_PURPOSE_INVALID = 'Choose school business or school news.';
    public const DELIVER_NOT_PUBLISHED = 'Publish this announcement before sending it out.';
    public const DELIVER_ALREADY_SENT = 'This announcement has already been sent.';
    public const DELIVER_NOTHING_TO_RETRY = 'Nothing here can be retried. Remaining failures have used every attempt and need a person to follow up.';
    public const PREF_NO_CONTACT = 'No contact record is linked to this account.';
    public const PREF_TRANSACTIONAL_LOCKED = 'Notices about your own ward cannot be switched off. Speak to the school office if the contact details are wrong.';
    public const ALERT_STALE = 'There is nothing to send for this alert any more.';

    // --- Reports (§3.21) --------------------------------------------------
    public const REPORT_NOT_FOUND = 'That report could not be found.';
    public const REPORT_EXPIRED = 'This export has expired and its file has been deleted. Run the report again.';
    public const REPORT_NOT_READY = 'This report is not ready yet.';

    // --- Register merge (§3.15) -------------------------------------------
    public const MERGE_FORBIDDEN = 'Merging records needs a registrar or administrator.';
    public const MERGE_SAME_RECORD = 'Choose two different records to merge.';
    public const MERGE_NOT_ON_REGISTER = 'That person is no longer on the register.';
    public const MERGE_REASON_REQUIRED = 'Record why these two records are the same person.';

    // --- Promotion (§3.16) ------------------------------------------------
    public const PROMOTION_FORBIDDEN = 'Promoting students needs a registrar or administrator.';
    public const PROMOTION_NO_SESSION = 'There is no academic session to promote from.';

    // --- CSV imports (§3.17) ----------------------------------------------
    public const IMPORT_FORBIDDEN = 'Importing records needs a registrar or administrator.';
    public const IMPORT_NOT_FOUND = 'That import could not be found.';
    public const IMPORT_ROW_NOT_FOUND = 'That row could not be found.';
    public const IMPORT_ALREADY_COMMITTED = 'This import has already been committed.';
    public const IMPORT_INVALID_NOT_IMPORTED = 'A row with errors cannot be imported. Correct the file and upload it again.';
    public const IMPORT_CHOOSE_MATCH = 'Choose which existing record this row belongs to.';
    public const IMPORT_MATCH_WITHIN_FILE = 'That match is another row of this same file, which does not exist yet. Skip one of the two rows instead.';
    public const IMPORT_NO_HEADING = 'That file has no heading row. Start from the template.';
    public const IMPORT_NO_RECORDS = 'That file has a heading row but no records under it.';
    public const IMPORT_COMMITTED_DISCARD = 'A committed import cannot be discarded.';

    // --- Subject catalogue (Stage 0 completeness pass) ----------------------
    public const SUBJECTS_FORBIDDEN = 'Only an administrator can manage the subject catalogue.';
    public const SUBJECT_NAME_REQUIRED = 'Enter the subject\'s name.';
    public const SUBJECT_EXISTS = 'That subject is already in the catalogue.';
    public const SUBJECT_IN_USE = 'That subject is referenced by existing records. Retire it instead.';
    public const SUBJECT_NOT_FOUND = 'That subject does not exist.';
    public const SUBJECT_UNKNOWN = 'That subject is not in the school\'s catalogue.';

    // --- Class group management (Stage 1 completeness pass) -----------------
    public const CLASS_NAME_REQUIRED = 'Enter the class\'s name.';
    public const CLASS_EXISTS = 'A class with that name already exists.';
    public const CLASS_IN_USE = 'That class has students or academic history. Move its students first.';
    public const CLASS_MANAGE_FORBIDDEN = 'Only an administrator or registrar can manage classes.';

    // --- Subject allocations + timetable (Stages 2-3) -----------------------
    public const ALLOCATION_EXISTS = 'That subject is already allocated for this class.';
    public const ALLOCATION_NOT_FOUND = 'That allocation could not be found.';
    public const SLOT_TAKEN = 'That period already has a subject scheduled.';
    public const SLOT_NOT_FOUND = 'That timetable entry could not be found.';
    public const TEACHER_DOUBLE_BOOKED = 'That teacher is already teaching another class in this period.';

    // --- Admission cycle management (Stage 4) -------------------------------
    public const CYCLE_NAME_REQUIRED = 'Enter the admission cycle\'s name.';
    public const CYCLE_DATES_REQUIRED = 'Enter when the cycle opens and closes.';
    public const CYCLE_DATE_ORDER = 'The cycle must open before it closes.';
    public const CYCLE_NOT_FOUND = 'That admission cycle could not be found.';
    public const CYCLE_MANAGE_FORBIDDEN = 'Only an administrator or registrar can manage admission cycles.';

    // --- Q10 archival rules + correction tier (Stage 5) ---------------------
    public const STUDENT_HAS_RECORDS = 'A student\'s record cannot be deleted. Withdraw the student instead, or merge duplicates.';
    public const TEACHER_HAS_RECORDS = 'A teacher\'s record cannot be deleted while classes reference it. Mark the teacher as former instead.';
    public const DOCUMENT_VERIFIED_LOCKED = 'A verified document is part of the student\'s record and cannot be removed.';
    public const EXAM_RELEASED_EDIT = 'A released examination cannot be edited. Reopen it for correction first.';
    public const EXAM_HAS_RECORDS = 'This examination already has schedules, papers or grades. It cannot be deleted.';
    public const ASSESSMENT_HAS_SCORES = 'This assessment already has scores. It cannot be changed or deleted.';
    public const ANNOUNCEMENT_SENT_LOCKED = 'A published announcement has already been delivered. It cannot be edited or deleted.';
    public const STRUCTURE_NOT_FOUND = 'That fee structure could not be found.';

    private function __construct()
    {
    }
}
