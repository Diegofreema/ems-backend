<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use App\Ems\Serializer\SettingsSerializer;
use Cake\Http\Response;

/**
 * School profile (document.md §3.14).
 */
class SchoolController extends AppController
{
    /**
     * GET /schools/{schoolId}/school — the tenant registry row.
     */
    public function view(): Response
    {
        $school = $this->fetchTable('EmsSchools')->get($this->viewer->schoolId);

        return $this->json(SettingsSerializer::school($school));
    }

    /**
     * PUT /schools/{schoolId}/school — SchoolProfileInput. Fields are
     * trimmed; `logo` is tri-state: absent = unchanged, null = cleared,
     * string = replaced.
     */
    public function update(): Response
    {
        $schools = $this->fetchTable('EmsSchools');
        $school = $schools->get($this->viewer->schoolId);
        $body = $this->body();

        $school->name = trim((string)($body['name'] ?? $school->name));
        $school->short_name = trim((string)($body['shortName'] ?? $school->short_name));
        $school->motto = trim((string)($body['motto'] ?? $school->motto));
        $school->address = trim((string)($body['address'] ?? $school->address));
        if (array_key_exists('logo', $body)) {
            $school->logo = $body['logo'] === null ? null : (string)$body['logo'];
        }
        $schools->saveOrFail($school);

        return $this->json(SettingsSerializer::school($school));
    }
}
