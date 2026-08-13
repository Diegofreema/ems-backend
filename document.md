# EMS Backend Contract — replicate the frontend's data layer exactly

**Audience**: the agent/team refactoring the existing live backend.
**Goal**: the backend must expose endpoints whose responses are byte-for-byte compatible with the shapes documented here, so the frontend at `src/services/*.service.ts` can swap `mockRequest(() => …)` for `http.get/post(url)` **without touching any hook, query key, type, or screen**. This is a refactor to the contract, not a patch: rebuild each module's endpoints to these shapes, in phases, until both systems are in sync.

**Source of truth**: this document was extracted from the frontend repo (`src/mock/`, `src/services/`, `src/features/*/types.ts`). Where the backend's current schema disagrees, the backend changes, not the frontend.

---

## 1. Global conventions (apply to every endpoint)

### 1.1 Tenancy

- A **school is the tenant**. Every school-owned row carries `schoolId` and every access path enforces it. In the mock, tables are physically keyed by `schoolId` (`Record<SchoolId, Entity[]>`) — in the real database this means `school_id` on every tenant-owned table, enforced with row-level security or an equivalent mandatory filter, never an optional `WHERE` clause left to the handler.
- The frontend addresses a school by **slug** in the URL (`/app/:schoolSlug`). It resolves slug → school once, then passes `schoolId` to every service call. The backend should expose school resolution (`GET /schools?slug=…` or equivalent) and accept `schoolId` scoping on everything else (path prefix `/schools/:schoolId/...` recommended).
- The tenant registry itself (`schools`) is the only unscoped table:

```ts
type School = {
  id: string
  slug: string
  name: string
  shortName: string
  motto: string
  address: string
  logo?: string   // data URL in the mock; backend: URL to an uploaded asset
}
```

### 1.2 Pagination — the list envelope

Every collection read returns this envelope, and only this envelope:

```ts
type Paginated<T> = {
  items: T[]
  total: number     // total matching rows across all pages (post-filter)
  page: number      // 1-based, echoed back
  pageSize: number  // echoed back
}
```

- `page` is **1-based**. Slicing is `items[(page-1)*pageSize .. page*pageSize)`.
- Filtering and sorting happen **before** counting: `total` is the size of the filtered set, not the table.
- Requests carry `page` and `pageSize` as query params along with the module's filter params (documented per module below). Sensible server-side caps (e.g. `pageSize <= 100`) are fine; the frontend always sends explicit values.

**Coverage rule: every unbounded, school-level collection endpoint is paginated.** That includes exams, classes, questions, fee structures, awards, invoices, applications, students, teachers, announcements, the feed, notifications, users, calendar audit, merge candidates, import batches, incidents, report jobs, privacy requests, and audit events. The only unpaginated collection responses are (a) **whole-view reads** the screen structurally needs in full — a class's timetable/allocations/roster, an exam's schedules/releases, a gradesheet/broadsheet/register, a student's guardians/enrolments — and (b) **small bounded reference sets** (admission cycles, calendar sessions with terms, campuses, distinct-value lists like class groups/levels/subjects, a single record's documents). Everything else returns `Paginated<T>`, no exceptions.

### 1.3 Errors

The frontend expects failures as an HTTP status plus a **human-readable, user-facing message**:

```ts
class ApiError extends Error { status: number }  // message is shown to the user verbatim
```

- Response body convention: `{ "message": "…" }` with the appropriate status. The message is plain language, in the product's voice — it is rendered in the UI as-is (e.g. `503` → "The school server did not respond. Please try again.").
- Statuses used by the contract: `400` (invalid input), `403` (access refused — see 1.4 for wording rules), `404` (not found), `409` (state conflict, e.g. acting on a closed term), `413` (file too large), `422` (unprocessable, e.g. disallowed file type), `503` (unavailable).
- **Enumeration safety**: a `403` for a record the viewer may not see must say nothing about whether the record exists. Canonical messages:
  - Student record: `"This record belongs to another family."`
  - Class: `"This class is not assigned to you."`
  - Incident detail: `"Incident detail is restricted to the responders named on this case."`

### 1.4 Authorization — the viewer and its scopes

Every request is evaluated against a **viewer**, the claims a verified access token carries:

```ts
type Viewer = { schoolId: string; userId: string | null; role: Role }

type Role = 'administrator' | 'registrar' | 'bursar' | 'teacher' | 'parent' | 'student'
```

Two questions are answered by policy, **once, server-side**, so route guards and data reads can never disagree:

1. **Module access** — role alone answers "may you open this module".
2. **Record access** — relationship answers "may you see THIS child/class".

Scope rules (replicate exactly):

- `administrator`, `registrar`, `bursar` act on the **whole school** — relationship never narrows them.
- A **teacher**'s class scope = classes where they are form teacher, OR hold a subject allocation, OR appear on the timetable. Their student scope = the rosters of those classes.
- A **parent**'s student scope = the `studentIds` linked on their user account. A **student**'s scope = their own record only.
- A viewer with no matching link resolves to an **empty** scope, not an error.
- Incidents add a tighter rule than role: **detail is sealed to the responders named on the case** — even an administrator is refused if not listed (see the incidents module).

Every list read filters by the viewer's scope **before** pagination; every detail read asserts scope membership and refuses with the canonical 403 wording above.

### 1.5 Money and time

- **Timestamps**: ISO-8601 strings, UTC on the wire; the school's timezone (`Africa/Lagos` initially) is a display concern.
- **Money**: integer **kobo** (minor units) everywhere on the wire and at rest, with implicit currency `NGN` in the first release. Never floats, never Naira floats. Kobo is also what Paystack charges and reports in, so provider amounts pass through unconverted. Naira is purely a display concern (the frontend divides by 100 to render and multiplies user input by 100).
- **IDs**: opaque strings. The mock uses readable prefixes (`stu-…`, `inv-…`); the backend may keep UUIDs as long as they are strings — the frontend never parses IDs.

### 1.6 Audit events

Every change to regulated or financially material data appends an audit event:

```ts
type AuditEvent = {
  id: string
  schoolId: string
  actor: string        // display name of the signed-in account that acted
  action: string       // dotted key, e.g. "term.closed", "refund.processed"
  entityType:
    | 'session' | 'term' | 'exam' | 'report' | 'privacy_request' | 'import'
    | 'student' | 'guardian' | 'document' | 'fee_award' | 'grading_scheme'
    | 'refund' | 'invoice' | 'assessment' | 'incident'
  entityId: string
  summary: string      // plain-language description, shown in change history
  reason?: string      // required for reopening/override actions
  at: string           // ISO date
}
```

Audit rows are **append-only**. Never place password material, tokens, or full payment instruments in audit data. Per-module sections list the exact `action` keys each mutation must write.

### 1.7 Private files and signed links

No school document is ever publicly addressable. Replicate this flow:

- **Bucket layout**: `school_id/entity/entity_id/file_id` in a private bucket.
- **Upload constraints** (refuse before writing): content type ∈ {`application/pdf`, `image/jpeg`, `image/png`}, size > 0 and ≤ **2 MB**. Refusals: `422` "That file type cannot be accepted. Please send a PDF, JPEG or PNG, up to 2 MB." / `413` "That file is larger than 2 MB. Please send a smaller copy." / `422` "That file is empty."
- **Reads are grant-based**: a permission check issues a **short-lived signed URL** (60 s TTL in the mock — tune, but keep it short). A grant is tied to the person it was issued to; it is single-audience, not a bearer link for the world.
- An **expired** link must be distinguishable from an **unknown** one (the UI tells the reader the link expired and offers a fresh one). Keep a short tombstone window after expiry for that purpose only.
- Deleting an object revokes its outstanding grants.

### 1.8 What "in sync" means

For each endpoint below, the backend response must satisfy the documented TypeScript type exactly: same field names (camelCase on the wire), same optionality, same string-literal union values, same envelope. Extra fields are tolerated by the frontend but discouraged; missing or renamed fields are breaking. Where a field is documented as derived (e.g. an invoice `balance`), the backend derives it the same way and returns it — the frontend does not compute it.

---

## 2. Table catalog

The complete set of tenant-scoped tables the frontend's data layer maintains. Every one maps to a backend table (or view) carrying `school_id`:

| Table | Entity | Module |
|---|---|---|
| `schools` | School (tenant registry — unscoped) | core |
| `users` | SchoolUser (accounts, role + person link) | settings/auth |
| `students` | Student | students |
| `guardians` | Guardian | students |
| `enrolments` | Enrolment (per-session class placement history) | students/promotion |
| `attendance` | AttendanceRecord | students/classes |
| `attendanceSessions` | AttendanceSession (a register taken for a class on a date) | classes |
| `academics` | AcademicTermRecord (per-term academic summary rows) | students/transcripts |
| `documents` | SchoolDocument (metadata; bytes in the private bucket) | documents |
| `teachers` | Teacher | teachers |
| `classGroups` | ClassGroup | classes |
| `subjectAllocations` | SubjectAllocation (teacher × subject × class) | classes |
| `timetable` | TimetableSlot | classes |
| `academicSessions` | AcademicSession (school year) | calendar |
| `academicTerms` | AcademicTerm | calendar |
| `campuses` | Campus | calendar/settings |
| `admissionCycles` | AdmissionCycle | admissions |
| `admissionApplications` | AdmissionApplication | admissions |
| `applicationReviews` | ApplicationReview | admissions |
| `exams` | Exam | exams |
| `examSchedules` | ExamSchedule | exams |
| `examGrades` | ExamGrade (score rows) | exams |
| `examPapers` | ExamPaper | exams |
| `resultReleases` | ResultRelease (pins the grading scheme version used) | exams |
| `gradingSchemes` | GradingScheme (versioned; exactly one `active`) | grading |
| `assessments` | Assessment (teacher-created, per offering) | assessments |
| `assessmentScores` | AssessmentScore | assessments |
| `questions` | Question (question bank) | questions |
| `feeStructures` | FeeStructure | fees |
| `feeAwards` | FeeAward (scholarships/discounts; applied at issue time only) | fees |
| `invoices` | Invoice | fees |
| `payments` | Payment | fees |
| `refunds` | Refund (only `processed` subtracts from paid totals) | fees |
| `announcements` | Announcement | communication |
| `notifications` | NotificationMessage | communication |
| `messageRecipients` | MessageRecipient (per-recipient delivery rows) | communication |
| `contactPreferences` | ContactPreference (consent-aware channels) | communication |
| `reportJobs` | ReportJob (async export lifecycle) | reports |
| `privacyRequests` | PrivacyRequest | audit |
| `incidents` | Incident (responder-sealed detail) | incidents |
| `importBatches` | ImportBatch (staging; register untouched until commit) | imports |
| `importRows` | ImportRow | imports |
| `auditEvents` | AuditEvent (append-only) | audit |

Structural rules baked into this catalog:

- **Enrolments are per-session history**: a student's current `classGroup` is a denormalized convenience; moving a student up a class next session writes a new enrolment row and never overwrites last year's placement.
- **Import staging is isolated**: uploaded rows live only in `importBatches`/`importRows` until a person commits the batch.
- **Financial records are append-only**: corrections are new rows (credit notes, reversals, refunds), never destructive edits.
- **Released results pin their grading scheme version** (`resultReleases`), so a later scheme change can never rewrite an issued report card.

---

*(Module-by-module contracts follow.)*

## 3. Module contracts

Each module section below lists: the entity types (the exact wire shapes), the endpoints to expose (one per frontend service function — the method/path is a recommendation, the **request params and response shape are the contract**), behavior rules the backend must replicate, and the audit events each mutation writes.

> Convention used below: all paths are under the school scope, e.g. `GET /schools/:schoolId/exams`. `Paginated<T>` and `ApiError` are as defined in §1.

---

### 3.1 Exams

#### Types

```ts
type ExamStatus = 'draft' | 'scheduled' | 'grading' | 'published'

type Exam = {
  id: string
  schoolId: string
  title: string          // e.g. "Third Term Examination"
  session: string        // e.g. "2024/2025"
  term: 'First' | 'Second' | 'Third'
  startDate: string      // ISO date
  endDate: string        // ISO date
  status: ExamStatus
  caMax: number          // continuous-assessment ceiling, e.g. 40
  examMax: number        // exam-paper ceiling, e.g. 60 (caMax + examMax = 100)
}
type ExamInput = Omit<Exam, 'id' | 'schoolId'>

type ExamSchedule = {
  id: string; schoolId: string; examId: string
  subject: string; level: string        // e.g. "JSS 1"
  date: string; startTime: string; endTime: string  // "09:00" style times
  venue: string
}
type ExamScheduleInput = Omit<ExamSchedule, 'id' | 'schoolId' | 'examId'>

type ExamGrade = {
  id: string; schoolId: string; examId: string
  studentId: string; classGroupId: string; subject: string
  ca: number | null      // 0..exam.caMax
  exam: number | null    // 0..exam.examMax
}

type ExamPaper = {
  id: string; schoolId: string; examId: string
  subject: string; level: string
  questionIds: string[]  // ordered
}

type ResultRelease = {
  id: string; schoolId: string; examId: string
  version: number
  releasedOn: string; releasedBy: string
  status: 'released' | 'superseded'
  supersededOn?: string
  reason?: string          // why a correction release happened (version > 1 only)
  schemeVersion?: number   // grading scheme version pinned at release time
}

type ReleasePreview = {
  expectedScores: number; enteredScores: number
  missingScores: number; nextVersion: number
}
```

