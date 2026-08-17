<?php
declare(strict_types=1);

namespace App\Ems;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Locator\LocatorInterface;
use DateTimeImmutable;
use DateTimeZone;

/**
 * The CSV import engine (document.md §3.17). A file lands in a STAGING batch and
 * every row is checked; nothing reaches the register until a person commits it.
 * A row that looks like someone already on the register (or listed earlier in
 * the same file) is HELD, not written — the reviewer decides new person, same
 * person, or skip. Committing accounts for every source row as created / merged
 * / skipped / rejected, and those four sum to the row count the file arrived
 * with. Duplicate detection shares one definition with the register merge
 * (App\Ems\Dedup).
 */
class Imports
{
    /** kind => [title, description, columns[{key,required,hint}]]. */
    public const DEFINITIONS = [
        'students' => [
            'title' => 'Students',
            'description' => 'One row per student, with the household contact the school already uses for them.',
            'columns' => [
                ['key' => 'admission_number', 'required' => false, 'hint' => 'Leave blank and the school allocates the next number.'],
                ['key' => 'first_name', 'required' => true, 'hint' => 'Given name.'],
                ['key' => 'last_name', 'required' => true, 'hint' => 'Family name.'],
                ['key' => 'date_of_birth', 'required' => true, 'hint' => 'Written as YYYY-MM-DD, for example 2012-04-09.'],
                ['key' => 'gender', 'required' => true, 'hint' => 'female, male or other.'],
                ['key' => 'class_group', 'required' => true, 'hint' => 'Must match a class the school already has, for example JSS 1A.'],
                ['key' => 'status', 'required' => false, 'hint' => 'enrolled, applicant, graduated or withdrawn. Blank means enrolled.'],
                ['key' => 'guardian_name', 'required' => true, 'hint' => 'The parent or guardian the school contacts first.'],
                ['key' => 'guardian_phone', 'required' => true, 'hint' => 'Any readable format, for example +234 803 1234567.'],
                ['key' => 'enrolled_on', 'required' => false, 'hint' => 'YYYY-MM-DD. Blank means today.'],
            ],
        ],
        'guardians' => [
            'title' => 'Guardians',
            'description' => 'One row per guardian, attached to a student already on the register.',
            'columns' => [
                ['key' => 'student_admission_number', 'required' => true, 'hint' => 'The admission number of the student this guardian belongs to.'],
                ['key' => 'first_name', 'required' => true, 'hint' => 'Given name.'],
                ['key' => 'last_name', 'required' => true, 'hint' => 'Family name.'],
                ['key' => 'relationship', 'required' => true, 'hint' => 'mother, father, guardian, sibling or other.'],
                ['key' => 'phone', 'required' => true, 'hint' => 'Any readable format.'],
                ['key' => 'email', 'required' => false, 'hint' => 'Used for portal access later.'],
                ['key' => 'occupation', 'required' => false, 'hint' => 'Free text.'],
                ['key' => 'is_primary', 'required' => false, 'hint' => 'yes or no. Only one guardian per student can be the first contact.'],
            ],
        ],
        'staff' => [
            'title' => 'Staff',
            'description' => 'One row per teacher or staff member for the staff directory. This is not login access — invite accounts separately once the directory is in place.',
            'columns' => [
                ['key' => 'staff_number', 'required' => false, 'hint' => 'Leave blank and the school allocates the next number.'],
                ['key' => 'first_name', 'required' => true, 'hint' => 'Given name.'],
                ['key' => 'last_name', 'required' => true, 'hint' => 'Family name.'],
                ['key' => 'email', 'required' => false, 'hint' => 'Work e-mail, if the school has it.'],
                ['key' => 'phone', 'required' => false, 'hint' => 'Any readable format.'],
                ['key' => 'gender', 'required' => false, 'hint' => 'female, male or other. May be left blank.'],
                ['key' => 'subjects', 'required' => false, 'hint' => 'Subjects taught, separated by semicolons, for example Mathematics; Physics. Each must already be in the school\'s subject list.'],
                ['key' => 'status', 'required' => false, 'hint' => 'active or former. Blank means active.'],
                ['key' => 'hired_on', 'required' => false, 'hint' => 'YYYY-MM-DD. Blank means today.'],
            ],
        ],
    ];

    private const STUDENT_STATUSES = ['enrolled', 'applicant', 'graduated', 'withdrawn'];
    private const STAFF_STATUSES = ['active', 'former'];
    private const GENDERS = ['female', 'male', 'other'];
    private const RELATIONSHIPS = ['mother', 'father', 'guardian', 'sibling', 'other'];

    /**
     * @var \Cake\ORM\Locator\LocatorInterface
     */
    private LocatorInterface $locator;

    /**
     * @var string
     */
    private string $schoolId;

    /**
     * @var string
     */
    private string $today;

