<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Staffmessages API — REST CRUD over the StaffmessagesTable model (filter, search, sort, paginate).
 */
class StaffmessagesController extends CrudController
{
    protected array $searchFields = ['title', 'message', 'messages'];
}