Read models (server-computed responses — the backend computes these, the frontend never does):

```ts
type GradesheetRow = {
  student: Student
  ca: number | null; exam: number | null; total: number | null
  grade: GradeBand | null
  caMissing?: number   // present only when CA is derived from assessments
}
type Gradesheet = {
  exam: Exam; classGroup: ClassGroup; subject: string
  rows: GradesheetRow[]; gradeBands: GradeBand[]
  caFromAssessments: boolean
}
type GradeEntry = { studentId: string; ca: number | null; exam: number | null }

type BroadsheetCell = { total: number | null; grade: GradeBand | null }
type BroadsheetRow = {
  student: Student
  cells: Record<string, BroadsheetCell>   // keyed by subject
  average: number | null; position: number | null
}
type Broadsheet = { exam: Exam; classGroup: ClassGroup; subjects: string[]; rows: BroadsheetRow[] }

type ReportCardSubject = {
  subject: string
  ca: number | null; exam: number | null; total: number | null
  grade: GradeBand | null; classAverage: number | null
  remark: string           // band label or "Not graded"
  caProvisional?: boolean  // true only when CA derived AND some assessment scores missing
}
type ReportCard = {
  school: School; exam: Exam; student: Student
  classGroup: ClassGroup | null
  subjects: ReportCardSubject[]
  average: number | null; position: number | null; classSize: number
  attendance: AttendanceSummary   // { present, absent, late, excused, total, rate }
  gradeBands: GradeBand[]
}

type ExamPaperDetail = {
  exam: Exam; subject: string; level: string
  paper: ExamPaper | null
  questions: Question[]      // resolved in saved order; deleted ids silently dropped
  totalMarks: number         // sum of resolved question marks
  bank: Question[]           // full bank for the subject
}
```

#### Endpoints

| Endpoint | Request | Response | Behavior |
|---|---|---|---|
| `GET /exams` | `page`, `pageSize`, `query`, `status` (`ExamStatus \| 'all'`), `sort` (`'recent' \| 'title'`) | `Paginated<Exam>` | query matches `title`/`session` case-insensitive. `recent`: session desc, then startDate desc. `title`: title asc, session desc. |
| `GET /exams/:id` | — | `Exam` | 404 "That examination could not be found." |
| `POST /exams` | `ExamInput` | `Exam` | Newest-first ordering. No audit event. |
| `GET /exams/:id/schedules` | — | `ExamSchedule[]` | Sorted date → startTime → level → subject asc. |
| `POST /exams/:id/schedules` | `ExamScheduleInput` | `ExamSchedule` | 422 "`{subject}` is already scheduled for `{level}`." on duplicate (subject, level). 422 "The sitting must fall inside the examination window." when date outside `[startDate, endDate]`. |
| `DELETE /exam-schedules/:id` | — | `void` | 404 "That sitting could not be found." |
| `GET /exams/:id/paper` | `subject`, `level` | `ExamPaperDetail` | Bank filtered by subject only, sorted type → marks → topic. |
| `PUT /exams/:id/paper` | `subject`, `level`, `questionIds: string[]` | `ExamPaper` | Upsert. 422 "A paper needs at least one question." on empty; 422 "The paper references a question that no longer exists." if any id absent from the bank. |
| `GET /exams/:id/gradesheet` | `classId`, `subject` | `Gradesheet` | See computation rules below. Bands come from the exam's **pinned** scheme when published. |
| `PUT /exams/:id/grades` | `classId`, `subject`, `entries: GradeEntry[]` | `void` | Requires class access (403 "This class is not assigned to you."). 422 "Results for this examination are released. Reopen it for correction first." when published. **When the offering is assessment-backed, incoming `ca` is ignored** (existing rows keep theirs, new rows get `ca: null`) — only `exam` marks are written. Upsert key: examId + studentId + subject. |
| `GET /exams/:id/broadsheet` | `classId` | `Broadsheet` | Every subject gets a cell per row; missing = `{ total: null, grade: null }`. |
| `GET /exams/:id/report-card/:studentId` | — | `ReportCard` | Requires student access (403 "This record belongs to another family."). When the student's class group no longer exists: `subjects: []`, `average/position: null`, `classSize: 0`, attendance and bands still returned. |
| `GET /exams/:id/releases` | — | `ResultRelease[]` | Newest version first. |
| `GET /exams/:id/release-preview` | — | `ReleasePreview` | See preview rules below. |
| `POST /exams/:id/release` | `{ reason?: string }` | `ResultRelease` | 403 "A teacher cannot release results — that needs the academic lead." when role=teacher. 409 "Only an examination in grading can release its results." unless status=`grading`. Creates release with `version = priorCount + 1`, `schemeVersion` = current active grading scheme version, `reason` stored only for versions > 1. Side effect: exam → `published`. Audit: `results.released` / `exam` / "Released {title} {session} results (version {v})". |
| `POST /exams/:id/reopen` | `{ reason: string }` | `Exam` | 403 "A teacher cannot reopen released results." 409 "Only released results can be reopened for correction." unless published. 422 "Reopening released results needs a reason for the record." on blank reason. Marks current `released` row `superseded` (+`supersededOn`) — never deleted. Exam → `grading`. Audit: `results.reopened` / `exam`, with `reason`. |

#### Computation rules (must match exactly)

- **CA resolution** (used by gradesheet, broadsheet, report card): if the offering (examId × classGroupId × subject) has **any** assessment (including drafts), CA is derived from assessments (see §3.2) and scaled to `exam.caMax`; otherwise CA is `ExamGrade.ca`.
- **Total** = `ca + exam` only when **both** are non-null; otherwise `null` — no partial totals.
- **Roster** = students with `status === 'enrolled'` in the class (matched by class *name*), sorted by `"{lastName} {firstName}"`. Students with no grade rows still appear with null marks.
- **Averages** = mean of non-null totals, rounded to **1 decimal place**; `null` when nothing counted.
- **Position** = standard competition ranking over non-null averages descending: ties share the earlier position, the next rank skips (1, 2, 2, 4). Null averages get `position: null`.
- **Per-subject classAverage** on the report card = mean of that subject's non-null totals across the class, 1 dp.
- **Attendance summary** = tallies over all of the student's attendance rows (no date window); `rate = total === 0 ? 0 : present / total`.
- **Release preview**: `expectedScores` = for each enrolled student, the number of distinct subjects scheduled for their class's *level* in this exam; `enteredScores` = grade rows with both `ca` and `exam` non-null; `missingScores = max(0, expected - entered)`. A release with missing scores **is allowed** — the preview is informational.
- **Grading scheme pinning**: a published exam is always graded with the scheme version recorded on its `released` release row (`schemeVersion`), even if that version is retired. Unpublished exams use the current active scheme.

---

### 3.2 Assessments (teacher-created CA)

#### Types

```ts
type AssessmentStatus = 'draft' | 'open' | 'closed' | 'published'

type Assessment = {
  id: string; schoolId: string; examId: string
  classGroupId: string; subject: string
  name: string          // e.g. "Class Test 1"
  maximum: number       // this assessment's own ceiling, 1..100 (integer)
  dueOn?: string        // ISO date
  status: AssessmentStatus
  createdBy: string; createdOn: string
}
type AssessmentInput = { name: string; maximum: number; dueOn?: string }

type AssessmentScore = {
  id: string; schoolId: string; assessmentId: string; studentId: string
  score: number | null      // 0..assessment.maximum; null = marked absent
  absentReason?: string
  enteredBy: string
}

type AssessmentSummary = Assessment & {
  scoredCount: number; rosterCount: number; missingCount: number
}

type AssessmentScoreRow = {
  studentId: string; studentName: string; admissionNumber: string
  score: number | null; absentReason?: string
  recorded: boolean   // a score row exists (may still be null = absent)
}
type AssessmentScoresheet = { assessment: Assessment; className: string; rows: AssessmentScoreRow[] }
type ScoreEntry = { studentId: string; score: number | null; absentReason?: string }

type DerivedCa = {
  ca: number | null       // scaled to exam.caMax, whole-number rounded; null when nothing scored
  earned: number; possible: number
  scoredCount: number; missingCount: number; contributingCount: number
  complete: boolean       // contributingCount > 0 && missingCount === 0
}
```

#### The CA derivation algorithm (the single most important rule to replicate)

For an offering (examId × classGroupId × subject) and a student:

1. Contributing assessments = those with `status ∈ {open, closed, published}` (**drafts never contribute**).
2. Per contributing assessment: no score row → `missingCount++`, **excluded from the ratio (never counted as zero)**; row with `score: null` → excused absence, contributes to neither side; otherwise `earned += score`, `possible += assessment.maximum`.
3. `ca = possible === 0 ? null : Math.round((earned / possible) * exam.caMax)` — **whole number**.

An offering "has assessments" (switching the gradesheet to derived CA) when **any** assessment exists for the triple, drafts included.

#### Endpoints

| Endpoint | Request | Response | Behavior |
|---|---|---|---|
| `GET /assessments` | `examId`, `classGroupId`, `subject` | `AssessmentSummary[]` | All statuses. `missingCount = max(0, rosterCount - scoredOnRoster)`. Sort: `(dueOn ?? createdOn)` asc, then name. |
| `GET /assessments/:id` | — | `Assessment` | 404 "That assessment could not be found." |
| `POST /assessments` | `examId`, `classGroupId`, `subject`, `AssessmentInput` | `Assessment` | Class access required. 404 if exam missing. 422 "An assessment needs a name." / "The maximum score must be between 1 and 100." Stored with `status: 'draft'`, `maximum` rounded to integer. Audit: `assessment.created` / `assessment` / 'Created assessment "{name}" (max {max}) for {class} {subject}'. |
| `POST /assessments/:id/status` | `{ status, reason? }` | `Assessment` | Class access. Transitions: `draft→open`, `open→closed`, `closed→open` (reopen, **reason required**: 422 "Reopening a closed assessment needs a reason for the record."), `closed→published`; anything else 409 "An assessment cannot go from {from} to {to}." Audit: `assessment.opened` / `assessment.reopened` / `assessment.closed` / `assessment.published`, `reason` stored only on reopen. |
| `GET /assessments/:id/scoresheet` | — | `AssessmentScoresheet` | **Class access required on this read.** Full enrolled roster. |
| `PUT /assessments/:id/scores` | `ScoreEntry[]` | `void` | Class access. 409 "This assessment is closed. Reopen it for correction before changing scores." unless status ∈ {draft, open}. Validate ALL entries before writing any: 422 "A score was entered for a student not enrolled in this class." / "A score cannot be negative." / "A score of {n} is above this assessment's maximum of {max}." Upsert per student; an existing row cleared to `score: null` with no `absentReason` is **deleted** (unresolved mark, not a zero). No audit event. |

---

### 3.3 Grading schemes (versioned scale)

#### Types

```ts
type GradeBand = { letter: string; min: number; label: string; tone: string }
// tone ∈ 'text-success' | 'text-info' | 'text-warning' | 'text-destructive' | 'text-muted-foreground'

type GradingScheme = {
  id: string; schoolId: string
  version: number
  status: 'active' | 'retired'
  bands: GradeBand[]           // stored DESCENDING by min; last band min must be 0
  effectiveFrom: string
  createdBy: string
  note?: string                // why the scale changed (versions > 1)
}
```

Default bands when a school has never saved a scheme (synthesised, version 1, createdBy "System"):
A≥70 Excellent, B≥60 Very good, C≥50 Credit, D≥45 Pass, E≥40 Fair, F≥0 Fail.

Grade lookup: first band (scanning in stored order) with `total >= min`; last band is the fallback.

#### Endpoints

| Endpoint | Request | Response | Behavior |
|---|---|---|---|
| `GET /grading/active` | — | `GradingScheme` | Never 404s — returns the synthesised default if none saved. |
| `GET /grading/history` | — | `GradingScheme[]` | Newest version first. Empty when only the default exists. |
| `PUT /grading` | `{ bands: GradeBand[]; note?: string }` | `GradingScheme` | Validation (422, first failure wins): ≥2 bands; every letter non-blank ("Every grade needs a letter."); every label non-blank ("Grade {L} needs a name."); min integer 0–100 ("The mark for grade {L} must be a whole number from 0 to 100."); strictly descending mins ("Each grade must start below the one above it."); last min = 0 ("The lowest grade must start at 0 so every mark has a grade."); distinct letters case-insensitive ("Two grades share a letter — each letter must be distinct."). **No-op guard**: identical bands → return active unchanged, no new version. Otherwise retire the active row and append `version = max + 1` — **never mutate an existing version** (this is what keeps released results durable). Audit: `grading.updated` / `grading_scheme` / "Updated the grading scale to version {v} ({n} grades)", `reason` = note. |

---

### 3.4 Question bank

#### Types

```ts
type QuestionType = 'objective' | 'theory'
type QuestionDifficulty = 'easy' | 'medium' | 'hard'

type Question = {
  id: string; schoolId: string
  subject: string
  level: string            // e.g. "JSS 1" or "All levels"
  type: QuestionType
  difficulty: QuestionDifficulty
  topic: string
  text: string
  options: string[]        // objective only; empty for theory
  answer: string           // correct option (objective) or marking guide (theory)
  marks: number
}
type QuestionInput = Omit<Question, 'id' | 'schoolId'>
```

#### Endpoints

