<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Invoices API — CRUD + filters. A student's invoices = ?student_id=.
 * Payment records: server-to-server API keys may read but not write.
 */
class InvoicesController extends CrudController
{
    protected array $searchFields = ['invoiceid'];
    protected bool $writeAllowApiKey = false;
}
