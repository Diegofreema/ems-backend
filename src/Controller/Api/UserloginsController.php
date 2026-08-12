<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Userlogins API — sensitive resource: reads restricted to admin roles (1/5/7) and
 * server-to-server API keys may not write.
 */
class UserloginsController extends CrudController
{
    protected ?array $readRoles = [1, 5, 7];
    protected bool $writeAllowApiKey = false;
}