| Endpoint | Request | Response | Behavior |
|---|---|---|---|
| `GET /questions` | `page`, `pageSize`, `query`, `subject \| 'all'`, `type \| 'all'`, `difficulty \| 'all'` | `Paginated<Question>` | Query matches text/topic/subject case-insensitive. Sort subject → topic. |
| `GET /questions/:id` | — | `Question` | 404 "That question could not be found." |
| `GET /questions/subjects` | — | `string[]` | Distinct subjects, sorted. |
| `POST /questions` | `QuestionInput` | `Question` | Newest first. |
| `PUT /questions/:id` | `QuestionInput` | `Question` | 404 as above. |
| `DELETE /questions/:id` | — | `void` | Idempotent (no 404). No referential check — exam papers drop dangling ids at read time. |

No audit events in this module.

---

### 3.5 Transcripts (cross-year, read-only)

#### Types

```ts
type TranscriptSubject = {
  subject: string
  ca: number | null; exam: number | null; total: number | null
  grade: GradeBand | null
}

type TranscriptTerm = {
  key: string                      // "{session}::{term}" — dedup identity
  session: string
  term: 'First' | 'Second' | 'Third'
  classGroup: string
  average: number | null
  position: number | null
  classSize: number | null
  remark: string
  source: 'released' | 'history'   // real release vs migrated summary row
  schemeVersion: number | null     // pinned scale for released; null for history
  subjects: TranscriptSubject[]    // detail for released; [] for history
}

type TranscriptSession = {
  session: string
  classGroup: string | null
  terms: TranscriptTerm[]
  average: number | null           // mean of the session's term averages, 1 dp
}

type Transcript = {
  school: School; student: Student
  sessions: TranscriptSession[]
  cumulativeAverage: number | null // mean of every counted term average, 1 dp
  termsCounted: number
  gradeBands: GradeBand[]          // the CURRENT active scale (printed as the key)
  generatedOn: string
}
```

#### Endpoint

`GET /students/:studentId/transcript` → `Transcript`. Student access required (403 "This record belongs to another family."). Fully read-only.

Assembly rules:

1. **Released exams only** — an exam contributes a term only when `status === 'published'`. The cohort for ranking is taken from the grade rows' `classGroupId` (the class *at the time*), not the student's current class. Subjects graded with the exam's **pinned** scheme; term `schemeVersion` records it.
2. **Academic history rows** (migrated `AcademicTermRecord` summaries: session, term, classGroup, average, position, classSize, remark) fill terms **only where no released term holds the same `{session}::{term}` key** — a real release supersedes an imported line.
3. Sessions = union of the student's enrolment sessions and all term sessions, ascending; a session with a placement but no results still appears with `terms: []`. Terms within a session ordered First, Second, Third.
4. Transcript totals read `ExamGrade.ca` **directly** (not the assessment-derived CA).
5. `classSize` for a released term = students with any grade row in that cohort (not current roster size).

---

### 3.6 Admissions

#### Types

```ts
type AdmissionCycle = {
  id: string; schoolId: string
  name: string          // e.g. "2026/2027 Admissions"
  session: string
  opensOn: string; closesOn: string
  status: 'open' | 'closed'
}

type ApplicationStatus =
  | 'submitted' | 'under_review' | 'waitlisted' | 'offered' | 'accepted'
  | 'declined' | 'withdrawn' | 'expired' | 'enrolled'

type ApplicationGuardian = {
  firstName: string; lastName: string
  relationship: 'mother' | 'father' | 'guardian' | 'sibling' | 'other'
  phone: string; email: string; occupation: string
}

type AdmissionApplication = {
  id: string; schoolId: string; cycleId: string
  applicationNumber: string        // e.g. "APP-0007", unique within the school
  firstName: string; lastName: string
  dateOfBirth: string
  gender: 'female' | 'male' | 'other'
  desiredLevel: string             // e.g. "JSS 1"
  previousSchool: string
  guardian: ApplicationGuardian
  note: string
  submittedOn: string
  status: ApplicationStatus
  offer?: { madeOn: string; expiresOn: string; note: string }
  studentId?: string               // set at enrolment — the student this became
}

type ApplicationReview = {        // append-only decision history
  id: string; schoolId: string; applicationId: string
  reviewer: string
  action: ApplicationStatus       // the status the application moved TO
  note: string
  decidedOn: string
}

type ApplicationInput = {
  firstName: string; lastName: string; dateOfBirth: string
  gender: StudentGender; desiredLevel: string; previousSchool: string
  guardian: ApplicationGuardian; note: string
  documents: DocumentUpload[]     // files attached on the public form
}

type ApplicationDetail = {
  application: AdmissionApplication
  cycle: AdmissionCycle | null
  reviews: ApplicationReview[]    // decidedOn desc
}

type ReviewAction = 'start_review' | 'waitlist' | 'offer' | 'accept'
  | 'decline' | 'withdraw' | 'mark_expired'
```

#### State machine (server-enforced)

| action | legal from | to |
|---|---|---|
| `start_review` | submitted | under_review |
| `waitlist` | under_review | waitlisted |
| `offer` | under_review, waitlisted | offered |
| `accept` | offered | accepted |
| `decline` | submitted, under_review, waitlisted, offered | declined |
| `withdraw` | submitted, under_review, waitlisted, offered | withdrawn |
| `mark_expired` | offered | expired |
| enrol (own endpoint) | accepted | enrolled |

Illegal transition → **409** "A {status with space} application cannot take this action."

#### Endpoints

| Endpoint | Request | Response | Behavior |
|---|---|---|---|
| `GET /public/apply/:slug` (unauthenticated) | — | `{ school: {id,name,shortName,motto,logo?}; cycle: AdmissionCycle \| null; levels: string[] } \| null` | Unknown slug → `null` (not 404). `cycle` = the open cycle whose window contains today. `levels` = distinct class-group levels. |
| `GET /admission-cycles` | — | `AdmissionCycle[]` | opensOn desc. |
| `GET /admission-cycles/open` | — | `AdmissionCycle \| null` | Open + in window today. |
| `GET /applications` | `page`, `pageSize`, `query`, `status \| 'all'` | `Paginated<AdmissionApplication>` | Query matches full name or applicationNumber, case-insensitive. Sort submittedOn desc. |
| `GET /applications/summary` | — | `Record<ApplicationStatus, number>` | All nine keys, zero-filled. |
| `GET /applications/:id` | — | `ApplicationDetail` | 404 "That application could not be found." |
| `POST /public/apply/:slug` (unauthenticated) | `ApplicationInput` | `AdmissionApplication` | 422 "Admissions are closed at the moment. Please check back later." when no open cycle. 422 "The applicant needs a first and last name." `applicationNumber` = next school sequence "APP-{0001}". Attached documents stored as `owner: 'application'` files (uploader = guardian name), **validated before the row is inserted — one transaction**. No audit event (the review trail is the record). |
| `POST /applications/:id/review` | `{ action: ReviewAction; note?; offer?: { expiresOn; note } }` | `AdmissionApplication` | Transition legality (409). Offer: 422 "An offer needs an expiry date." / "The offer expiry must be a future date."; stores `offer = { madeOn: today, expiresOn, note }`. Decline with blank note: 422 "Declining an application needs a reason for the record." mark_expired before expiry passed: 422 "This offer has not passed its expiry date yet." Appends an `ApplicationReview` with `action` = destination status. |
| `POST /applications/:id/enrol` | `{ classGroup: string }` (class **name**) | `AdmissionApplication` | 409 "Only an accepted application can be enrolled." 422 "Choose the class the student joins." when the name is unknown. 422 "{class} is a {level} class — this applicant applied for {desiredLevel}." on level mismatch. **Side effects, one transaction**: creates the Student (status `enrolled`, next admission-number sequence "{PREFIX}/{0001}", guardian name/phone denormalized, `enrolledOn` today); creates the primary Guardian row; copies every application document onto the student record (bytes copied too, verification state preserved, application copies kept); sets application `status: 'enrolled'` + `studentId`; appends review "Enrolled into {class}. {n} documents moved onto the student record." |

---

### 3.7 Fees

#### Money convention

**Integer kobo — every amount field in this module is minor units** (₦90,000 travels as `9000000`). All amounts are integers (the server rounds with `Math.round` at ingestion — that now means rounding to the nearest kobo); there is **no `currency` field** on any entity (NGN implicit). Kobo is Paystack's native unit, so checkout amounts and webhook amounts need no conversion. The frontend converts only at its edges: display divides by 100, amount inputs are typed in Naira and multiplied by 100 before they reach the API. Derived figures (percentage awards, instalment splits) are computed in kobo, so no precision is lost to Naira rounding.

#### Types

```ts
type FeeLineItem = {
  name: string
  amount: number        // positive for a charge, NEGATIVE for an award line
  kind?: 'charge' | 'award'   // absent means charge
  awardId?: string      // which award produced this line
}

type ScheduleRow = { label: string; dueOn: string; percent: number }  // percents sum to 100

type FeeStructure = {
  id: string; schoolId: string
  session: string; term: 'First' | 'Second' | 'Third'
  level: string                 // every stream at the level shares the bill
  items: FeeLineItem[]          // charges only
  total: number                 // derived sum, stored
  schedule?: ScheduleRow[]      // absent = one payment by one date
}
type FeeStructureInput = Omit<FeeStructure, 'id' | 'schoolId' | 'total'>

type FeeAward = {
  id: string; schoolId: string
  name: string
  kind: 'scholarship' | 'discount'
  basis: 'percentage' | 'amount'
  value: number                 // percent 1..100, or kobo
  appliesToItem: string         // charge name, or 'all' for the whole bill
  scope: 'student' | 'level'
  studentId?: string; studentName?: string   // scope=student
  level?: string                              // scope=level
  session: string
  term: 'First' | 'Second' | 'Third' | 'all'
  status: 'active' | 'ended'
  note?: string
  awardedBy: string; awardedOn: string
  endedOn?: string; endedReason?: string
}

type Instalment = { number: number; label: string; dueOn: string; amount: number }
type InstalmentState = Instalment & {
  paid: number; balance: number
  status: 'paid' | 'part_paid' | 'overdue' | 'upcoming'
}
type ScheduleRevision = {
  revisedOn: string; revisedBy: string
  agreedWith: string; reason: string
  previous: Instalment[]        // the schedule this revision replaced
}
type RescheduleInput = {
  agreedWith: string; reason: string
  instalments: { label: string; dueOn: string; amount: number }[]
}

type Invoice = {
  id: string; schoolId: string
  invoiceNumber: string         // "GFC/INV/2526T3/0001"
  studentId: string
  studentName: string; classGroup: string   // denormalized at issue
  session: string; term: TermName
  issuedOn: string
  dueDate: string               // on a schedule: the final instalment's date
  lineItems: FeeLineItem[]      // charges then award lines
  total: number
  status: 'issued' | 'cancelled'
  instalments?: Instalment[]
  scheduleRevisions?: ScheduleRevision[]
  cancelledOn?: string; cancellationReason?: string
}
type InvoiceInput = {
  studentId: string; session: string; term: TermName; dueDate: string
  lineItems: FeeLineItem[]      // CHARGES ONLY — server applies awards itself
  instalments?: { label: string; dueOn: string; amount: number }[]
}
type InvoicePreview = {
  lineItems: FeeLineItem[]; charged: number
  awarded: number               // negative or zero
  total: number
  applied: { award: FeeAward; amount: number; base: number }[]
}

type PaymentStatus = 'paid' | 'part_paid' | 'unpaid' | 'overdue' | 'cancelled'
type InvoiceWithBalance = Invoice & {
  paid: number; balance: number
  paymentStatus: PaymentStatus
  charged: number; awarded: number
  schedule: InstalmentState[]   // [] when payable in one go
  nextDue?: InstalmentState     // oldest instalment still owing
}

type Payment = {
  id: string; schoolId: string; invoiceId: string; studentId: string
  receiptNumber: string         // "GFC/RCP/000123"; empty while pending
  amount: number
  method: 'cash' | 'bank_transfer' | 'card' | 'pos' | 'cheque'
  reference?: string
  paidOn: string
  note?: string
  state: 'pending' | 'completed' | 'failed' | 'reversed'
  reversedOn?: string; reversalReason?: string
  channel?: 'provider' | 'office'
  failedOn?: string; failureReason?: string
}
type PaymentInput = { amount: number; method: PaymentMethod; reference?: string; paidOn: string; note?: string }

type Refund = {
  id: string; schoolId: string
  paymentId: string; invoiceId: string; studentId: string
  amount: number; reason: string
  status: 'pending' | 'processed' | 'rejected'
  requestedBy: string; requestedOn: string
  decidedBy?: string; decidedOn?: string
  decisionNote?: string
}
type RefundInput = { paymentId: string; amount: number; reason: string }

type InvoiceDetail = InvoiceWithBalance & { payments: Payment[]; refunds: Refund[] }

type StudentLedger = {
  studentId: string; studentName: string; classGroup: string
  invoices: InvoiceWithBalance[]
  payments: Payment[]
  awards: FeeAward[]            // live AND ended, all sessions
  totalInvoiced: number; totalPaid: number; totalBalance: number
}

type Receipt = {
  payment: Payment; invoice: Invoice
  paidToDate: number            // invoice's CURRENT net paid
  balanceAfter: number
}

type ReconciliationReport = {
  pending: (Payment & { studentName; invoiceNumber })[]
  failed: (Payment & { studentName; invoiceNumber })[]
  completedCount: number; completedAmount: number   // gross, not net of refunds
  pendingAmount: number; reversedCount: number
  refundsPending: (Refund & { studentName; invoiceNumber; receiptNumber; paymentAmount })[]
  refundedAmount: number; refundedCount: number     // processed only
}

type FeesReport = {
  term: TermName | 'all'
  totalInvoiced: number; totalCollected: number     // collected is NET of processed refunds
  totalAwarded: number                              // positive figure
  totalOutstanding: number; collectionRate: number  // 0..1
  invoiceCount: number; paidCount: number; overdueCount: number
  byClass: { key: string; label: string; invoiced: number; collected: number; outstanding: number; collectionRate: number }[]
  recentPayments: Payment[]                         // completed, school-wide, latest 8
}
```

