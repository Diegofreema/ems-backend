<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Subjects API — REST CRUD over the SubjectsTable model.
 * (list / view / create / update / delete + filter, search, sort, paginate)
 */
class SubjectsController extends CrudController
{
    protected array $searchFields = ['name', 'code'];
}
