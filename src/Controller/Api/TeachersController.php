<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Teachers API — REST CRUD over the TeachersTable model.
 * (list / view / create / update / delete + filter, search, sort, paginate)
 */
class TeachersController extends CrudController
{
    protected array $searchFields = ['firstname', 'lastname', 'surname', 'email', 'phone'];
}
