<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Employees API — REST CRUD over the EmployeesTable model.
 * (list / view / create / update / delete + filter, search, sort, paginate)
 */
class EmployeesController extends CrudController
{
    protected array $searchFields = ['fname', 'lname', 'firstname', 'lastname', 'email', 'phone'];
}
