<?php
declare(strict_types=1);

namespace App\Ems;

use Cake\Http\Exception\HttpException;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * The per-school subject catalogue (Stage 0 of the completeness pass). Subjects
 * are first-class rows in `ems_subjects`; every referencing table stores
 * `subject_id`. This class is the application-level join: a per-request cache
 * of each school's id↔name map, so reads can decorate rows with the subject
 * NAME (the wire contract) and writes can resolve an incoming name to its id
 * without an extra query per row.
 */
class SubjectCatalog
{
    use LocatorAwareTrait;

    /** @var array<string, array<string, string>> schoolId => [subjectId => name] */
    private static array $names = [];

    /** @var array<string, array<string, string>> schoolId => [lower(name) => subjectId] */
    private static array $ids = [];

    /** The verbatim refusal when a subject name is not in the catalogue. */
    public const UNKNOWN_SUBJECT = Messages::SUBJECT_UNKNOWN;

    /** The standard curriculum seeded into every new school's catalogue. */
    public const STANDARD_SUBJECTS = [
        'English', 'Mathematics', 'Basic Science', 'Social Studies',
        'Civic Education', 'Agricultural Science', 'Computer Studies',
        'Business Studies', 'Physical Education', 'Fine Art', 'French',
        'Physics', 'Chemistry', 'Biology', 'Economics', 'Literature',
        'Geography', 'Government',
    ];

    private static function load(string $schoolId): void
    {
        if (isset(self::$names[$schoolId])) {
            return;
        }
        $table = (new self())->fetchTable('EmsSubjects');
        $rows = $table->find()
            ->where(['school_id' => $schoolId])
            ->orderByAsc('name')
            ->all();
        $names = [];
        $ids = [];
        foreach ($rows as $row) {
            $names[(string)$row->id] = (string)$row->name;
            $ids[mb_strtolower((string)$row->name)] = (string)$row->id;
        }
        self::$names[$schoolId] = $names;
        self::$ids[$schoolId] = $ids;
    }

    /** The subject's display name, or '' when the id is unknown. */
    public static function name(string $schoolId, ?string $subjectId): string
    {
        if ($subjectId === null || $subjectId === '') {
            return '';
        }
        self::load($schoolId);

        return self::$names[$schoolId][$subjectId] ?? '';
    }

    /** The id behind a subject name, or null when the name is unknown. */
    public static function idFor(string $schoolId, string $name): ?string
    {
        self::load($schoolId);

        return self::$ids[$schoolId][mb_strtolower(trim($name))] ?? null;
    }

    /** Resolve a name that MUST exist — 422 with the verbatim refusal if not. */
    public static function requireId(string $schoolId, string $name): string
    {
        $id = self::idFor($schoolId, $name);
        if ($id === null) {
            throw new HttpException(self::UNKNOWN_SUBJECT, 422);
        }

        return $id;
    }

    /** The full catalogue map (id => name), ordered by name. */
    public static function all(string $schoolId): array
    {
        self::load($schoolId);

        return self::$names[$schoolId];
    }

    /** Forget a school's cached catalogue (after a create/rename/delete). */
    public static function invalidate(string $schoolId): void
    {
        unset(self::$names[$schoolId], self::$ids[$schoolId]);
    }
}
