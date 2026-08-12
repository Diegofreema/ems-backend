<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Coursematerials API — REST CRUD over the CoursematerialsTable model (filter, search, sort, paginate).
 * filter by subject_id, teacher_id, department_id.
 */
class CoursematerialsController extends CrudController
{
    protected array $searchFields = ['title', 'comment'];
}
