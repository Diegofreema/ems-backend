<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Faculties API — REST CRUD over the FacultiesTable model.
 * (list / view / create / update / delete + filter, search, sort, paginate)
 */
class FacultiesController extends CrudController
{
    protected array $searchFields = ['name'];
}
