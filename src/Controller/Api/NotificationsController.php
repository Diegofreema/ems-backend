<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Notifications API — REST CRUD over the NotificationsTable model (filter, search, sort, paginate).
 */
class NotificationsController extends CrudController
{
    protected array $searchFields = ['title', 'notice', 'message'];
}
