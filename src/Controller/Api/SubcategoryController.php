<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Subcategory API — REST CRUD over the SubcategoryTable model (filter, search, sort, paginate).
 */
class SubcategoryController extends CrudController
{
    protected array $searchFields = ['name', 'title'];
}
