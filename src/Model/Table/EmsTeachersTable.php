<?php
declare(strict_types=1);

namespace App\Model\Table;

/**
 * EMS contract table `ems_teachers`. Explicit setTable() so the class can
 * never be conflated with a legacy `tss` table of a similar name.
 */
class EmsTeachersTable extends EmsTable
{
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('ems_teachers');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }

    /**
     * A teacher's qualifications live in the `subjects` column as a JSON array of
     * catalogue ids. These two methods are the ONLY place that storage format is
     * expressed in a query, so no other module (the subjects catalogue, search)
     * reaches across the seam into the JSON — they ask the teachers module
     * "does this subject id appear" and it owns how that is stored.
     *
     * @return array<string, string> a WHERE fragment for use in find()/OR groups
     */
    public function carriesSubjectCondition(string $subjectId): array
    {
        return ['subjects LIKE' => '%' . json_encode($subjectId) . '%'];
    }

    /** Whether any teacher in a school carries a subject id in their JSON list. */
    public function anyCarriesSubject(string $schoolId, string $subjectId): bool
    {
        return $this->exists(['school_id' => $schoolId] + $this->carriesSubjectCondition($subjectId));
    }
}
