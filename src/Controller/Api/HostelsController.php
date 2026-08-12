<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Hostels API — REST CRUD (create/updatehostel map to POST/PATCH).
 */
class HostelsController extends CrudController
{
    protected array $searchFields = ['name', 'title'];
}
