<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Letters API — REST CRUD over the LettersTable model (filter, search, sort, paginate).
 */
class LettersController extends CrudController
{
    protected array $searchFields = ['title', 'body', 'content'];
}
