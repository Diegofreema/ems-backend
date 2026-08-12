<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\AppController;
use Cake\TestSuite\TestCase;

final class LegacyOnlinePaymentsTest extends TestCase
{
    public function testLegacyOnlinePaymentActionsAreClosed(): void
    {
        $this->assertTrue(AppController::isLegacyOnlinePaymentAction('Invoices', 'verifyetransact'));
        $this->assertTrue(AppController::isLegacyOnlinePaymentAction('Transactions', 'paymentverificationstack'));
        $this->assertTrue(AppController::isLegacyOnlinePaymentAction('Students', 'gotopaystack'));
        $this->assertTrue(AppController::isLegacyOnlinePaymentAction('Sparents', 'paymentverificationtest'));
        $this->assertFalse(AppController::isLegacyOnlinePaymentAction('Invoices', 'getreceipt'));
        $this->assertFalse(AppController::isLegacyOnlinePaymentAction('Ems\\Payments', 'confirm'));
    }
}
