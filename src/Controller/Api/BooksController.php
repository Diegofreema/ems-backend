<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Books API — REST CRUD over the BooksTable model (filter, search, sort, paginate).
 * findbooks = ?q=  •  filter by department_id, section, isavailable.
 */
class BooksController extends CrudController
{
    protected array $searchFields = ['title', 'author', 'isbn', 'callno', 'section'];
}
