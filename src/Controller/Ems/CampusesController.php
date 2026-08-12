<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Messages;
use App\Ems\Serializer\CalendarSerializer;
use Cake\Datasource\EntityInterface;
use Cake\Http\Response;

/**
 * Campuses (document.md §3.13).
 */
class CampusesController extends AppController
{
    /**
     * GET /campuses — active first, then name.
     */
    public function index(): Response
    {
        $rows = $this->tenant()->query('EmsCampuses')
            ->orderByDesc('active')
            ->orderByAsc('name')
            ->all();

        return $this->json(array_map([CalendarSerializer::class, 'campus'], $rows->toList()));
    }

    /**
     * POST /campuses { name, address } — created active.
     */
    public function add(): Response
    {
        $body = $this->body();
        $name = trim((string)($body['name'] ?? ''));

        if ($name === '') {
            $this->fail(422, Messages::CAMPUS_NAME_REQUIRED);
        }
        $this->assertNameFree($name, null);

        $campuses = $this->fetchTable('EmsCampuses');
        $campus = $campuses->saveOrFail($campuses->newEntity([
            'school_id' => $this->viewer->schoolId,
            'name' => $name,
            'address' => trim((string)($body['address'] ?? '')),
            'active' => true,
        ]));

        return $this->json(CalendarSerializer::campus($campus), 201);
    }

    /**
     * PUT /campuses/{id} { name, address } — same checks excluding self.
     */
    public function edit(string $id): Response
    {
        $campuses = $this->fetchTable('EmsCampuses');
        $campus = $this->findCampus($id);
        $body = $this->body();
        $name = trim((string)($body['name'] ?? ''));

        if ($name === '') {
            $this->fail(422, Messages::CAMPUS_NAME_REQUIRED);
        }
        $this->assertNameFree($name, (string)$campus->id);

        $campus->name = $name;
        $campus->address = trim((string)($body['address'] ?? $campus->address));
        $campuses->saveOrFail($campus);

        return $this->json(CalendarSerializer::campus($campus));
    }

    /**
     * POST /campuses/{id}/active { active } — a school needs at least one
     * active campus.
     */
    public function setActive(string $id): Response
    {
        $campuses = $this->fetchTable('EmsCampuses');
        $campus = $this->findCampus($id);
        $active = (bool)($this->body()['active'] ?? true);

        if (!$active && (bool)$campus->active) {
            $otherActive = $this->tenant()->query('EmsCampuses')
                ->where([
                    'active' => true,
                    'id !=' => $campus->id,
                ])
                ->count();
            if ($otherActive === 0) {
                $this->fail(422, Messages::CAMPUS_LAST_ACTIVE);
            }
        }

        $campus->active = $active;
        $campuses->saveOrFail($campus);

        return $this->json(CalendarSerializer::campus($campus));
    }

    private function findCampus(string $id): EntityInterface
    {
        return $this->findOr404('EmsCampuses', $id, Messages::CAMPUS_NOT_FOUND);
    }

    /**
     * Case-insensitive duplicate-name check (§3.13).
     */
    private function assertNameFree(string $name, ?string $excludeId): void
    {
        $query = $this->tenant()->query('EmsCampuses')
            ->where([
                'LOWER(name)' => strtolower($name),
            ]);
        if ($excludeId !== null) {
            $query->where(['id !=' => $excludeId]);
        }
        if ($query->count() > 0) {
            $this->fail(422, sprintf(Messages::CAMPUS_EXISTS, $name));
        }
    }
}
