<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Messages;
use App\Ems\Serializer\SettingsSerializer;
use Cake\Http\Response;

/**
 * Tenant resolution: the frontend resolves /app/{schoolSlug} → school once,
 * then scopes every call by schoolId (document.md §1.1 / §3.18).
 */
class SchoolsController extends AppController
{
    /**
     * GET /schools/by-slug/{slug}
     *
     * Enumeration-safe: an unknown slug and a school the viewer has no
     * membership in produce the same 404.
     */
    public function bySlug(string $slug): Response
    {
        $school = $this->fetchTable('EmsSchools')->find()
            ->where(['slug' => strtolower(trim($slug))])
            ->first();

        if ($school === null || (string)$school->id !== $this->viewer->schoolId) {
            $this->fail(404, Messages::SCHOOL_NOT_FOUND);
        }

        return $this->json(SettingsSerializer::school($school));
    }
}
