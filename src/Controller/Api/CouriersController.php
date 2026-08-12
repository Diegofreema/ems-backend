<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Couriers API — REST CRUD over the CouriersTable model (filter, search, sort, paginate).
 */
class CouriersController extends CrudController
{
    protected array $searchFields = ['name', 'title'];
}
