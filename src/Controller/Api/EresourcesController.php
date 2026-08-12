<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Eresources API — REST CRUD over the EresourcesTable model (filter, search, sort, paginate).
 * filter by department_id.
 */
class EresourcesController extends CrudController
{
    protected array $searchFields = ['title', 'author', 'isbn'];
}
