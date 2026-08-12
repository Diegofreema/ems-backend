<?php
declare(strict_types=1);

namespace App\Controller\Ems;

use Cake\Http\Response;

/**
 * Analytics dashboards (document.md §3.22) — four read-only derived views. No
 * storage: everything is computed on read by App\Ems\Analytics from the tables
 * the other modules mutate. Staff-only (administrator/registrar/bursar) — these
 * expose whole-school figures a class teacher, parent or student never sees.
 */
class AnalyticsController extends AppController
{
    /** GET /analytics/overview */
    public function overview(): Response
    {
        return $this->json($this->analyticsEngine()->overview());
    }

    /** GET /analytics/attendance */
    public function attendance(): Response
    {
        return $this->json($this->analyticsEngine()->attendanceInsights());
    }

    /** GET /analytics/academics */
    public function academics(): Response
    {
        return $this->json($this->analyticsEngine()->academicPerformance());
    }

    /** GET /analytics/enrolment */
    public function enrolment(): Response
    {
        return $this->json($this->analyticsEngine()->enrolmentTrends());
    }
}