    /**
     * @var array<int, \Cake\Datasource\EntityInterface>|null Per-request roster snapshot for matching.
     */
    private ?array $studentsCache = null;

    /**
     * @var array<int, \Cake\Datasource\EntityInterface>|null
     */
    private ?array $guardiansCache = null;

    /**
     * @var array<int, \Cake\Datasource\EntityInterface>|null
     */
    private ?array $classGroupsCache = null;

    /**
     * @var array<int, \Cake\Datasource\EntityInterface>|null
     */
    private ?array $teachersCache = null;

    /**
     * @var \App\Ems\Tenant|null
     */
    private ?Tenant $tenantScope = null;

    public function __construct(LocatorInterface $locator, string $schoolId, string $today)
    {
        $this->locator = $locator;
        $this->schoolId = $schoolId;
        $this->today = $today;
    }

    /**
     * This engine's tenant-scope choke point — reads narrowed to $this->schoolId
     * by construction. See App\Ems\Tenant.
     */
    private function tenant(): Tenant
    {
        return $this->tenantScope ??= new Tenant($this->locator, $this->schoolId);
    }

    public static function isKnownKind(string $kind): bool
    {
        return isset(self::DEFINITIONS[$kind]);
    }

    // --- template ------------------------------------------------------------

    /** @return array{filename:string, content:string} */
    public function template(string $kind): array
    {
        $def = self::DEFINITIONS[$kind];
        $lines = [
            '# ' . $def['title'] . ' import template',
            '# ' . $def['description'],
            '# Lines starting with # are notes and are ignored when the file is read.',
            '# Fill in one row per record under the heading row below.',
            '#',
        ];
        foreach ($def['columns'] as $c) {
            $lines[] = '# ' . $c['key'] . ($c['required'] ? ' (required)' : ' (optional)') . ' — ' . $c['hint'];
        }
        $lines[] = implode(',', array_map(fn($c) => $c['key'], $def['columns']));

        return ['filename' => $kind . '-import-template.csv', 'content' => implode("\n", $lines)];
    }

    // --- parsing -------------------------------------------------------------

