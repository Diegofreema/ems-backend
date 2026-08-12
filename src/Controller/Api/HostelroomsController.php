<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Hostelrooms API — REST CRUD + a room's occupants.
 * Assigning a student to a room = POST /hostelrooms-students (create a
 * hostelrooms_students record); ejecting = DELETE that record.
 * GET /api/v1/hostelrooms/{id}/students  -> occupants of a room
 */
class HostelroomsController extends CrudController
{
    protected array $searchFields = ['name', 'roomname', 'title'];

    public function students($id = null)
    {
        // Prefer an explicit occupants association if present.
        foreach (['HostelroomsStudents', 'Students'] as $assoc) {
            if ($this->Model->hasAssociation($assoc)) {
                return $this->relatedList($id, $assoc);
            }
        }

        return $this->respondError('No occupants association is available.', 404);
    }
}
