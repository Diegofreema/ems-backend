<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Messages;
use App\Ems\SubjectCatalog;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;

/**
 * The subject catalogue (Stage 0 of the completeness pass). Administrator-only
 * configuration: subjects are referenced by id everywhere (questions, papers,
 * schedules, grades, assessments, allocations, timetable, teacher
 * qualifications), so a rename here is a single-row update that history
 * follows, and deletion is refused while anything references the subject —
 * deactivation hides it from new use instead.
 */
class SubjectsController extends AppController
{
    /** GET /subjects — the full catalogue, inactive entries included. */
    public function index(): Response
    {
        $rows = $this->tenant()->query('EmsSubjects')
            ->orderByAsc('name')
            ->all()
            ->toList();

        return $this->json(array_map([$this, 'serialize'], $rows));
    }

    /** POST /subjects — add a subject to the catalogue. */
    public function add(): Response
    {
        $name = trim((string)($this->body()['name'] ?? ''));
        if ($name === '') {
            $this->fail(422, Messages::SUBJECT_NAME_REQUIRED);
        }
        $subjects = $this->fetchTable('EmsSubjects');
        if (
            $this->tenant()->exists('EmsSubjects', [
                'LOWER(name)' => mb_strtolower($name),
            ])
        ) {
            $this->fail(422, Messages::SUBJECT_EXISTS);
        }
        $row = $subjects->saveOrFail($subjects->newEntity([
            'school_id' => $this->viewer->schoolId,
            'name' => $name,
            'active' => true,
        ]));
        SubjectCatalog::invalidate($this->viewer->schoolId);
        $this->audit()->log(
            $this->viewer,
            'subject.created',
            'subject',
            (string)$row->id,
            sprintf('Added %s to the subject catalogue', $name),
        );

        return $this->json($this->serialize($row), 201);
    }

    /** PUT /subjects/{id} — rename. History follows: names live in one place. */
    public function edit(string $id): Response
    {
        $row = $this->findSubject($id);
        $name = trim((string)($this->body()['name'] ?? ''));
        if ($name === '') {
            $this->fail(422, Messages::SUBJECT_NAME_REQUIRED);
        }
        $subjects = $this->fetchTable('EmsSubjects');
        $clash = $this->tenant()->query('EmsSubjects')
            ->where([
                'LOWER(name)' => mb_strtolower($name),
                'id !=' => $row->id,
            ])
            ->count();
        if ($clash > 0) {
            $this->fail(422, Messages::SUBJECT_EXISTS);
        }
        $was = (string)$row->name;
        $row->name = $name;
        $subjects->saveOrFail($row);
        SubjectCatalog::invalidate($this->viewer->schoolId);
        if ($was !== $name) {
            $this->audit()->log(
                $this->viewer,
                'subject.renamed',
                'subject',
                (string)$row->id,
                sprintf('Renamed the subject %s to %s', $was, $name),
            );
        }

        return $this->json($this->serialize($row));
    }

    /** POST /subjects/{id}/deactivate — hide from new use; history keeps it. */
    public function deactivate(string $id): Response
    {
        return $this->setActive($id, false);
    }

    /** POST /subjects/{id}/reactivate */
    public function reactivate(string $id): Response
    {
        return $this->setActive($id, true);
    }

    /** DELETE /subjects/{id} — refused while anything references the subject. */
    public function delete(string $id): Response
    {
        $row = $this->findSubject($id);
        $subjectId = (string)$row->id;
        $this->assertNoReferences($subjectId, [
            'EmsQuestions' => 'subject_id',
            'EmsExamSchedules' => 'subject_id',
            'EmsExamGrades' => 'subject_id',
            'EmsExamPapers' => 'subject_id',
            'EmsAssessments' => 'subject_id',
            'EmsSubjectAllocations' => 'subject_id',
            'EmsTimetableSlots' => 'subject_id',
        ], Messages::SUBJECT_IN_USE);
        // Teacher qualification lists hold subject ids inside a JSON array — a
        // match the FK-equality guard above cannot express. Ask the teachers
        // module, which owns that storage format, rather than reaching into it.
        if ($this->fetchTable('EmsTeachers')->anyCarriesSubject($this->viewer->schoolId, $subjectId)) {
            $this->fail(409, Messages::SUBJECT_IN_USE);
        }
        $name = (string)$row->name;
        $this->fetchTable('EmsSubjects')->deleteOrFail($row);
        SubjectCatalog::invalidate($this->viewer->schoolId);
        $this->audit()->log(
            $this->viewer,
            'subject.deleted',
            'subject',
            $id,
            sprintf('Removed %s from the subject catalogue', $name),
        );

        return $this->response->withStatus(204);
    }

    private function setActive(string $id, bool $active): Response
    {
        $row = $this->findSubject($id);
        $row->active = $active;
        $this->fetchTable('EmsSubjects')->saveOrFail($row);
        SubjectCatalog::invalidate($this->viewer->schoolId);
        $this->audit()->log(
            $this->viewer,
            $active ? 'subject.reactivated' : 'subject.deactivated',
            'subject',
            (string)$row->id,
            sprintf(
                $active
                    ? 'Returned %s to the subject catalogue'
                    : 'Retired %s from the subject catalogue',
                (string)$row->name,
            ),
        );

        return $this->json($this->serialize($row));
    }

    private function findSubject(string $id): EntityInterface
    {
        return $this->findOr404('EmsSubjects', $id, Messages::SUBJECT_NOT_FOUND);
    }

    private function serialize(EntityInterface $row): array
    {
        return [
            'id' => (string)$row->id,
            'schoolId' => (string)$row->school_id,
            'name' => (string)$row->name,
            'active' => (bool)$row->active,
        ];
    }
}
