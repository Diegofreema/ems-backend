<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Semesters API — REST CRUD over the SemestersTable model.
 * (list / view / create / update / delete + filter, search, sort, paginate)
 */
class SemestersController extends CrudController
{
    protected array $searchFields = ['name'];
}
