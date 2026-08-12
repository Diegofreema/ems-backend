<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Topics API — REST CRUD over the TopicsTable model (filter, search, sort, paginate).
 */
class TopicsController extends CrudController
{
    protected array $searchFields = ['title', 'name'];
}
