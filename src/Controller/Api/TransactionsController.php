<?php
declare(strict_types=1);

namespace App\Controller\Api;

/**
 * Transactions API — CRUD + filters.
 *   student's payments  = ?student_id=
 *   pending payments    = ?paystatus=initialized
 *   paid payments       = ?paystatus=paid
 *   search              = ?q=  (payref / gateway)
 * Payment-gateway initiation & verification (Paystack/Credo/Remita/etc.) are
 * server-redirect flows and are intentionally NOT exposed as API endpoints.
 * Payment records: API keys may read but not write.
 */
class TransactionsController extends CrudController
{
    protected array $searchFields = ['payref', 'pgateway'];
    protected bool $writeAllowApiKey = false;
}
