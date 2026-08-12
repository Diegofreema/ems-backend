<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Sparents API — REST CRUD over the SparentsTable model.
 * (list / view / create / update / delete + filter, search, sort, paginate)
 */
class SparentsController extends CrudController
{
    protected array $searchFields = ['fname', 'lname', 'firstname', 'lastname', 'email', 'phone'];
}
