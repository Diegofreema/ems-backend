<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use Cake\Http\Response;

/**
 * The staff dashboard — one aggregate read (Policy: OFFICER). All figures are
 * computed by App\Ems\Dashboard; this controller only delegates.
 */
class DashboardController extends AppController
{
    public function index(): Response
    {
        return $this->json($this->dashboardEngine()->overview());
    }
}
