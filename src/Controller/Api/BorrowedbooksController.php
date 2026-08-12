<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Borrowedbooks API — REST CRUD over the BorrowedbooksTable model (filter, search, sort, paginate).
 * A student's loans = ?student_id=  •  by book = ?book_id=  •  by status.
 */
class BorrowedbooksController extends CrudController
{
    protected array $searchFields = ['status'];
}
