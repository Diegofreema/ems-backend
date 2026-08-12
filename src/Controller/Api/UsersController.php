<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Users API — sensitive resource: reads restricted to admin roles (1/5/7) and
 * server-to-server API keys may not write.
 */
class UsersController extends CrudController
{
    protected ?array $readRoles = [1, 5, 7];
    protected bool $writeAllowApiKey = false;
}
