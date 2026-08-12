<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Levels API — REST CRUD over the LevelsTable model.
 * (list / view / create / update / delete + filter, search, sort, paginate)
 */
class LevelsController extends CrudController
{
    protected array $searchFields = ['name'];
}
