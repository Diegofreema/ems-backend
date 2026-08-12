<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Courses API — REST CRUD over the CoursesTable model.
 * (list / view / create / update / delete + filter, search, sort, paginate)
 */
class CoursesController extends CrudController
{
    protected array $searchFields = ['name', 'title', 'code'];
}
