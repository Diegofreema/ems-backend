<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Studentmessages API — REST CRUD over the StudentmessagesTable model (filter, search, sort, paginate).
 */
class StudentmessagesController extends CrudController
{
    protected array $searchFields = ['title', 'messages'];
}
