<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use Cake\Http\Response;

/**
 * Refunds (document.md §3.7). A refund is a request, then a decision by someone
 * else: money leaves only on a processed refund, which is the one thing that
 * subtracts from an invoice's paid total. Requesting and rejecting move nothing.
 * Separation of duties — the approver cannot be the requester.
 */
class RefundsController extends AppController
{
    /**
     * POST /refunds — a bursar or administrator requests a refund against a
     * completed payment. Moves no money.
     */
    public function request(): Response
    {
        return $this->retired();
    }

    /**
     * POST /refunds/{id}/process — an administrator other than the requester
     * approves. This is the only point money leaves.
     */
    public function process(string $id): Response
    {
        return $this->retired();
    }

    /**
     * POST /refunds/{id}/reject — an administrator declines. Nothing moves; the
     * reserved amount is freed.
     */
    public function reject(string $id): Response
    {
        return $this->retired();
    }

    private function retired(): Response
    {
        return $this->json([
            'error' => 'This mutable refund endpoint has been retired. Use finance adjustment requests and payout confirmation.',
        ], 410);
    }
}
