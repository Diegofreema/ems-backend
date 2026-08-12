<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Trequests API — REST CRUD over the TrequestsTable model (filter, search, sort, paginate).
 */
class TrequestsController extends CrudController
{
    protected array $searchFields = ['status'];
}
