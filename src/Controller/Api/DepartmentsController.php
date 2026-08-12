<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Departments API — REST CRUD over the DepartmentsTable model.
 * (list / view / create / update / delete + filter, search, sort, paginate)
 */
class DepartmentsController extends CrudController
{
    protected array $searchFields = ['name'];
}
