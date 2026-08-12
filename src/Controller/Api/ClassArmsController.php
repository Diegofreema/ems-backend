<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * ClassArms API — REST CRUD over the ClassArmsTable model.
 * (list / view / create / update / delete + filter, search, sort, paginate)
 */
class ClassArmsController extends CrudController
{
    protected array $searchFields = ['arm_name'];
}
