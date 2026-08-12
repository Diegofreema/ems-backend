<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Payslips API — CRUD + filters. An employee's payslips = ?teacher_id=.
 * (Bulk payroll generation from the web controller is not ported.)
 */
class PayslipsController extends CrudController
{
    protected array $searchFields = ['formonth'];
    protected bool $writeAllowApiKey = false;
}
