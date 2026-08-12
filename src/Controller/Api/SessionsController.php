<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Sessions API — REST CRUD over the SessionsTable model.
 * (list / view / create / update / delete + filter, search, sort, paginate)
 */
class SessionsController extends CrudController
{
    protected array $searchFields = ['name'];
}