    /** @return array{header: array<int,string>, rows: array<int, array{lineNumber:int, cells: array<int,string>}>} */
    public function parseFile(string $text): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $text);
        $header = null;
        $rows = [];
        $delimiter = ',';
        foreach ($lines as $i => $line) {
            if (trim($line) === '' || str_starts_with(ltrim($line), '#')) {
                continue;
            }
            if ($header === null) {
                $delimiter = count(explode("\t", $line)) > count(explode(',', $line)) ? "\t" : ',';
                $header = array_map([self::class, 'normaliseHeader'], $this->splitLine($line, $delimiter));
                continue;
            }
            $rows[] = ['lineNumber' => $i + 1, 'cells' => $this->splitLine($line, $delimiter)];
        }

        return ['header' => $header ?? [], 'rows' => $rows];
    }

    public static function normaliseHeader(string $value): string
    {
        $value = preg_replace('/^\xEF\xBB\xBF/', '', $value);
        $value = mb_strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '_', $value);

        return trim($value, '_');
    }

    /** @return array<int, string> */
    private function splitLine(string $line, string $delimiter): array
    {
        $cells = [];
        $cell = '';
        $quoted = false;
        $len = strlen($line);
        for ($i = 0; $i < $len; $i++) {
            $char = $line[$i];
            if ($quoted) {
                if ($char === '"') {
                    if (($line[$i + 1] ?? '') === '"') {
                        $cell .= '"';
                        $i++;
                    } else {
                        $quoted = false;
                    }
                } else {
                    $cell .= $char;
                }
            } elseif ($char === '"') {
                $quoted = true;
            } elseif ($char === $delimiter) {
                $cells[] = trim($cell);
                $cell = '';
            } else {
                $cell .= $char;
            }
        }
        $cells[] = trim($cell);

        return $cells;
    }

    private static function isRealDate(string $value): bool
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return false;
        }
        $d = DateTimeImmutable::createFromFormat('!Y-m-d', $value, new DateTimeZone('UTC'));

        return $d !== false && $d->format('Y-m-d') === $value;
    }

    // --- checking ------------------------------------------------------------

    /**
     * @param array<string, string> $values
     * @return array<int, array{column:string, message:string}>
     */
    public function checkStudentRow(array $values): array
    {
        $issues = [];
        $require = function (string $key, string $label) use (&$issues, $values): void {
            if (($values[$key] ?? '') === '') {
                $issues[] = ['column' => $key, 'message' => $label . ' is missing.'];
            }
        };
        $require('first_name', 'First name');
        $require('last_name', 'Last name');
        $require('date_of_birth', 'Date of birth');
        $require('gender', 'Gender');
        $require('class_group', 'Class');
        $require('guardian_name', 'Guardian name');
        $require('guardian_phone', 'Guardian phone');

        $dob = (string)($values['date_of_birth'] ?? '');
        if ($dob !== '' && !self::isRealDate($dob)) {
            $issues[] = ['column' => 'date_of_birth', 'message' => 'Write the date of birth as YYYY-MM-DD, for example 2012-04-09.'];
        } elseif ($dob > $this->today) {
            $issues[] = ['column' => 'date_of_birth', 'message' => 'The date of birth is in the future.'];
        }

        $gender = (string)($values['gender'] ?? '');
        if ($gender !== '' && !in_array(mb_strtolower($gender), self::GENDERS, true)) {
            $issues[] = ['column' => 'gender', 'message' => 'Gender must be female, male or other.'];
        }

        $status = (string)($values['status'] ?? '');
        if ($status !== '' && !in_array(mb_strtolower($status), self::STUDENT_STATUSES, true)) {
            $issues[] = ['column' => 'status', 'message' => 'Status must be enrolled, applicant, graduated or withdrawn.'];
        }

        $class = (string)($values['class_group'] ?? '');
        if ($class !== '') {
            $known = array_map(fn($c) => (string)$c->name, $this->classGroups());
            if ($known !== [] && !$this->anyMatch($known, $class)) {
                $issues[] = ['column' => 'class_group', 'message' => sprintf('The school has no class called "%s". Create the class first, or correct the spelling.', $class)];
            }
        }

        $phone = (string)($values['guardian_phone'] ?? '');
        if ($phone !== '' && strlen(Dedup::phoneKey($phone)) < 7) {
            $issues[] = ['column' => 'guardian_phone', 'message' => 'That does not look like a phone number.'];
        }

        $enrolledOn = (string)($values['enrolled_on'] ?? '');
        if ($enrolledOn !== '' && !self::isRealDate($enrolledOn)) {
            $issues[] = ['column' => 'enrolled_on', 'message' => 'Write the enrolment date as YYYY-MM-DD.'];
        }

        return $issues;
    }

    /**
     * @param array<string, string> $values
     * @return array<int, array{column:string, message:string}>
     */
    public function checkGuardianRow(array $values): array
    {
        $issues = [];
        $require = function (string $key, string $label) use (&$issues, $values): void {
            if (($values[$key] ?? '') === '') {
                $issues[] = ['column' => $key, 'message' => $label . ' is missing.'];
            }
        };
        $require('student_admission_number', 'Student admission number');
        $require('first_name', 'First name');
        $require('last_name', 'Last name');
        $require('relationship', 'Relationship');
        $require('phone', 'Phone');

        $adm = (string)($values['student_admission_number'] ?? '');
        if ($adm !== '' && $this->studentByAdmission($adm) === null) {
            $issues[] = ['column' => 'student_admission_number', 'message' => 'No student on the register has that admission number.'];
        }

        $rel = (string)($values['relationship'] ?? '');
        if ($rel !== '' && !in_array(mb_strtolower($rel), self::RELATIONSHIPS, true)) {
            $issues[] = ['column' => 'relationship', 'message' => 'Relationship must be mother, father, guardian, sibling or other.'];
        }

        $phone = (string)($values['phone'] ?? '');
        if ($phone !== '' && strlen(Dedup::phoneKey($phone)) < 7) {
            $issues[] = ['column' => 'phone', 'message' => 'That does not look like a phone number.'];
        }

        $email = (string)($values['email'] ?? '');
        if ($email !== '' && !preg_match('/^[^@\s]+@[^@\s]+\.[^@\s]+$/', $email)) {
            $issues[] = ['column' => 'email', 'message' => 'That does not look like an e-mail address.'];
        }

        return $issues;
    }

    /**
     * @param array<string, string> $values
     * @return array<int, array{column:string, message:string}>
     */
    public function checkStaffRow(array $values): array
    {
        $issues = [];
        $require = function (string $key, string $label) use (&$issues, $values): void {
            if (($values[$key] ?? '') === '') {
                $issues[] = ['column' => $key, 'message' => $label . ' is missing.'];
            }
        };
        $require('first_name', 'First name');
        $require('last_name', 'Last name');

        $gender = (string)($values['gender'] ?? '');
        if ($gender !== '' && !in_array(mb_strtolower($gender), self::GENDERS, true)) {
            $issues[] = ['column' => 'gender', 'message' => 'Gender must be female, male or other, or left blank.'];
        }

        $status = (string)($values['status'] ?? '');
        if ($status !== '' && !in_array(mb_strtolower($status), self::STAFF_STATUSES, true)) {
            $issues[] = ['column' => 'status', 'message' => 'Status must be active or former.'];
        }

        $phone = (string)($values['phone'] ?? '');
        if ($phone !== '' && strlen(Dedup::phoneKey($phone)) < 7) {
            $issues[] = ['column' => 'phone', 'message' => 'That does not look like a phone number.'];
        }

        $email = (string)($values['email'] ?? '');
        if ($email !== '' && !preg_match('/^[^@\s]+@[^@\s]+\.[^@\s]+$/', $email)) {
            $issues[] = ['column' => 'email', 'message' => 'That does not look like an e-mail address.'];
        }

        $hiredOn = (string)($values['hired_on'] ?? '');
        if ($hiredOn !== '' && !self::isRealDate($hiredOn)) {
            $issues[] = ['column' => 'hired_on', 'message' => 'Write the hire date as YYYY-MM-DD.'];
        }

        // Subjects must already exist in the school's catalogue — an import never
        // invents a subject, exactly as a student row never invents a class.
        foreach ($this->splitSubjects((string)($values['subjects'] ?? '')) as $name) {
            if (SubjectCatalog::idFor($this->schoolId, $name) === null) {
                $issues[] = ['column' => 'subjects', 'message' => sprintf('The school has no subject called "%s". Add it to the subject list first, or correct the spelling.', $name)];
            }
        }

        return $issues;
    }

    // --- matching ------------------------------------------------------------

    /**
     * @param array<string, string> $values
     * @param array<int, array<string, mixed>> $earlier previously-staged rows (values + lineNumber)
     * @return array<int, array>
     */
    public function studentMatches(array $values, array $earlier): array
    {
        $candidates = [];
        foreach ($this->students() as $s) {
            $candidates[] = [
                'targetId' => (string)$s->id,
                'targetLabel' => trim((string)$s->first_name . ' ' . (string)$s->last_name) . ' · ' . (string)$s->admission_number . ' · ' . (string)$s->class_group,
                'firstName' => (string)$s->first_name,
                'lastName' => (string)$s->last_name,
                'dateOfBirth' => Serializer\Wire::date($s->date_of_birth),
                'phone' => (string)$s->guardian_phone,
                'admissionNumber' => (string)$s->admission_number,
                'withinFile' => false,
            ];
        }
        foreach ($earlier as $r) {
            $v = $r['values'];
            $candidates[] = [
                'targetId' => 'row:' . $r['lineNumber'],
                'targetLabel' => 'Row ' . $r['lineNumber'] . ' of this file · ' . ($v['first_name'] ?? '') . ' ' . ($v['last_name'] ?? ''),
                'firstName' => (string)($v['first_name'] ?? ''),
                'lastName' => (string)($v['last_name'] ?? ''),
                'dateOfBirth' => (string)($v['date_of_birth'] ?? ''),
                'phone' => (string)($v['guardian_phone'] ?? ''),
                'admissionNumber' => (string)($v['admission_number'] ?? ''),
                'withinFile' => true,
            ];
        }
        $found = [];
        foreach ($candidates as $c) {
            $scored = Dedup::scoreMatch($values, [
                'firstName' => $c['firstName'], 'lastName' => $c['lastName'],
                'dateOfBirth' => $c['dateOfBirth'], 'phone' => $c['phone'], 'admissionNumber' => $c['admissionNumber'],
            ]);
            if ($scored === null) {
                continue;
            }
            $found[] = [
                'targetId' => $c['targetId'], 'targetLabel' => $c['targetLabel'],
                'score' => $scored['score'], 'reasons' => $scored['reasons'], 'withinFile' => $c['withinFile'],
            ];
        }
        usort($found, fn($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($found, 0, 3);
    }

    /**
     * @param array<string, string> $values
     * @param array<int, array<string, mixed>> $earlier
     * @return array<int, array>
     */
    public function guardianMatches(array $values, array $earlier): array
    {
        $student = $this->studentByAdmission((string)($values['student_admission_number'] ?? ''));
        if ($student === null) {
            return [];
        }
        $found = [];
        foreach ($this->guardians() as $g) {
            if ((string)$g->student_id !== (string)$student->id) {
                continue;
            }
            $sameName = Dedup::norm((string)($values['first_name'] ?? '')) === Dedup::norm((string)$g->first_name)
                && Dedup::norm((string)($values['last_name'] ?? '')) === Dedup::norm((string)$g->last_name);
            $samePhone = Dedup::phoneKey((string)($values['phone'] ?? '')) !== ''
                && Dedup::phoneKey((string)($values['phone'] ?? '')) === Dedup::phoneKey((string)$g->phone);
            if (!$sameName && !$samePhone) {
                continue;
            }
            $found[] = [
                'targetId' => (string)$g->id,
                'targetLabel' => trim((string)$g->first_name . ' ' . (string)$g->last_name) . ' · ' . (string)$g->relationship . ' of ' . trim((string)$student->first_name . ' ' . (string)$student->last_name),
                'score' => $sameName && $samePhone ? 1.0 : ($sameName ? 0.9 : 0.7),
                'reasons' => [$sameName && $samePhone ? 'Same name and phone number on the same student' : ($sameName ? 'Same name on the same student' : 'Same phone number on the same student')],
                'withinFile' => false,
            ];
        }
        foreach ($earlier as $r) {
            $v = $r['values'];
            if (Dedup::norm((string)($v['student_admission_number'] ?? '')) !== Dedup::norm((string)($values['student_admission_number'] ?? ''))) {
                continue;
            }
            $sameName = Dedup::norm((string)($values['first_name'] ?? '')) === Dedup::norm((string)($v['first_name'] ?? ''))
                && Dedup::norm((string)($values['last_name'] ?? '')) === Dedup::norm((string)($v['last_name'] ?? ''));
            if (!$sameName) {
                continue;
            }
            $found[] = [
                'targetId' => 'row:' . $r['lineNumber'],
                'targetLabel' => 'Row ' . $r['lineNumber'] . ' of this file · ' . ($v['first_name'] ?? '') . ' ' . ($v['last_name'] ?? ''),
                'score' => 0.95,
                'reasons' => ['The same guardian is listed twice in this file'],
                'withinFile' => true,
            ];
        }
        usort($found, fn($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($found, 0, 3);
    }

    /**
     * Staff are a small directory, not a roster, so matching leans on the strong
     * identifiers a school actually keeps: the staff number, then the name paired
     * with a phone or e-mail. A held row is only ever a question for the reviewer.
     *
     * @param array<string, string> $values
     * @param array<int, array<string, mixed>> $earlier
     * @return array<int, array>
     */
    public function staffMatches(array $values, array $earlier): array
    {
        $rowStaff = Dedup::norm((string)($values['staff_number'] ?? ''));
        $rowFirst = Dedup::norm((string)($values['first_name'] ?? ''));
        $rowLast = Dedup::norm((string)($values['last_name'] ?? ''));
        $rowPhone = Dedup::phoneKey((string)($values['phone'] ?? ''));
        $rowEmail = mb_strtolower(trim((string)($values['email'] ?? '')));

        $found = [];
        foreach ($this->teachers() as $t) {
            $sameStaff = $rowStaff !== '' && $rowStaff === Dedup::norm((string)$t->staff_number);
            $sameName = $rowFirst === Dedup::norm((string)$t->first_name) && $rowLast === Dedup::norm((string)$t->last_name);
            $samePhone = $rowPhone !== '' && $rowPhone === Dedup::phoneKey((string)$t->phone);
            $sameEmail = $rowEmail !== '' && $rowEmail === mb_strtolower(trim((string)$t->email));
            [$score, $reason] = match (true) {
                $sameStaff => [1.0, 'Same staff number'],
                $sameName && $samePhone => [0.95, 'Same name and phone number'],
                $sameName && $sameEmail => [0.9, 'Same name and e-mail'],
                $sameName => [0.8, 'Same name'],
                $sameEmail => [0.7, 'Same e-mail'],
                $samePhone => [0.6, 'Same phone number'],
                default => [0.0, ''],
            };
            if ($score === 0.0) {
                continue;
            }
            $found[] = [
                'targetId' => (string)$t->id,
                'targetLabel' => trim((string)$t->first_name . ' ' . (string)$t->last_name) . ' · ' . (string)$t->staff_number . ' · ' . (string)$t->status,
                'score' => $score,
                'reasons' => [$reason],
                'withinFile' => false,
            ];
        }
        foreach ($earlier as $r) {
            $v = $r['values'];
            $sameStaff = $rowStaff !== '' && $rowStaff === Dedup::norm((string)($v['staff_number'] ?? ''));
            $sameName = $rowFirst === Dedup::norm((string)($v['first_name'] ?? ''))
                && $rowLast === Dedup::norm((string)($v['last_name'] ?? ''));
            if (!$sameStaff && !$sameName) {
                continue;
            }
            $found[] = [
                'targetId' => 'row:' . $r['lineNumber'],
                'targetLabel' => 'Row ' . $r['lineNumber'] . ' of this file · ' . ($v['first_name'] ?? '') . ' ' . ($v['last_name'] ?? ''),
                'score' => $sameStaff ? 1.0 : 0.9,
                'reasons' => [$sameStaff ? 'The same staff number appears earlier in this file' : 'The same staff member is listed twice in this file'],
                'withinFile' => true,
            ];
        }
        usort($found, fn($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($found, 0, 3);
    }

    // --- writing -------------------------------------------------------------

    public function nextAdmissionNumber(): string
    {
        $existing = array_map(fn($s) => (string)$s->admission_number, $this->students());
        $prefix = $existing === [] ? 'ADM' : preg_replace('#/[^/]*$#', '', $existing[0]);
        $highest = 0;
        foreach ($existing as $number) {
            $tail = (int)substr($number, strrpos($number, '/') === false ? 0 : strrpos($number, '/') + 1);
            if ($tail > $highest) {
                $highest = $tail;
            }
        }

        return $prefix . '/' . str_pad((string)($highest + 1), 4, '0', STR_PAD_LEFT);
    }

    /** @param array<string, string> $values */
    public function createStudent(array $values): EntityInterface
    {
        $students = $this->locator->get('EmsStudents');
        $admission = ($values['admission_number'] ?? '') !== '' ? $values['admission_number'] : $this->nextAdmissionNumber();
        $student = $students->newEntity([
            'school_id' => $this->schoolId,
            'admission_number' => $admission,
            'first_name' => $values['first_name'] ?? '',
            'last_name' => $values['last_name'] ?? '',
            'date_of_birth' => $values['date_of_birth'] ?? '',
            'gender' => mb_strtolower((string)($values['gender'] ?? '')),
            'class_group' => $values['class_group'] ?? '',
            'status' => ($values['status'] ?? '') === '' ? 'enrolled' : mb_strtolower((string)$values['status']),
            'guardian_name' => $values['guardian_name'] ?? '',
            'guardian_phone' => $values['guardian_phone'] ?? '',
            'enrolled_on' => ($values['enrolled_on'] ?? '') === '' ? $this->today : $values['enrolled_on'],
        ], ['validate' => false]);
        $students->saveOrFail($student);
        $this->studentsCache = null;

        return $student;
    }

    /**
     * Non-destructive field update — a blank cell never erases existing data.
     *
     * @param array<string, string> $values
     * @return array<int, string> plain-language change descriptions
     */
    public function mergeStudent(EntityInterface $existing, array $values): array
    {
        $changed = [];
        $apply = function (string $col, string $incoming, string $label) use (&$changed, $existing): void {
            if ($incoming === '') {
                return;
            }
            // Dates come back from the ORM as Date objects; compare (and report)
            // in the wire YYYY-MM-DD form the incoming cell uses.
            $current = $col === 'date_of_birth'
                ? (string)Serializer\Wire::date($existing->$col)
                : (string)$existing->$col;
            if ($current === $incoming) {
                return;
            }
            $changed[] = $label . ' ' . $current . ' to ' . $incoming;
            $existing->$col = $incoming;
        };
        $apply('first_name', (string)($values['first_name'] ?? ''), 'first name');
        $apply('last_name', (string)($values['last_name'] ?? ''), 'last name');
        $apply('date_of_birth', (string)($values['date_of_birth'] ?? ''), 'date of birth');
        $apply('class_group', (string)($values['class_group'] ?? ''), 'class');
        $apply('guardian_name', (string)($values['guardian_name'] ?? ''), 'guardian');
        $apply('guardian_phone', (string)($values['guardian_phone'] ?? ''), 'guardian phone');
        if (($values['admission_number'] ?? '') !== '') {
            $apply('admission_number', (string)$values['admission_number'], 'admission number');
        }
        if (($values['gender'] ?? '') !== '') {
            $apply('gender', mb_strtolower((string)$values['gender']), 'gender');
        }
        if (($values['status'] ?? '') !== '') {
            $apply('status', mb_strtolower((string)$values['status']), 'status');
        }
        $this->locator->get('EmsStudents')->saveOrFail($existing);
        $this->studentsCache = null;

        return $changed;
    }

    /** @param array<string, string> $values */
    public function createGuardian(array $values): ?EntityInterface
    {
        $student = $this->studentByAdmission((string)($values['student_admission_number'] ?? ''));
        if ($student === null) {
            return null;
        }
        $guardiansTable = $this->locator->get('EmsGuardians');
        $siblings = $this->tenant()->query('EmsGuardians')
            ->where(['student_id' => (string)$student->id])->all()->toList();
        $wantsPrimary = in_array(mb_strtolower((string)($values['is_primary'] ?? '')), ['yes', 'true', 'y', '1'], true);
        $isPrimary = $siblings === [] ? true : $wantsPrimary;
        if ($isPrimary) {
            foreach ($siblings as $g) {
                $g->is_primary = false;
                $guardiansTable->saveOrFail($g);
            }
        }
        $guardian = $guardiansTable->newEntity([
            'school_id' => $this->schoolId,
            'student_id' => (string)$student->id,
            'first_name' => $values['first_name'] ?? '',
            'last_name' => $values['last_name'] ?? '',
            'relationship' => mb_strtolower((string)($values['relationship'] ?? '')),
            'phone' => $values['phone'] ?? '',
            'email' => $values['email'] ?? '',
            'occupation' => $values['occupation'] ?? '',
            'is_primary' => $isPrimary,
        ], ['validate' => false]);
        $guardiansTable->saveOrFail($guardian);
        $this->guardiansCache = null;
        $this->syncPrimaryContact((string)$student->id);

        return $guardian;
    }

    /**
     * @param array<string, string> $values
     * @return array<int, string>
     */
    public function mergeGuardian(EntityInterface $existing, array $values): array
    {
        $changed = [];
        $apply = function (string $col, string $incoming, string $label) use (&$changed, $existing): void {
            if ($incoming === '') {
                return;
            }
            if ((string)$existing->$col === $incoming) {
                return;
            }
            $changed[] = $label . ' ' . (string)$existing->$col . ' to ' . $incoming;
            $existing->$col = $incoming;
        };
        $apply('first_name', (string)($values['first_name'] ?? ''), 'first name');
        $apply('last_name', (string)($values['last_name'] ?? ''), 'last name');
        $apply('phone', (string)($values['phone'] ?? ''), 'phone');
        $apply('email', (string)($values['email'] ?? ''), 'e-mail');
        $apply('occupation', (string)($values['occupation'] ?? ''), 'occupation');
        if (($values['relationship'] ?? '') !== '') {
            $apply('relationship', mb_strtolower((string)$values['relationship']), 'relationship');
        }
        $this->locator->get('EmsGuardians')->saveOrFail($existing);
        $this->guardiansCache = null;
        $this->syncPrimaryContact((string)$existing->student_id);

        return $changed;
    }

    public function syncPrimaryContact(string $studentId): void
    {
        $primary = $this->tenant()->query('EmsGuardians')
            ->where(['student_id' => $studentId, 'is_primary' => true])->first();
        $students = $this->locator->get('EmsStudents');
        $student = $this->tenant()->query('EmsStudents')->where(['id' => $studentId])->first();
        if ($student === null) {
            return;
        }
        $student->guardian_name = $primary === null ? '' : trim((string)$primary->first_name . ' ' . (string)$primary->last_name);
        $student->guardian_phone = $primary === null ? '' : (string)$primary->phone;
        $students->saveOrFail($student);
    }

    // --- staff writing -------------------------------------------------------

    /**
     * The next staff number when a row leaves it blank — the prefix of the first
     * existing number with its trailing digits incremented, or STF/001 for the
     * first staff member. createStaff clears the cache so a file of blanks never
     * hands out the same number twice.
     */
    public function nextStaffNumber(): string
    {
        $existing = array_map(fn($t) => (string)$t->staff_number, $this->teachers());
        $prefix = 'STF/';
        foreach ($existing as $number) {
            $stripped = preg_replace('/\d+\s*$/', '', $number);
            if ($stripped !== null && $stripped !== '') {
                $prefix = $stripped;
                break;
            }
        }
        $highest = 0;
        foreach ($existing as $number) {
            if (preg_match('/(\d+)\s*$/', $number, $m) && (int)$m[1] > $highest) {
                $highest = (int)$m[1];
            }
        }

        return $prefix . str_pad((string)($highest + 1), 3, '0', STR_PAD_LEFT);
    }

    /** @param array<string, string> $values */
    public function createStaff(array $values): EntityInterface
    {
        $teachers = $this->locator->get('EmsTeachers');
        $staffNumber = ($values['staff_number'] ?? '') !== '' ? $values['staff_number'] : $this->nextStaffNumber();
        $teacher = $teachers->newEntity([
            'school_id' => $this->schoolId,
            'staff_number' => $staffNumber,
            'first_name' => $values['first_name'] ?? '',
            'last_name' => $values['last_name'] ?? '',
            'email' => $values['email'] ?? '',
            'phone' => $values['phone'] ?? '',
            'gender' => mb_strtolower((string)($values['gender'] ?? '')),
            'subjects' => $this->resolveSubjectIds((string)($values['subjects'] ?? '')),
            'status' => ($values['status'] ?? '') === '' ? 'active' : mb_strtolower((string)$values['status']),
            'hired_on' => ($values['hired_on'] ?? '') === '' ? $this->today : $values['hired_on'],
        ], ['validate' => false]);
        $teachers->saveOrFail($teacher);
        $this->teachersCache = null;

        return $teacher;
    }

    /**
     * Non-destructive field update — a blank cell never erases existing data. A
     * non-empty subjects cell replaces the whole list (the file states the full
     * set of subjects for that member); a blank one leaves the subjects alone.
     *
     * @param array<string, string> $values
     * @return array<int, string>
     */
    public function mergeStaff(EntityInterface $existing, array $values): array
    {
        $changed = [];
        $apply = function (string $col, string $incoming, string $label) use (&$changed, $existing): void {
            if ($incoming === '' || (string)$existing->$col === $incoming) {
                return;
            }
            $changed[] = $label . ' ' . (string)$existing->$col . ' to ' . $incoming;
            $existing->$col = $incoming;
        };
        $apply('first_name', (string)($values['first_name'] ?? ''), 'first name');
        $apply('last_name', (string)($values['last_name'] ?? ''), 'last name');
        $apply('email', (string)($values['email'] ?? ''), 'e-mail');
        $apply('phone', (string)($values['phone'] ?? ''), 'phone');
        if (($values['staff_number'] ?? '') !== '') {
            $apply('staff_number', (string)$values['staff_number'], 'staff number');
        }
        if (($values['gender'] ?? '') !== '') {
            $apply('gender', mb_strtolower((string)$values['gender']), 'gender');
        }
        if (($values['status'] ?? '') !== '') {
            $apply('status', mb_strtolower((string)$values['status']), 'status');
        }
        if (($values['hired_on'] ?? '') !== '') {
            $current = (string)Serializer\Wire::date($existing->hired_on);
            if ($current !== (string)$values['hired_on']) {
                $changed[] = 'hire date ' . $current . ' to ' . (string)$values['hired_on'];
                $existing->hired_on = $values['hired_on'];
            }
        }
        $subjectsCell = (string)($values['subjects'] ?? '');
        if ($subjectsCell !== '') {
            $newIds = $this->resolveSubjectIds($subjectsCell);
            $currentIds = array_values(array_map('strval', (array)($existing->subjects ?? [])));
            if ($newIds !== $currentIds) {
                $before = $this->subjectNames($currentIds);
                $after = $this->subjectNames($newIds);
                $changed[] = 'subjects ' . ($before === '' ? 'none' : $before) . ' to ' . ($after === '' ? 'none' : $after);
                $existing->subjects = $newIds;
            }
        }
        $this->locator->get('EmsTeachers')->saveOrFail($existing);
        $this->teachersCache = null;

        return $changed;
    }

    /**
     * Resolve a semicolon-separated subjects cell to catalogue ids, de-duplicated
     * and preserving order. Unknown names are dropped here — checkStaffRow has
     * already flagged them, so a committed row only carries subjects that exist.
     *
     * @return array<int, string>
     */
    private function resolveSubjectIds(string $cell): array
    {
        $ids = [];
        foreach ($this->splitSubjects($cell) as $name) {
            $id = SubjectCatalog::idFor($this->schoolId, $name);
            if ($id !== null && !in_array($id, $ids, true)) {
                $ids[] = $id;
            }
        }

        return $ids;
    }

    /** @return array<int, string> */
    private function splitSubjects(string $cell): array
    {
        $parts = preg_split('/\s*;\s*/', trim($cell)) ?: [];

        return array_values(array_filter(array_map('trim', $parts), fn($s) => $s !== ''));
    }

    /** @param array<int, string> $ids */
    private function subjectNames(array $ids): string
    {
        $names = array_filter(array_map(fn($id) => SubjectCatalog::name($this->schoolId, $id), $ids), fn($n) => $n !== '');

        return implode(', ', $names);
    }

    // --- small data helpers --------------------------------------------------

    /** @param array<int, string> $known */
    private function anyMatch(array $known, string $value): bool
    {
        $needle = Dedup::norm($value);
        foreach ($known as $name) {
            if (Dedup::norm($name) === $needle) {
                return true;
            }
        }

        return false;
    }

    private function studentByAdmission(string $admission): ?EntityInterface
    {
        $needle = Dedup::norm($admission);
        foreach ($this->students() as $s) {
            if (Dedup::norm((string)$s->admission_number) === $needle) {
                return $s;
            }
        }

        return null;
    }

    /**
     * The roster, loaded once per request and reused. Matching scores every
     * staged row against the whole roster, so without this a file of M rows
     * re-read all N students M times; the cache makes it one read. Any write
     * that could change the roster (createStudent/mergeStudent) clears it so a
     * later nextAdmissionNumber never hands out a number twice.
     *
     * @return array<int, \Cake\Datasource\EntityInterface>
     */
    private function students(): array
    {
        return $this->studentsCache ??= $this->tenant()->query('EmsStudents')
            ->all()->toList();
    }

    /** @return array<int, \Cake\Datasource\EntityInterface> */
    private function guardians(): array
    {
        return $this->guardiansCache ??= $this->tenant()->query('EmsGuardians')
            ->all()->toList();
    }

    /**
     * The class list, loaded once and reused — every student row validates its
     * class against it, and imports never create classes, so it never needs to
     * be reloaded within a request.
     *
     * @return array<int, \Cake\Datasource\EntityInterface>
     */
    private function classGroups(): array
    {
        return $this->classGroupsCache ??= $this->tenant()->query('EmsClassGroups')
            ->all()->toList();
    }

    /**
     * The staff directory, loaded once and reused for the whole file — matching
     * scores every staged row against it. createStaff/mergeStaff clear it so a
     * later nextStaffNumber sees rows created earlier in the same commit.
     *
     * @return array<int, \Cake\Datasource\EntityInterface>
     */
    private function teachers(): array
    {
        return $this->teachersCache ??= $this->tenant()->query('EmsTeachers')
            ->all()->toList();
    }
}
