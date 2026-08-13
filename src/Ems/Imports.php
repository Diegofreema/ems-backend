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
    ];

    private const STUDENT_STATUSES = ['enrolled', 'applicant', 'graduated', 'withdrawn'];
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
}