#### Core arithmetic (the backend is the single authority — replicate exactly)

- **Net paid per invoice** (every balance derives from this):
  `paid = Σ completed payments − Σ processed refunds`. Pending/failed/reversed payments and pending/rejected refunds contribute nothing.
- **Award application at issue** (`applyAwards`): awards sorted student-scope-first, then awardedOn asc, then name. Base per award = the named charge's amount (case-insensitive name match) or the whole charge total for `'all'`; base ≤ 0 → award skipped. `raw = percentage ? round(base·value/100) : round(value)`; applied `amount = min(raw, base, remainingBill)` — **no compounding** (each award computed on the original charge) and the bill floors at zero. Produces a negative line `kind: 'award'` labeled "{name} ({value}% of {target})" or "{name} (off {target})".
- **Active awards for an invoice**: `status = 'active'` AND session matches AND (term = 'all' or matches) AND (student award → studentId matches; level award → level = student's level, where level = classGroup minus its last character, "JSS 1A" → "JSS 1"). A child gets both their own scholarships and their level's discounts. **Awards are applied only at issue — never re-applied to an existing invoice; ending an award never edits an issued bill.**
- **Instalment waterfall**: payments settle instalments oldest-first. Per instalment: `paidHere = clamp(netPaid − consumed, 0, amount)`; status `paid` when balance 0, else `overdue` when dueOn < today, else `part_paid` when something paid, else `upcoming`. `nextDue` = first with balance > 0.
- **Invoice payment status**: cancelled → `cancelled`; balance ≤ 0 → `paid`; any overdue instalment (or no instalments and dueDate past) → `overdue`; paid > 0 → `part_paid`; else `unpaid`.
- **Instalment validation** (issue and reschedule): every row needs a date (422 "Every instalment needs a date.") and amount > 0 (422 "Every instalment needs an amount greater than zero."); rounded amounts must sum **exactly** to the invoice total after awards (422 "The instalments add up to ₦X, but this invoice comes to ₦Y once scholarships and discounts are applied."); rows sorted by dueOn and renumbered 1..n; labels default "Instalment {n}".
- **Numbering**: invoice "{PREFIX}/INV/{termCode}/{seq 4-digit}" (termCode e.g. "2526T1"), per-term sequence; receipt "{PREFIX}/RCP/{seq 5-digit}", school-wide sequence. Backend: use real DB sequences per scope, keep the format.

#### Endpoints

| Endpoint | Request | Response | Behavior |
|---|---|---|---|
| `GET /fee-structures` | `page`, `pageSize`, `query` (level match), `term \| 'all'` | `Paginated<FeeStructure>` | Sort term, then level. |
| `POST /fee-structures` | `FeeStructureInput` | `FeeStructure` | Blank-named items dropped; schedule rows without a date dropped; surviving schedule percents must sum to 100 (422 "The instalments come to X% of the bill. A payment schedule has to account for all of it."). |
| `GET /fee-awards` | `page`, `pageSize`, `query`, `term \| 'all'`, `status \| 'all'` | `Paginated<FeeAward>` | An all-terms award matches every term filter. Sort: active first, awardedOn desc, name. |
| `POST /fee-awards` | `FeeAwardInput` | `FeeAward` | 422s: "Give the award a name families will recognise." / "Enter how much the award is worth." (value ≤ 0) / "A percentage award cannot be more than 100% of the fee." / "Choose the student this award is for." / "Choose the level this discount applies to." Audit: `fee_award.granted` / `fee_award` / "{name} awarded to {who} — {value} off {target}." |
| `POST /fee-awards/:id/end` | `{ reason }` | `FeeAward` | 422 "This award has already ended." / "Say why the award is ending." Never deleted; issued invoices keep it. Audit: `fee_award.ended`, with reason. |
| `GET /invoices` | `query`, `status: PaymentStatus \| 'all'`, `term \| 'all'`, `page`, `pageSize`, `sort: 'recent' \| 'balance' \| 'name'` | `Paginated<InvoiceWithBalance>` | Status filters on the **derived** paymentStatus. Query over studentName/invoiceNumber/classGroup. `recent` = issuedOn desc; `balance` = balance desc; `name` = studentName asc. |
| `GET /invoices/:id` | — | `InvoiceDetail` | 404 "That invoice could not be found." Payments paidOn desc (all states); refunds requestedOn desc (all statuses). |
| `POST /invoices/preview` | `{ studentId, session, term, lineItems }` | `InvoicePreview` | Same pricing routine as issue — preview and bill can never differ. 404 "That student could not be found." |
| `POST /invoices` | `InvoiceInput` | `Invoice` | Charges cleaned (blank names dropped, amounts rounded and floored at 0); zero charges → 422 "An invoice needs at least one line item." **Client-sent award lines are ignored — the server prices awards itself.** Instalment rules above; dueDate = last instalment's date when scheduled. |
| `POST /invoices/:id/cancel` | `{ reason }` | `Invoice` | 422 "This invoice has already been cancelled." / "This invoice has payments recorded against it. Reverse them before cancelling." (net-of-refunds check). |
| `POST /invoices/:id/reschedule` | `RescheduleInput` | `Invoice` | 403 "Rescheduling a payment plan needs a bursar or administrator." 422 "A cancelled invoice cannot be rescheduled." / "Record who agreed the new arrangement." / "Record why the schedule is being changed." / "A reschedule needs at least one instalment." + instalment rules (must equal invoice total). Appends a `ScheduleRevision` preserving the old rows (a single-payment invoice's history row is `{number:1, label:'Full payment', dueOn: dueDate, amount: total}`); replaces instalments; dueDate = new last date. Payments are NOT touched — money re-settles the new rows oldest-first. Audit: `invoice.rescheduled` / `invoice` / "Rescheduled {number} for {student} into {n} instalment(s) ending {date}, agreed with {who}. The schedule it replaced is kept as history.", with reason. |
| `POST /invoices/:id/payments` | — | — | Retired direct posting endpoint. Always `410 Gone`; use `/invoices/:id/payment-submissions` and separate administrator approval. |
| `POST /payments/:id/reverse` | `{ reason }` | `Payment` | 404 "That payment could not be found." Only a completed payment can be reversed and a reason is required. Sets state `reversed` + reversedOn/reason and audits `payment.reversed`. |
| `POST /invoices/:id/checkout` | `{ amount }` | `Payment` | Online collection seam, deliberately inactive until a verified provider adapter is implemented. Always 503 "Online payments are not available yet. Record an offline payment instead." |
| `POST /checkout/:paymentId/confirm` | provider event | `Payment` | Online confirmation seam, deliberately inactive. Browser supplied outcomes cannot complete a payment or issue a receipt. Always returns the same 503 as checkout until the signed webhook and server verification flow replaces this guard. |
| `GET /payments/:id/receipt` | — | `Receipt` | Finance users and linked family accounts may read a receipt within their student scope. 404 "That receipt could not be found." 422 "A receipt is issued only after the payment is confirmed." (pending/failed; a reversed payment still yields its receipt). |
| `POST /refunds` | `RefundInput` | `Refund` | 403 "Only a bursar or administrator can request a refund." 422 "Only a confirmed payment can be refunded." / "A refund needs a reason for the record." / "Enter a refund amount greater than zero." Refundable remaining = payment.amount − Σ non-rejected refunds on it (pending reserves; rejected frees): over → 422 "A refund cannot exceed the ₦X paid." or "Only ₦X of this payment is left to refund." Moves no money. Audit: `refund.requested`. |
| `POST /refunds/:id/process` | `{ note? }` | `Refund` | 403 "Processing a refund needs an administrator." 409 "Only a refund awaiting approval can be processed." **Separation of duties**: approver must differ from requester → 403 "A refund must be approved by someone other than the person who requested it." This is the only point money leaves — net paid drops immediately. Audit: `refund.processed`. |
| `POST /refunds/:id/reject` | `{ reason }` | `Refund` | 403 "Deciding a refund needs an administrator." 409 "Only a refund awaiting approval can be rejected." 422 "Rejecting a refund needs a reason for the record." Frees the reserved amount. Audit: `refund.rejected`. |
| `GET /reconciliation` | — | `ReconciliationReport` | Shapes above; completed figures are gross; refunded figures are processed-only. |
| `GET /students/:studentId/ledger` | — | `StudentLedger` | Student access required (403 family message). totalInvoiced excludes cancelled invoices; awards list is unfiltered by session. |
| `GET /fees/report` | `term \| 'all'` | `FeesReport` | Collected figures net of processed refunds; byClass sorted outstanding desc; recentPayments not term-filtered. |

---

### 3.8 Documents & signed links

#### Types

```ts
type DocumentOwner = 'student' | 'application'
type DocumentVerification = 'pending' | 'verified' | 'rejected'
type StudentDocumentType = 'birth_certificate' | 'report_card' | 'photo'
  | 'transfer_letter' | 'medical' | 'other'

type SchoolDocument = {
  id: string; schoolId: string
  owner: DocumentOwner
  ownerId: string               // student id or application id
  name: string
  type: StudentDocumentType
  contentType: string; sizeBytes: number
  storagePath: string           // private bucket path — NEVER sent to a browser
  uploadedBy: string; uploadedOn: string
  verification: DocumentVerification
  verifiedBy?: string; verifiedOn?: string; verificationNote?: string
}

type DocumentUpload = {
  name: string; type: StudentDocumentType
  contentType: string; sizeBytes: number
  body: string                  // file bytes (data URL in the mock; multipart in production)
}

type SignedLink = {
  token: string
  path: string                  // "/files/{token}" — the frontend route
  expiresAt: number             // epoch ms
  documentId: string
  filename: string              // slugified name + proper extension
}

type DocumentFile = {
  filename: string; contentType: string
  body: string                  // the bytes (data URL)
  sizeBytes: number
  documentName: string
}
```

#### Access rules

- Student-owned documents: student scope (403 "This record belongs to another family.").
- Application-owned documents: `administrator`/`registrar` only (403 "Admission documents are for the admissions office.").
- Verification: `administrator`/`registrar` only (403 "Only the admissions office can check documents.") — a bursar may read a student document but never verify.

#### Endpoints

| Endpoint | Request | Response | Behavior |
|---|---|---|---|
| `GET /documents` | `owner`, `ownerId` | `SchoolDocument[]` | Access gate per owner kind. uploadedOn desc. |
| `POST /documents` | `owner`, `ownerId`, `DocumentUpload` | `SchoolDocument` | Upload constraints from §1.7 (422/413 with those exact messages); blank name → 422 "Give the document a name." Verification starts `pending`. Audit: `document.uploaded` / `document` / 'Added "{name}" to the {student\|application} record.' |
| `POST /documents/:id/verify` | `{ decision: 'verified' \| 'rejected'; note }` | `SchoolDocument` | Rejection needs a note: 422 "Say what is wrong with it, so the family can send a better copy." Re-verification allowed. Audit: `document.verified` / `document.rejected`, summary 'Accepted/Rejected "{name}".', reason = note. |
| `DELETE /documents/:id` | — | `void` | Deletes the object AND revokes its outstanding grants. Audit: `document.removed` / 'Deleted "{name}" and the file behind it.' |
| `POST /documents/:id/link` | — | `SignedLink` | **The permission decision point.** Full access check, then issue a short-TTL grant bound to (userId, role, schoolId). Response never contains bytes or the storage path. A mutation, never cached. |
| `GET /files/:token` | — | `DocumentFile` | Redemption, checked in this exact order: (1) grant lookup — expired → 410 "This link has expired. Open the document again from the record to get a fresh one."; unknown → 410 "This link is not valid. Open the document from the record instead." (2) no session → 401 "Sign in to open this document." (3) reader ≠ issuee (userId+schoolId+role) → audit `document.link_refused` ("A document link was opened by somebody it was not issued to, and was refused.") then 403 "This link was issued to somebody else." (4) document since deleted → 404. (5) **access re-checked at redemption** (permission may have changed since issue). (6) success → audit `document.downloaded` ('Opened "{name}".'). Filename: slugified doc name + `.pdf`/`.png`/`.jpg`. |

Internal (not an endpoint): **transfer to student at enrolment** — copies each application document to the student record (new ids, new bucket paths, bytes copied, verification state and uploader/date preserved; application copies retained). Runs inside the enrol transaction.

The frontend reads two storage constants for display: the signed-link TTL (renders a countdown) and the accepted-format label "PDF, JPEG or PNG, up to 2 MB" — keep both stable or expose them via config.

---

### 3.9 Hardening notes for the backend (fix these while keeping the wire contract)

The mock has known laxities that a real, scaled backend must correct **without changing response shapes**:

1. **Derive role/actor from the token, never the request body.** The mock passes `actorRole`/`actor` from the client session store (refunds, releases). The backend uses the authenticated principal.
2. **Separation of duties by user id, not display name** (refund approve compares names in the mock).
3. **Real sequences for numbering.** `applicationNumber`, `admissionNumber`, invoice/receipt sequences are table-length-derived in the mock and would collide after deletion. Use DB sequences scoped as documented (per school; invoices per term code) while keeping the formats.
4. **Transactions.** Public application submit (row + files), enrolment (student + guardian + documents + application update + review), release (release row + exam status + audit) are each one atomic unit.
5. **Validate office payments.** The active backend accepts only cash, bank transfer, POS, or cheque, requires references for non-cash methods, rejects future or invalid dates, and rejects amounts outside the remaining invoice balance.
6. **Role-gate and audit payment reversal and invoice cancellation** (bursar/administrator), even though the mock does neither — additions are non-breaking.
7. **Guard against refund-after-reversal double counting**: refuse to reverse a payment that has a processed refund (or vice versa) with a 422.
8. **Provider verification**: `POST /checkout/:id/confirm` must only be drivable by verified Paystack webhook events / server-side verification — never trust a browser redirect. Keep the idempotent-replay semantics.
9. **Rate-limit** the public apply endpoint and sign-in; **cap pageSize**; index every `(school_id, …)` filter/sort column used by the list endpoints.

---

### 3.10 Students

#### Types

```ts
type StudentStatus = 'enrolled' | 'applicant' | 'graduated' | 'withdrawn'
type StudentGender = 'female' | 'male' | 'other'

type Student = {
  id: string; schoolId: string
  admissionNumber: string
  firstName: string; lastName: string
  dateOfBirth: string              // YYYY-MM-DD
  gender: StudentGender
  classGroup: string               // e.g. "JSS 1A"
  status: StudentStatus
  guardianName: string             // denormalized mirror of the primary guardian
  guardianPhone: string
  enrolledOn: string
}
type StudentInput = Omit<Student, 'id' | 'schoolId'>

type GuardianRelationship = 'mother' | 'father' | 'guardian' | 'sibling' | 'other'
type Guardian = {
  id: string; schoolId: string; studentId: string
  firstName: string; lastName: string
  relationship: GuardianRelationship
  phone: string; email: string; occupation: string
  isPrimary: boolean               // exactly one primary per student
}
type GuardianInput = Omit<Guardian, 'id' | 'schoolId' | 'studentId'>

type AttendanceStatus = 'present' | 'absent' | 'late' | 'excused'
type AttendanceRecord = {
  id: string; schoolId: string; studentId: string
  date: string; status: AttendanceStatus; note?: string
}
type AttendanceSummary = {          // derived on read, never stored
  present: number; absent: number; late: number; excused: number
  total: number
  rate: number                      // present ÷ total, 0..1
}

type AcademicTermRecord = {         // migrated per-term summaries (transcript history)
  id: string; schoolId: string; studentId: string
  session: string; term: 'First' | 'Second' | 'Third'
  classGroup: string
  average: number; position: number; classSize: number
  remark: string
}

type EnrolmentStatus = 'active' | 'completed'
type EnrolmentOutcome = 'promoted' | 'repeated' | 'graduated' | 'withdrawn'
type Enrolment = {                  // per-session placement history
  id: string; schoolId: string; studentId: string
  session: string; classGroup: string; level: string
  startedOn: string; endedOn?: string
  status: EnrolmentStatus
  outcome?: EnrolmentOutcome
  promotedTo?: string               // class moved into, when promoted/repeated
  average?: number | null           // released average behind the decision
}
```

#### Endpoints

| Endpoint | Request | Response | Behavior |
|---|---|---|---|
| `GET /students` | `page`, `pageSize`, `query`, `status \| 'all'`, `classGroup \| 'all'`, `sort: 'name' \| 'admission' \| 'recent'` | `Paginated<Student>` | **Viewer-scoped before pagination** (parents see wards, teachers their rosters, students themselves). Query matches full name, admissionNumber, classGroup. `name` = "lastName firstName" asc; `admission` = admissionNumber asc; `recent` = enrolledOn desc. |
| `GET /students/:id` | — | `Student` | Student access (403 family message). 404 "That student record could not be found." |
| `GET /students/class-groups` | — | `string[]` | Distinct classGroup values, sorted. |
| `POST /students` | `StudentInput` | `Student` | **Side effect**: when status is `enrolled` and a current session exists, also creates an active `Enrolment` for that session (level = classGroup minus stream letter). |
| `POST /students/admit` | `{ student: Omit<StudentInput, 'guardianName' \| 'guardianPhone'>; guardians: GuardianInput[] }` | `Student` | Requires at least one guardian. Creates the student, every guardian, the primary contact mirror, and the current-session enrolment in one transaction. Any failed save rolls the entire admission back. |
| `PUT /students/:id` | `StudentInput` | `Student` | Full merge-replace. 404 as above. |
| `DELETE /students/:id` | — | `void` | **Always refused (409** "A student's record cannot be deleted. Withdraw the student instead, or merge duplicates."**)** — a student's record is regulated data a school can never hard-delete, dependents or not. Use `POST /students/:id/withdraw` (archival) or merge (the only path that removes a duplicate). |
| `POST /students/:id/class` | `{ classGroup }` | `Student` | Updates the live placement only. |
| `GET /students/:id/guardians` | — | `Guardian[]` | Student access. Primary first, then firstName asc. |
| `POST /students/:id/guardians` | `GuardianInput` | `Guardian` | First guardian is always primary; an explicit primary demotes the others. **Always re-sync** the student's denormalized guardianName/guardianPhone from the primary. |
| `PUT /guardians/:id` | `GuardianInput` | `Guardian` | 404 "That guardian could not be found." Primary demotion + re-sync as above. |
| `POST /guardians/:id/primary` | — | `Guardian` | Makes this the single primary; re-sync. |
| `DELETE /guardians/:id` | — | `void` | Idempotent. **Soft-archive** (a guardian is student-related data — the row is stamped `archived_at` and hidden from every read, never destroyed); the client sees it disappear exactly as before. If the removed one was primary, promote the first remaining active sibling; re-sync. |
| `GET /students/:id/attendance` | — | `{ records: AttendanceRecord[]; summary: AttendanceSummary }` | Student access. Records date desc; summary derived. |
| `GET /students/:id/academics` | — | `AcademicTermRecord[]` | Student access. "{session} {term}" desc. |
| `GET /students/:id/enrolments` | — | `Enrolment[]` | Student access. Session desc, startedOn desc. |

No audit events in this module (attendance corrections are tracked on the class register's session row, §3.12).

---

### 3.11 Teachers

#### Types

```ts
type TeacherStatus = 'active' | 'on_leave' | 'former'
type Teacher = {
  id: string; schoolId: string
  staffNumber: string
  firstName: string; lastName: string
  email: string; phone: string
  gender: 'female' | 'male' | 'other'
  subjects: string[]               // what they are qualified to teach
  status: TeacherStatus
  hiredOn: string
}
type TeacherInput = Omit<Teacher, 'id' | 'schoolId'>
```

#### Endpoints

| Endpoint | Request | Response | Behavior |
|---|---|---|---|
| `GET /teachers` | `page`, `pageSize`, `query`, `status \| 'all'`, `subject \| 'all'`, `sort: 'name' \| 'staff' \| 'recent'` | `Paginated<Teacher>` | Not viewer-scoped. Query matches name, staffNumber, any subject. |
| `GET /teachers/:id` | — | `Teacher` | 404 "That teacher record could not be found." |
| `GET /teachers/subjects` | — | `string[]` | Union of all teachers' subjects, sorted. |
| `POST /teachers` | `TeacherInput` | `Teacher` | — |
| `PUT /teachers/:id` | `TeacherInput` | `Teacher` | — |
| `DELETE /teachers/:id` | — | `void` | Idempotent; allocations/timetable keep a dangling teacherId which renders as "Unassigned". |
| `GET /teachers/:id/assignments` | — | `{ classGroupId; className; subject }[]` | From subject allocations; sorted className then subject. |

---

### 3.12 Classes, timetable & attendance registers

#### Types

```ts
type ClassGroup = {
  id: string; schoolId: string
  name: string                     // "JSS 1A"
  level: string                    // "JSS 1"
  stream: string                   // "A"
  formTeacherId: string | null
  capacity: number
}
type SubjectAllocation = {
  id: string; schoolId: string; classGroupId: string
  subject: string
  teacherId: string | null         // null renders "Unassigned"
}
type Day = 'Mon' | 'Tue' | 'Wed' | 'Thu' | 'Fri'
type TimetableSlot = {
  id: string; schoolId: string; classGroupId: string
  day: Day; period: number
  subject: string
  teacherId: string | null
}
```

The period grid is a **shared constant** both sides must agree on (period number → time):
1: 08:00–08:45, 2: 08:45–09:30, 3: 09:30–10:15, short break 10:15–10:35, 4: 10:35–11:20, 5: 11:20–12:05, lunch 12:05–12:45, 6: 12:45–13:30, 7: 13:30–14:15.

Read models (server-computed):

```ts
type ClassSummary = ClassGroup & { studentCount: number; formTeacherName: string | null }
type AllocationView = SubjectAllocation & { teacherName: string }     // "Unassigned" fallback
type TimetableSlotView = TimetableSlot & { teacherName: string }
type RegisterRow = { student: Student; status: AttendanceStatus; note: string }  // note '' when none
type AttendanceSession = {          // one submitted register per class per date
  id: string; schoolId: string; classGroupId: string
  date: string
  submittedBy: string; submittedOn: string
  corrections: { by: string; on: string; reason: string }[]
}
type ClassRegister = {
  classGroup: ClassGroup
  date: string
  rows: RegisterRow[]               // full enrolled roster; default status 'present'
  session: AttendanceSession | null // null until submitted for this date
}
type DayScheduleItem = { period: number; start: string; end: string; subject: string; className: string; classGroupId: string }
```

#### Endpoints

| Endpoint | Request | Response | Behavior |
|---|---|---|---|
| `GET /classes` | `page`, `pageSize`, `query`, `level \| 'all'`, `sort: 'name' \| 'size'` | `Paginated<ClassSummary>` | **Viewer-scoped before pagination** (a teacher sees only their classes). `size` = studentCount desc. Roster counts enrolled students matched by class *name*. |
| `GET /classes/:id` | — | `ClassSummary` | 404 "That class could not be found." |
| `GET /classes/levels` | — | `string[]` | Distinct levels, sorted. |
| `GET /classes/:id/roster` | — | `Student[]` | Enrolled only, "lastName firstName" asc. |
| `GET /classes/:id/allocations` | — | `AllocationView[]` | Sorted by subject. |
| `GET /classes/:id/timetable` | — | `TimetableSlotView[]` | — |
| `GET /teachers/:teacherId/day` | `day: Day` | `DayScheduleItem[]` | That teacher's slots for the day, period asc, times from the shared grid. |
| `GET /classes/:id/register` | `date` | `ClassRegister` | Unsubmitted dates default every row to `present`. |
| `PUT /classes/:id/register` | `date`, `{ entries: { studentId; status; note? }[]; reason? }` | `void` | **Class access required** (403 "This class is not assigned to you."). First submission creates the `AttendanceSession`. Re-submission is a **correction**: requires a reason → 422 "This register is already submitted. A correction needs a reason for the record."; appends `{by, on, reason}` to `session.corrections`. Attendance rows upserted per `(studentId, date)`. Optional per-student `note` (trimmed, ≤255 chars) is stored on the row; an empty/absent note clears it. The correction trail lives on the session row — no separate audit event. |

---

### 3.13 Calendar (sessions, terms, campuses)

#### Types

```ts
type CalendarStatus = 'open' | 'closed'
type AcademicSession = {
  id: string; schoolId: string
  name: string                     // "2025/2026" — unique per school
  startsOn: string; endsOn: string
  status: CalendarStatus
  closedOn?: string                // cleared again on reopening
}
type AcademicTerm = {
  id: string; schoolId: string; sessionId: string
  name: 'First' | 'Second' | 'Third'
  startsOn: string; endsOn: string  // must fall inside the session
  status: CalendarStatus
  closedOn?: string
}
type SessionWithTerms = AcademicSession & { terms: AcademicTerm[] }   // terms in First/Second/Third order
type Campus = { id: string; schoolId: string; name: string; address: string; active: boolean }
```

#### Endpoints

| Endpoint | Request | Response | Behavior / errors (422 unless noted) |
|---|---|---|---|
| `GET /calendar/sessions` | — | `SessionWithTerms[]` | Newest session first (name desc). |
| `POST /calendar/sessions` | `{ name, startsOn, endsOn }` | `AcademicSession` | Name must match `YYYY/YYYY` ("Name a session like 2026/2027."). "Both a start and an end date are needed." / "The start date must come before the end date." / "The {name} session already exists." / "Those dates overlap the {other} session." |
| `POST /calendar/sessions/:id/terms` | `{ name, startsOn, endsOn }` | `AcademicTerm` | Session must be open ("This session is closed. Reopen it, with a reason, before changing it."). "Term dates must fall inside the {session} session." / "The {name} term already exists in {session}." |
| `PUT /calendar/terms/:id/dates` | `{ startsOn, endsOn }` | `AcademicTerm` | Session AND term must be open; range + inside-session checks. |
| `POST /calendar/terms/:id/close` | — | `AcademicTerm` | Audit: `term.closed` / `term` / "Closed the {name} term of {session}". |
| `POST /calendar/terms/:id/reopen` | `{ reason }` | `AcademicTerm` | "Only a closed term can be reopened." / "Reopening a closed term needs a reason for the audit history." / session must be open. Clears `closedOn`. Audit: `term.reopened`, with reason. |
| `POST /calendar/sessions/:id/close` | — | `AcademicSession` | All terms must be closed first: "Close the {Second and Third} term(s) first." Audit: `session.closed`. |
| `POST /calendar/sessions/:id/reopen` | `{ reason }` | `AcademicSession` | "Only a closed session can be reopened." / reason required. Terms stay closed. Audit: `session.reopened`, with reason. |
| `GET /calendar/audit` | `page`, `pageSize` | `Paginated<AuditEvent>` | Session/term events only, newest first. |
| `GET /campuses` | — | `Campus[]` | Active first, then name. |
| `POST /campuses` | `{ name, address }` | `Campus` | "The campus needs a name." / "A campus called {name} already exists." (case-insensitive). Created active. |
| `PUT /campuses/:id` | `{ name, address }` | `Campus` | Same checks excluding self. 404 "That campus could not be found." |
| `POST /campuses/:id/active` | `{ active: boolean }` | `Campus` | "A school needs at least one active campus." when deactivating the last one. |

Closed sessions/terms are **immutable reference points** — every write path above checks openness before touching dates.

---

### 3.14 Settings (school profile & accounts)

#### Types

```ts
type SchoolProfileInput = {
  name: string; shortName: string; motto: string; address: string
  logo?: string | null             // null clears it; omit to leave unchanged
}
type UserStatus = 'active' | 'invited' | 'disabled'
type PersonLink =                   // who the account IS
  | { kind: 'teacher'; teacherId: string }
  | { kind: 'parent'; studentIds: string[] }
  | { kind: 'student'; studentId: string }
type SchoolUser = {
  id: string; schoolId: string
  name: string; email: string
  role: Role
  status: UserStatus
  addedOn: string
  link?: PersonLink
  // mock-only fields the real backend must NEVER return: password, inviteCode, reset
}
type InviteUserInput = { name: string; email: string; role: Role; link?: PersonLink }
```

#### Endpoints

| Endpoint | Request | Response | Behavior |
|---|---|---|---|
| `GET /school` | — | `School` | The tenant registry row. |
| `PUT /school` | `SchoolProfileInput` | `School` | Trims fields; logo tri-state as typed. |
| `GET /users` | `page`, `pageSize` | `Paginated<SchoolUser>` | Active → invited → disabled, then name. **Project away credentials** (the mock leaks them; the backend must not). |
| `POST /users/invite` | `InviteUserInput` | `SchoolUser` | Validates the role and its tenant-scoped `PersonLink`. Creates status `invited`, stores only a SHA-256 hash of an ambiguity-free `XXXX-XXXX` code, expires it after 48 hours, and emails the one-time `/join?code=...` link. The raw code is never returned by the API. A delivery failure removes the pending account and returns 503. |
| `PUT /users/:id/role` | `{ role }` | `SchoolUser` | Administrator-only; an administrator may change **other** staff accounts but can never change their own role (403). **Last-administrator guard**: demoting the last active administrator → 422 "A school needs at least one active administrator." |
| `PUT /users/:id/link` | `{ link: PersonLink \| null }` | `SchoolUser` | Administrator-only. Replaces or clears a parent/student link after tenant, role, enrolment, and duplicate-account checks. Teacher links must target an active teacher in the school. |
| `PUT /users/:id/status` | `{ status: 'active' \| 'disabled' }` | `SchoolUser` | Same guard when disabling the last active administrator. |
| `DELETE /users/:id/invite` | — | `void` | 422 "Only a pending invitation can be revoked." Removes the row. |

The `PersonLink` on a user account is what drives **all** parent/teacher/student scoping in §1.4 — it is the join between the auth account and the domain person.

---

### 3.15 Register merge (duplicates)

#### Types

```ts
type MergeParty = {
  id: string; admissionNumber: string; name: string
  classGroup: string; status: StudentStatus
  dateOfBirth: string; guardianName: string; guardianPhone: string; enrolledOn: string
}
type MergeCategory = 'guardians' | 'attendance' | 'academics' | 'enrolments'
  | 'examGrades' | 'assessmentScores' | 'invoices' | 'payments' | 'refunds'
  | 'feeAwards' | 'documents'
type MergeCounts = Record<MergeCategory, number>

type DuplicatePair = {
  id: string                        // "{aId}__{bId}"
  score: number                     // 0.6..1
  reasons: string[]
  a: MergeParty; b: MergeParty
  aRecords: number; bRecords: number
}
type MergePreview = {
  survivor: MergeParty; retired: MergeParty
  moving: MergeCounts; movingTotal: number
  survivorExisting: MergeCounts
}
type MergeResult = { survivorId: string; retiredId: string; moving: MergeCounts; movingTotal: number }
```

#### Duplicate scoring (shared with imports — one definition of "duplicate")

First matching rule wins; below 0.6 (or null) is not a candidate:
same admission number → 1.0; same name + same DOB → 0.95; same name + same guardian phone → 0.9; near-name (same last name, first-name edit distance ≤ 1) + same DOB + same phone → 0.85; same name alone → 0.6; same DOB + same phone → 0.5 ("may be a sibling"). Name/phone comparison is normalized (case/punctuation-insensitive; phone = last 10 digits). A DOB alone or a phone alone is never a match.

#### Endpoints

Role gate on all three: `administrator`/`registrar` only → 403 "Merging records needs a registrar or administrator." (a bursar is refused).

| Endpoint | Request | Response | Behavior |
|---|---|---|---|
| `GET /merge/candidates` | `page`, `pageSize` | `Paginated<DuplicatePair>` | All-pairs scoring over the register, score ≥ 0.6, sorted score desc. |
| `GET /merge/preview` | `survivorId`, `retiredId` | `MergePreview` | 422 "Choose two different records to merge." 404 "That person is no longer on the register." Read-only counts. |
| `POST /merge` | `{ survivorId, retiredId, reason }` | `MergeResult` | Reason required: 422 "Record why these two records are the same person." **One transaction**: repoint `studentId` on guardians (demoted to non-primary), attendance, academics, enrolments, examGrades, assessmentScores, invoices, payments, refunds, feeAwards; repoint student-owned documents' ownerId; repoint application `studentId`; fix user `PersonLink`s (student links and parent ward lists, de-duplicated); re-sync the survivor's primary contact; delete the retired row (its id is never reused). Audit: `student.merged` / `student` / survivorId, with a full plain-language summary of what was moved and a snapshot of the retired record's identifying fields, plus the reason. |

---

### 3.16 Promotion (session rollover)

#### Types

```ts
type PromotionDecision = 'promote' | 'repeat' | 'graduate' | 'withdraw'
type PromotionRow = {
  studentId: string; studentName: string; admissionNumber: string
  currentClass: string; currentLevel: string
  average: number | null            // released-result average for the closing session
  hasResult: boolean
  suggested: PromotionDecision      // the rule's proposal; registrar may override
  nextClass: string | null          // null for graduate/withdraw
}
type PromotionPreview = {
  fromSession: string; toSession: string
  passMark: number
  alreadyPromoted: boolean
  rows: PromotionRow[]
}
type PromotionCommitInput = {
  fromSession: string; toSession: string; passMark: number
  decisions: { studentId: string; decision: PromotionDecision }[]
}
type PromotionResult = { toSession: string; promoted: number; repeated: number; graduated: number; withdrawn: number }
```

#### Rules

- Levels ladder: JSS 1 → JSS 2 → JSS 3 → SSS 1 → SSS 2 → SSS 3; next class keeps the stream letter ("JSS 1A" → "JSS 2A"). Next session name = both year halves + 1.
- Suggested decision: no released result → benefit of the doubt (promote); average < passMark (default 40) → repeat; passed at SSS 3 → graduate; else promote.
- Released average = from the closing session's latest published exam where the student has fully-marked rows, mean of (ca+exam), 1 dp.

#### Endpoints

| Endpoint | Request | Response | Behavior |
|---|---|---|---|
| `GET /promotion/preview` | `passMark?` (default 40) | `PromotionPreview` | 422 "There is no academic session to promote from." when no session exists. Rows = enrolled students with a placement in the closing session, sorted class then name. |
| `POST /promotion/commit` | `PromotionCommitInput` | `PromotionResult` | **Idempotency**: any enrolment already in `toSession` → 409 "Students have already been promoted to {toSession}. Promotion runs once per session." Per decision: skip silently if the student is missing/not enrolled/has no active closing enrolment. A promote with no next level is coerced to graduate. Closing enrolment → `completed` + endedOn + outcome + recomputed average; promote/repeat additionally set `promotedTo` and create the next session's active enrolment (startedOn = 1 September of the new session's first year). **`Student.classGroup` is deliberately NOT changed** — the live placement moves only when the session actually rolls over. graduate/withdraw set the student's status. Audit per student: `student.promoted` / `student.repeated` / `student.graduated` / `student.withdrawn` with plain-language summaries. |

---

### 3.17 CSV imports (staged, duplicate-reviewed)

#### Types

```ts
type ImportKind = 'students' | 'guardians'
type RowCheck = 'valid' | 'invalid' | 'duplicate'
type RowDecision = 'undecided' | 'import' | 'merge' | 'skip'
type RowOutcome = 'created' | 'merged' | 'skipped' | 'rejected'

type ImportBatch = {
  id: string; schoolId: string
  kind: ImportKind
  filename: string
  uploadedBy: string; uploadedOn: string
  sourceRowCount: number            // every row must be accounted for
  ignoredColumns: string[]
  status: 'review' | 'committed' | 'discarded'
  committedOn?: string
  result?: { created: number; merged: number; skipped: number; rejected: number }
}
type ImportRow = {
  id: string; batchId: string; schoolId: string
  lineNumber: number                // 1-based line in the original file
  values: Record<string, string>
  check: RowCheck
  issues: { column: string; message: string }[]
  matches: {                        // top 3, score desc
    targetId: string                // existing record id, or "row:<line>" within-file
    targetLabel: string
    score: number; reasons: string[]
    withinFile: boolean
  }[]
  decision: RowDecision
  mergeTargetId?: string
  outcome?: RowOutcome
  resultId?: string
  outcomeNote?: string
}
type ImportPreview = { batch: ImportBatch; rows: ImportRow[] }
```

File columns (header names normalize case/punctuation to snake_case):
- **students**: `admission_number?`, `first_name`, `last_name`, `date_of_birth` (YYYY-MM-DD), `gender`, `class_group` (must match an existing class), `status?` (blank = enrolled), `guardian_name`, `guardian_phone`, `enrolled_on?` (blank = today).
- **guardians**: `student_admission_number`, `first_name`, `last_name`, `relationship`, `phone`, `email?`, `occupation?`, `is_primary?` (yes/no).

Validation messages are field-specific plain language (e.g. "Write the date of birth as YYYY-MM-DD, for example 2012-04-09.", "The school has no class called \"X\". Create the class first, or correct the spelling.", "No student on the register has that admission number.", "That does not look like a phone number."). Rows with issues never get duplicate matches (an unreadable row is never also a duplicate question).

#### Endpoints

| Endpoint | Request | Response | Behavior |
|---|---|---|---|
| `GET /imports/template` | `kind` | `{ filename; content }` | Commented CSV template (lines starting `#` are ignored on upload). |
| `GET /imports` | `page`, `pageSize` | `Paginated<ImportBatch>` | uploadedOn desc. |
| `GET /imports/:batchId` | — | `ImportPreview` | 404 "That import could not be found." Rows by lineNumber. |
| `POST /imports` | `{ kind, filename, text }` | `ImportPreview` | Parses CSV/TSV (quoted fields, `#` comments, blank lines skipped). 422 "That file has no heading row. Start from the template." / "The file is missing these columns: {labels}. Start from the template." / "That file has a heading row but no records under it." Per row: check → issues; duplicate matching against the register AND earlier rows of the same file. Default decisions: valid→import, invalid→skip, duplicate→undecided. **Nothing touches the register on upload.** |
| `PUT /imports/:batchId/rows/:rowId/decision` | `{ decision, mergeTargetId? }` | `ImportRow` | 409 "This import has already been committed." 422 "A row with errors cannot be imported. Correct the file and upload it again." / "Choose which existing record this row belongs to." / "That match is another row of this same file, which does not exist yet. Skip one of the two rows instead." |
| `POST /imports/:batchId/skip-flagged` | — | `number` | Skips every undecided duplicate; returns how many. |
| `POST /imports/:batchId/commit` | — | `ImportPreview` | 422 "{n} row(s) still need a decision before this file can be committed." Then per row: invalid→rejected; skip→skipped; merge→**non-destructive field update** (a blank cell never erases existing data; each real change listed in the outcome note; audit `import.merged` per merged row); import→created (students get the next admission number when blank; guardians attach by admission number, primary handling + contact re-sync as §3.10). Batch → `committed` with the result tally (sums to sourceRowCount). Audit: `import.committed` / `import` with the full tally summary. |
| `POST /imports/:batchId/discard` | — | `void` | 409 "A committed import cannot be discarded." Batch → `discarded`; **staged rows are deleted** (they hold real names). Audit: `import.discarded`. |

---

### 3.18 Auth & session

The auth endpoints are **tenant-less** — they resolve the tenant. `AuthResult = { user: SchoolUser; school: School }` (project credentials off `user` in the real backend).

| Endpoint | Request | Response | Behavior |
|---|---|---|---|
| `POST /auth/sign-in` | `{ email, password }` | `AuthResult` | 401 "Incorrect e-mail or password." (unknown email or wrong password — same message). 403 "This account has a pending invitation. Redeem your invite code to finish setting up." / "This account has been disabled. Contact the school administrator." E-mail is globally unique across tenants. |
| `POST /auth/register-school` | `{ school: SchoolProfileInput; admin: { name; email; password } }` | `AuthResult` | Slug derived from the school name. Requires school name, short name, administrator name, valid e-mail, and password of at least 8 characters. Enforces globally unique e-mail and school slug, then creates the tenant and first active administrator in one transaction. |
| `POST /auth/invite/lookup` | `{ code }` | `AuthResult` | 422 "Enter your invite code." 404 "That invite code was not recognised. Check it with your school." The normalized code is hashed for lookup and must be unexpired. |
| `POST /auth/invite/accept` | `{ code, password }` | `AuthResult` | Same 404; password ≥ 8. Activates the account and burns both the invite hash and expiry. |
| `POST /auth/reset/request` | `{ email }` | `{ sent: true }` | **Never reveals whether the address exists.** Issues a 30-minute single-use code delivered out of band (the mock's `demoCode` field is dropped in production). |
| `POST /auth/reset/confirm` | `{ email, code, password }` | `AuthResult` | One message for every failure: 400 "That reset code is not valid any more. Ask for a new one and try again." Password ≥ 8. Burns the code; also reactivates an invited account. |

Invitation delivery reads `EMS_FRONTEND_URL` for the public React origin and `EMS_EMAIL_FROM` for the sender. The same frontend URL is the default exact CORS origin, so one backend setting connects invitation links and browser access. `EMS_CORS_ORIGINS` is an optional comma-separated override only when multiple React origins are intentional. Configure CakePHP's `EmailTransport.default` SMTP host, port, username, password, and TLS settings through deployment environment configuration. Separately hosted production applications must use HTTPS; custom frontend and API subdomains under the same site are recommended so browser refresh-cookie restrictions do not interrupt sessions.

**Session semantics the backend must support:**
- The session/token carries `{ userId, name, role, activeSchoolId, memberships: {schoolId, role}[] }`. A normal account has exactly one membership.
- The URL slug is the tenant source of truth: the app resolves `/app/:schoolSlug` → school, checks membership, and scopes every subsequent call to that school id. Backend needs `GET /schools/by-slug/:slug` (or the resolution folded into sign-in) plus a membership check on every request.
- The frontend **clears its entire cache** whenever userId/role/activeSchoolId changes — the backend must treat every response as private (no shared HTTP caching; `Cache-Control: private, no-store` on API responses).
- Passwords: mock stores plaintext — the backend obviously stores hashes, keeps the same validation surface (min 8 chars message), and rate-limits sign-in and reset.

---

### 3.19 Portal (parent/student home)

```ts
type PortalIdentity = {
  user: SchoolUser | null           // project credentials away
  teacher: Teacher | null           // teacher accounts
  wards: Student[]                  // parent accounts
  student: Student | null           // student accounts
}

type WardOverview = {
  student: Student
  attendance: { rate: number; absences: number; windowDays: number }   // last 20 school days
  fees: { balance: number; overdueCount: number }
  latestResult: {
    examId: string; examTitle: string; session: string
    average: number; subjects: number
    grade: string                   // letter, graded on the exam's pinned scheme
  } | null
  upcomingSittings: (ExamSchedule & { examTitle: string })[]           // next 5 for the ward's level
}
```

| Endpoint | Request | Response | Behavior |
|---|---|---|---|
| `GET /portal/identity` | — | `PortalIdentity` | Resolves the signed-in account's `PersonLink` to the person/wards. Never errors. |
| `GET /portal/wards/:studentId` | — | `WardOverview` | Student access (403 family message). Attendance window = the last 20 distinct register dates school-wide; rate counts present+late. Fees derive from the same net-paid rule as §3.7 (completed − processed refunds). Latest result = most recent published exam with fully-marked rows for the ward. |

---

### 3.20 Communication (announcements, delivery, consent, alerts)

#### Types

```ts
type Audience = 'everyone' | 'teachers' | 'parents' | 'students'
type AnnouncementCategory = 'general' | 'academic' | 'fees' | 'event' | 'urgent'
type Announcement = {
  id: string; schoolId: string
  title: string; body: string       // plain text, blank-line paragraphs
  audience: Audience; category: AnnouncementCategory
  status: 'draft' | 'published'
  authorName: string
  createdOn: string; publishedOn?: string
  pinned: boolean
}

type NotificationChannel = 'email' | 'sms'
type NotificationKind = 'fee_reminder' | 'attendance_alert' | 'result_alert' | 'announcement' | 'general'
type NotificationMessage = {        // the outbound log
  id: string; schoolId: string
  channel: NotificationChannel; kind: NotificationKind
  subject: string; body: string
  audienceLabel: string             // "All parents", "Parents of JSS 1A"
  recipientCount: number
  sentOn: string; sentBy: string
}

type MessagePurpose = 'transactional' | 'school_news'
type RecipientStatus = 'queued' | 'sent' | 'failed' | 'suppressed'
type MessageRecipient = {           // one person, one channel, one attempt trail
  id: string; schoolId: string; announcementId: string
  personId: string; personName: string
  personKind: 'guardian' | 'teacher'
  aboutStudentName?: string
  channel: NotificationChannel
  address: string                   // MASKED — the full address never leaves the contact record
  status: RecipientStatus
  attempts: number                  // max 3
  providerRef?: string; failureReason?: string; suppressedReason?: string
  updatedOn: string
}

type DeliveryReport = {
  announcementId: string
  status: 'not_sent' | 'queued' | 'sent' | 'partly_failed'
  channel: NotificationChannel | null
  total: number; sent: number; failed: number; queued: number; suppressed: number
  needsFollowUp: number             // failed with all 3 attempts used
  recipients: MessageRecipient[]    // failures first
}

type AudiencePreview = {
  audience: Audience; purpose: MessagePurpose; channel: NotificationChannel
  total: number; reachable: number; suppressed: number; unreachable: number
  sample: { personName: string; address: string; aboutStudentName?: string }[]  // first 5, masked
}

type ContactPreference = {
  id: string; schoolId: string
  personId: string; personName: string
  channel: NotificationChannel
  purpose: MessagePurpose
  enabled: boolean
  source: string                    // "Portal", a form, the school office
  recordedOn: string; withdrawnOn?: string
}

type Alert = {                      // derived fresh on every read, never stored
  id: string; kind: 'fee_overdue' | 'attendance' | 'result_published'
  severity: 'destructive' | 'warning' | 'info'
  title: string; detail: string; count: number
  audienceLabel: string
}
```

#### Delivery rules (replicate exactly)

- **Audience resolution** (one routine for preview AND send, so they can never disagree): non-teacher audiences resolve to the **primary guardian of each enrolled student** (a household is contacted once — `parents`, `students`, and the family half of `everyone` reach the same people); `teachers`/`everyone` add active teachers. Address = email or phone per channel.
- **Consent**: `transactional` always sends. `school_news` requires an explicit enabled preference — a missing record means **no** ("silence is never a yes"): suppressed with reason "No consent recorded for school news" / "Consent withdrawn" / "Opted out".
- **Masking**: stored/displayed addresses are masked (2 chars + dots for email local part; first 5 + last 3 for phones).
- **Attempts**: max 3 per recipient; a failure that has used all attempts is `needsFollowUp` and is never auto-retried.
- **One send per announcement, ever**: a second delivery attempt → 409 "This announcement has already been sent."

#### Endpoints

| Endpoint | Request | Response | Behavior |
|---|---|---|---|
| `GET /announcements` | `page`, `pageSize`, `query`, `audience \| 'all'`, `status \| 'all'` | `Paginated<Announcement>` | Newest (publishedOn ?? createdOn) first. |
| `GET /announcements/:id` | — | `Announcement` | 404 "That announcement could not be found." Non-staff readers may only open published announcements addressed to them. |
| `POST /announcements` | `{ data: AnnouncementInput; publish: boolean }` | `Announcement` | Draft or immediate publish. Publishing does NOT deliver. |
| `POST /announcements/:id/publish` | — | `Announcement` | 422 "This announcement is already published." |
| `GET /announcements/feed` | `page`, `pageSize`, `audience` (from role) | `Paginated<Announcement>` | Published only; `everyone` + own audience; **pinned first**, then newest — pinning is a sort key, so pinned items lead page 1. |
| `GET /announcements/audience-preview` | `audience`, `purpose`, `channel` | `AudiencePreview` | Counts + 5-sample, masked. This previews an audience before or after an announcement is saved. |
| `POST /announcements/:id/deliver` | `{ channel, purpose }` | `DeliveryReport` | 422 "Publish this announcement before sending it out." 409 one-send rule. Writes one `MessageRecipient` per resolved person (suppressed rows record why: no address on file / consent), attempts delivery once each. Then appends a `NotificationMessage` log entry (kind `announcement`, body = first paragraph, recipientCount = successes). |
| `GET /announcements/:id/delivery` | — | `DeliveryReport` | Never-sent → `not_sent` with empty counters. |
| `POST /announcements/:id/delivery/retry` | — | `DeliveryReport` | Retries failures with attempts < 3; 422 "Nothing here can be retried. Remaining failures have used every attempt and need a person to follow up." |
| `GET /notifications` | `channel \| 'all'`, `kind \| 'all'`, `page`, `pageSize` | `Paginated<NotificationMessage>` | sentOn desc. |
| `GET /me/preferences` | — | `{ personName; preferences: ContactPreference[] } \| null` | null for staff-office accounts with no contact record. |
| `PUT /me/preferences` | `{ channel, purpose, enabled }` | `ContactPreference` | 404 "No contact record is linked to this account." 422 "Notices about your own ward cannot be switched off. Speak to the school office if the contact details are wrong." Withdrawal stamps `withdrawnOn` — consent history is kept, not deleted. |
| `GET /alerts` | — | `Alert[]` | Derived: overdue invoices (severity destructive), students with 3+ absences in 14 days (warning), latest published results (info). |
| `POST /alerts/send` | `{ kind, channel }` | `NotificationMessage` | Recomputes server-side; stale → 422 "There is nothing to send for this alert any more." Resolves only affected guardian households, applies channel and consent rules, creates recipient rows tied to the notification, and records the actual successful count. Provider transport is still simulated. |

---

### 3.21 Reports (async CSV exports)

#### Types

```ts
type ReportType = 'attendance_summary' | 'admissions_conversion' | 'class_list'
  | 'grade_distribution' | 'fee_ageing' | 'collections' | 'reconciliation_status'
type ReportFilters = { from?: string; to?: string; classGroup?: string; term?: string; campus?: string }
type ReportJobStatus = 'queued' | 'running' | 'ready' | 'failed' | 'expired'
type ReportJob = {
  id: string; schoolId: string
  reportType: ReportType
  requestedBy: string; requestedOn: string
  filters: ReportFilters
  status: ReportJobStatus
  storagePath?: string              // private bucket; never a public URL
  rowCount?: number
  readyOn?: string
  expiresOn?: string                // file deleted after this (7-day retention)
  error?: string
  downloads: { by: string; at: string }[]
}
type ReportFile = { filename: string; content: string; rowCount: number }
```

Definitions with role gates (server-enforced, not just menu filtering): attendance_summary + admissions_conversion + class_list + grade_distribution → administrator/registrar; fee_ageing + collections + reconciliation_status → administrator/bursar. `class_list` and `reconciliation_status` are **row-level** (name individual people) — their files carry a `# CONFIDENTIAL` header line.

#### Endpoints

| Endpoint | Request | Response | Behavior |
|---|---|---|---|
| `GET /reports/jobs` | `page`, `pageSize` | `Paginated<ReportJob>` | Newest first. The frontend polls this every **800 ms while any job on the current page is queued/running** — a real backend runs jobs on a worker and this endpoint reflects live status. |
| `POST /reports/jobs` | `{ reportType, filters }` | `ReportJob` | 403 "Your role cannot run the {title} report." Queued for the worker. |
| `POST /reports/jobs/:id/download` | — | `ReportFile` | Permission **re-checked at download**. 410 "This export has expired and its file has been deleted. Run the report again." 409 "This report is not ready yet." Rows are generated against current data; each download appends to the job's download log; audit: `report.downloaded` / `report` / "Downloaded the {title} export ({n} rows)". Filename: `{reportType}-{requestedOn}.csv`. |

Every CSV opens with provenance comment lines (school, requested by/on, generated/expiry dates, CONFIDENTIAL marker for row-level types, applied filters). Report bodies: attendance by class ((present+late)/total rate), admissions funnel by status, class list with primary guardian, grade distribution bucketed on each exam's **pinned** bands, fee ageing over instalment-aware outstanding pieces (Not yet due / 1–30 / 31–60 / Over 60 days), collections by class, reconciliation = provider-channel payments. **Money cells in CSV bodies are emitted in Naira (kobo ÷ 100)** so the files read naturally in a spreadsheet — the one surface where amounts leave the system in Naira.

---

### 3.22 Analytics (derived dashboards)

Four read-only endpoints, no storage, staff-only. All money figures use the shared net-paid rule; all grade figures use the exam's pinned scheme.

| Endpoint | Response (shape) | Key computation rules |
|---|---|---|
| `GET /analytics/overview` | `SchoolOverview` | Attendance rate over the last 20 distinct register dates, trend vs the 20 before; collection rate for the current term with per-invoice paid **capped at total**; overdue counted across all non-cancelled invoices; latest published exam average + its pinned bands. |
| `GET /analytics/attendance` | `AttendanceInsights` | 20-day window; per-day rates (oldest→newest); per-class table sorted worst-rate first; frequently-absent = 3+ absences in window. |
| `GET /analytics/academics` | `AcademicPerformance` | Latest published exam; per-subject and per-class averages (desc); grade distribution from **per-student averages** with one row per band including zeros; top 5 students. |
| `GET /analytics/enrolment` | `EnrolmentTrends` | By-level enrolled vs capacity with gender split; intake by enrolment year; status counts. |

```ts
type SchoolOverview = {
  enrolled: number; applicants: number; activeTeachers: number
  studentTeacherRatio: number; classCount: number
  attendanceRate: number; attendanceTrend: number
  collectionRate: number; outstanding: number; overdueInvoices: number
  latestExam: { id; title; session; term; average } | null
  gradeBands: GradeBand[]
}
type AttendanceInsights = {
  windowDays: number; overallRate: number
  days: { date; rate; present; total }[]
  byClass: { classGroup; rate; present; late; absent; total }[]
  frequentlyAbsent: { studentId; name; classGroup; absences }[]
}
type AcademicPerformance = {
  exam: { id; title; session; term } | null
  overallAverage: number; gradedEntries: number
  bySubject: { subject; average; entries }[]
  byClass: { classGroup; average; students }[]
  distribution: { letter; label; count; share }[]
  topStudents: { studentId; name; classGroup; average }[]
  gradeBands: GradeBand[]
}
type EnrolmentTrends = {
  enrolled: number
  byLevel: { level; enrolled; capacity; female; male }[]
  intakeByYear: { year; count }[]
  genderSplit: { female: number; male: number }
  statuses: { enrolled; applicant; graduated; withdrawn }
}
```

---

### 3.23 Audit trail & privacy requests

Administrator-only module.

```ts
type AuditListParams = { entityType: AuditEvent['entityType'] | 'all'; query: string; page: number; pageSize: number }

type PrivacyRequestKind = 'access' | 'correction' | 'export' | 'deletion'
type PrivacyRequestStatus = 'received' | 'verified' | 'approved' | 'refused' | 'fulfilled'
type PrivacyRequest = {
  id: string; schoolId: string
  reference: string                 // "PRV-0003"
  kind: PrivacyRequestKind
  subjectName: string; subjectStudentId?: string
  requestedBy: string; contact: string
  requestedOn: string; detail: string
  status: PrivacyRequestStatus
  identityVerifiedBy?: string; identityVerifiedOn?: string; identityEvidence?: string
  decidedBy?: string; decidedOn?: string; decisionNote?: string
  fulfilledOn?: string; fulfilmentNote?: string
  retentionNote?: string            // approved deletions: what must be kept regardless
}
```

| Endpoint | Request | Response | Behavior |
|---|---|---|---|
| `GET /audit/events` | `AuditListParams` | `Paginated<AuditEvent>` | Filter by entityType; query over actor/summary/action; newest first. **Append-only — no update/delete exists anywhere.** |
| `GET /privacy-requests` | `page`, `pageSize` | `Paginated<PrivacyRequest>` | requestedOn desc. |
| `POST /privacy-requests` | `{ kind, subjectName, subjectStudentId?, requestedBy, contact, detail }` | `PrivacyRequest` | Reference = next "PRV-{0001}" sequence. Audit: `privacy_request.logged`. |
| `POST /privacy-requests/:id/verify` | `{ evidence }` | `PrivacyRequest` | 409 "This request has already moved past identity checks." 422 "Record how the requester proved who they are." Audit: `privacy_request.identity_verified`, reason = evidence. |
| `POST /privacy-requests/:id/decide` | `{ approve, note, retentionNote? }` | `PrivacyRequest` | 409 "Verify who is asking before deciding. A record must never go to the wrong person." 422 "Record the reason for this decision." Approved deletions additionally require: 422 "Name what must be kept regardless. Financial and academic records have their own retention rules." Audit: `privacy_request.approved` / `.refused`. |
| `POST /privacy-requests/:id/fulfil` | `{ note }` | `PrivacyRequest` | 409 "Only an approved request can be marked fulfilled." 422 "Record what was handed over or changed." Audit: `privacy_request.fulfilled`. |

State machine: `received → verified → approved | refused`; `approved → fulfilled`. Deciding never deletes/exports anything itself — it records the decision; fulfilment records what was actually done.

---

### 3.24 Incidents (breach register, responder-sealed)

```ts
type IncidentSeverity = 'low' | 'medium' | 'high' | 'critical'
type IncidentStatus = 'recorded' | 'contained' | 'investigating' | 'reported' | 'closed'
// Strictly one legal step forward: recorded→contained→investigating→reported→closed. No skips, no reopening.

type IncidentDataCategory = 'student_records' | 'guardian_contacts' | 'financial'
  | 'health' | 'credentials' | 'staff_records' | 'documents'

type IncidentResponder = { userId: string; name: string; addedOn: string; addedBy: string; lead?: boolean }
type IncidentEntry = { id: string; at: string; actor: string; kind: IncidentStatus | 'note' | 'responder_added'; note: string }

type Incident = {
  id: string; schoolId: string
  reference: string                 // "INC-0001"
  title: string; description: string
  severity: IncidentSeverity
  dataCategories: IncidentDataCategory[]
  status: IncidentStatus
  discoveredOn: string; recordedOn: string; recordedBy: string
  responders: IncidentResponder[]
  containmentNote?: string          // set at contained
  reportEvidence?: string           // set at reported
  closeSummary?: string             // set at closed
  entries: IncidentEntry[]
}

type IncidentSummary = {            // the register row — deliberately detail-free
  id: string; reference: string; title: string
  severity: IncidentSeverity; status: IncidentStatus
  dataCategoryCount: number         // count only, not the categories
  responders: { userId: string; name: string; lead: boolean }[]
  viewerIsResponder: boolean
  discoveredOn: string; recordedOn: string
}
type IncidentDetail = Incident & { candidates: { userId: string; name: string }[] }  // addable admins
```

**The sealing rule** (tighter than role): the register list shows that cases exist, but **detail is readable only by the responders named on that case** — an administrator who is not named is refused with 403 "Incident detail is restricted to the responders named on this case." The recorder becomes the lead responder at creation, so every case has an owner from the first instant.

| Endpoint | Request | Response | Behavior |
|---|---|---|---|
| `GET /incidents` | `page`, `pageSize` | `Paginated<IncidentSummary>` | recordedOn desc. Description/categories/entries never appear in the list projection. |
| `GET /incidents/:id` | — | `IncidentDetail` | 404 before 403 (a non-responder asking for a missing id gets 404). Responder check as above. |
| `POST /incidents` | `{ title, description, severity, dataCategories, discoveredOn }` | `Incident` | 422s: "Give the incident a short title." / "Describe what happened, as far as it is known." / "Name at least one category of data that may be affected." / "Record when the incident was discovered." Recorder = lead responder; first entry kind `recorded`. Audit: `incident.recorded` ("Recorded {ref} ({severity} severity)"). |
| `POST /incidents/:id/advance` | `{ to, note }` | `Incident` | Responder-only. 409 "A {current} incident cannot move to {target}." (one step only). 422 "Record what was done at this step before moving the case on." Stores the step note on the matching field (containmentNote/reportEvidence/closeSummary) + the trail. Audit: `incident.contained` / `.investigating` / `.reported` / `.closed`, reason = note. |
| `POST /incidents/:id/notes` | `{ note }` | `Incident` | Responder-only. 409 "This case is closed. Its record can no longer be added to." |
| `POST /incidents/:id/responders` | `{ userId }` | `Incident` | Responder-only (only someone inside can widen the circle). 409 "That person is already a responder on this case." 422 "A responder must be an active administrator at this school." No remove operation exists. Audit: `incident.responder_added`. |

---

## 4. Complete audit action catalog

Every mutation that must write an audit event, by action key (entityType in parentheses):

`session.closed`, `session.reopened` (session) · `term.closed`, `term.reopened` (term) · `results.released`, `results.reopened` (exam) · `assessment.created`, `assessment.opened`, `assessment.reopened`, `assessment.closed`, `assessment.published` (assessment) · `grading.updated` (grading_scheme) · `fee_award.granted`, `fee_award.ended` (fee_award) · `invoice.rescheduled` (invoice) · `refund.requested`, `refund.processed`, `refund.rejected` (refund) · `document.uploaded`, `document.verified`, `document.rejected`, `document.removed`, `document.downloaded`, `document.link_refused` (document) · `student.merged`, `student.promoted`, `student.repeated`, `student.graduated`, `student.withdrawn` (student) · `import.merged` (student/guardian), `import.committed`, `import.discarded` (import) · `report.downloaded` (report) · `privacy_request.logged`, `privacy_request.identity_verified`, `privacy_request.approved`, `privacy_request.refused`, `privacy_request.fulfilled` (privacy_request) · `incident.recorded`, `incident.contained`, `incident.investigating`, `incident.reported`, `incident.closed`, `incident.responder_added` (incident).

Deliberately NOT audited in the frontend contract (the record lives elsewhere): admissions reviews (the review table is the trail), attendance corrections (on the session row), exam/schedule/paper/grade/score saves, invoice issue/cancel, payment record/reverse, checkout, announcements. The backend may add audit coverage to these (additive, non-breaking) — §3.9 recommends it for payment reversal and invoice cancellation.

---

## 5. Phased refactor plan

Refactor the live backend **one phase at a time**; each phase ends with the frontend module(s) swapped onto the real endpoints and verified before the next begins. Within a phase: migrate the schema, build the endpoints to this contract, backfill/transform existing data, then point the frontend's service file at the real URLs (the swap is per-service-file, so partially migrated states are fine — some services on mock, some real).

**Phase 0 — Platform.** Tenancy (`schools` + `school_id` everywhere + RLS/mandatory scoping), auth (§3.18: sign-in, school registration, invites, resets; token carries the viewer claims), the shared plumbing: `Paginated<T>` envelope, `{message}` error bodies with the documented statuses, audit-event writer, private storage buckets + signed-grant flow (§1.7), and the scope policy module (§1.4) as reusable middleware. **Exit check**: sign-in/invite/reset flows work against the real backend; a parent token cannot read another family's student by id.

**Phase 1 — Domain core.** Students, guardians (primary-contact sync), teachers, classes/allocations/timetable, calendar (sessions/terms/campuses with close/reopen audit), settings (profile, users, last-admin guard). **Exit check**: register lists paginate/filter/sort identically; closed-term immutability enforced; calendar audit trail visible.

**Phase 2 — Admissions & documents.** Public apply (rate-limited), application state machine, review trail, enrolment transaction (student+guardian+documents+review), document upload/verify/delete, signed links with re-checked access at redemption. **Exit check**: full applicant→enrolled journey against the real backend; an expired link reports 410-expired, a stranger's link 403.

**Phase 3 — Academics.** Exams/schedules/papers/questions, assessments + the CA derivation, grading schemes (versioned, no-op guard), gradesheet/broadsheet/report card computations, release/reopen with scheme pinning, transcripts. **Exit check**: a report card rendered from the real backend is numerically identical to the mock for the same seed data (averages 1 dp, competition ranking, pinned bands); editing the scale after a release does not change the released card.

**Phase 4 — Finance.** Fee structures, awards (apply-at-issue), invoices + instalments + reschedule history, office payments, **Paystack checkout with webhook-driven confirm (idempotent)**, receipts, refund workflow (separation of duties by user id), reconciliation, ledger, fees report. **Exit check**: net-paid rule verified (pending/failed/reversed payments and pending/rejected refunds move nothing); instalment waterfall re-settles after a reschedule; a replayed webhook is a no-op.

**Phase 5 — Portals, comms, reporting, governance.** Portal identity/ward overview, announcements + consent-aware delivery + retry, contact preferences, alerts, report jobs on a real worker + expiring files, analytics endpoints, audit event list, privacy request workflow, incident register with responder sealing. **Exit check**: school-news suppression honored for a person with no consent record; incident detail refused to a non-responder administrator; report file expires and its download 410s.

Ordering rationale: each phase only depends on tables from earlier phases (academics needs students/classes/calendar; finance needs students; portals need everything), so the frontend can run mixed mock/real throughout the migration.

---

## 6. Parity verification checklist (run per phase)

1. **Shape diff**: for each endpoint, capture the mock response and the real response for the same inputs and diff the JSON structure (field names, optionality, union values). The frontend's TypeScript types in `src/features/*/types.ts` are the arbiter.
2. **Envelope**: every list returns `{ items, total, page, pageSize }` with post-filter totals and 1-based pages.
3. **Errors**: trigger each documented failure and compare status + message text (messages are shown to users verbatim — they are part of the contract).
4. **Scoping**: for each role (administrator, registrar, bursar, teacher, parent, student), verify list narrowing and detail 403s with the canonical wordings.
5. **Derived numbers**: seed both sides with identical data and compare report cards, broadsheets, invoice balances, ledgers, analytics payloads number-for-number (rounding rules: derived CA whole-number; averages 1 dp; rates raw 0..1 fractions).
6. **Audit**: after each mutation in §4's catalog, confirm exactly one event with the documented action/entityType/summary/reason.
7. **Swap test**: point the module's `src/services/*.service.ts` at the real base URL and run the affected screens — zero changes to hooks, query keys, types, or screens is the definition of done.
