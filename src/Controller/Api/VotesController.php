<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Votes API — REST CRUD over the VotesTable model (filter, search, sort, paginate).
 */
class VotesController extends CrudController
{
    protected array $searchFields = ['title'];
}
