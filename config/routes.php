<?php
/**
 * Routes configuration.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * It's loaded within the context of `Application::routes()` method which
 * receives a `RouteBuilder` instance `$routes` as method argument.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

/*
 * The default class to use for all routes
 *
 * The following route classes are supplied with CakePHP and are appropriate
 * to set as the default:
 *
 * - Route
 * - InflectedRoute
 * - DashedRoute
 *
 * If no call is made to `Router::defaultRouteClass()`, the class used is
 * `Route` (`Cake\Routing\Route\Route`)
 *
 * Note that `Route` does not do any inflections on URLs which will result in
 * inconsistently cased URLs when used with `:plugin`, `:controller` and
 * `:action` markers.
 */
/** @var \Cake\Routing\RouteBuilder $routes */
$routes->setRouteClass(DashedRoute::class);


/*
 * ---------------------------------------------------------------------------
 * EMS contract API (document.md) — the surface the React ems frontend
 * consumes. Its controllers live in src/Controller/Ems/, use `ems_` tables,
 * and accept their own token type.
 *
 * Layout (§1.1): tenant-less /auth/* + /schools/by-slug/{slug}; everything
 * else under /schools/{schoolId}/... Static path segments are connected
 * BEFORE {id} routes so UUIDs never shadow them.
 * ---------------------------------------------------------------------------
 */
$routes->prefix('Ems', ['path' => '/api/ems'], function (RouteBuilder $builder) {
    $builder->setExtensions(['json']);

    // --- Auth (§3.18, tenant-less) ---
    $builder->post('/auth/sign-in', ['controller' => 'Auth', 'action' => 'signIn']);
    $builder->post('/auth/register-school', ['controller' => 'Auth', 'action' => 'registerSchool']);

    // Subject catalogue (Stage 0 completeness pass) — administrator-only.
    $builder->get('/schools/{schoolId}/subjects', ['controller' => 'Subjects', 'action' => 'index']);
    $builder->post('/schools/{schoolId}/subjects', ['controller' => 'Subjects', 'action' => 'add']);
    $builder->put('/schools/{schoolId}/subjects/{id}', ['controller' => 'Subjects', 'action' => 'edit'])->setPass(['id']);
    $builder->post('/schools/{schoolId}/subjects/{id}/deactivate', ['controller' => 'Subjects', 'action' => 'deactivate'])->setPass(['id']);
    $builder->post('/schools/{schoolId}/subjects/{id}/reactivate', ['controller' => 'Subjects', 'action' => 'reactivate'])->setPass(['id']);
    $builder->delete('/schools/{schoolId}/subjects/{id}', ['controller' => 'Subjects', 'action' => 'delete'])->setPass(['id']);
    $builder->post('/auth/invite/lookup', ['controller' => 'Auth', 'action' => 'inviteLookup']);
    $builder->post('/auth/invite/accept', ['controller' => 'Auth', 'action' => 'inviteAccept']);
    $builder->post('/auth/reset/request', ['controller' => 'Auth', 'action' => 'resetRequest']);
    $builder->post('/auth/reset/confirm', ['controller' => 'Auth', 'action' => 'resetConfirm']);
    // E-mail verification for self-served registrations (§3.18): the mailed
    // 30-minute link proves the address; resend is enumeration-safe.
    $builder->post('/auth/verify-email', ['controller' => 'Auth', 'action' => 'verifyEmail']);
    $builder->post('/auth/verify-email/resend', ['controller' => 'Auth', 'action' => 'verifyResend']);
    // Second-factor sign-in step (§3.18): a 2FA account's correct password on an
    // untrusted device returns a challenge; this exchanges the emailed code for a
    // session. Public — the session does not exist yet.
    $builder->post('/auth/sign-in/verify', ['controller' => 'Auth', 'action' => 'signInVerify']);
    // Confirm a login-e-mail change from the link mailed to the NEW address.
    // Public: the link may be opened in a logged-out browser.
    $builder->post('/auth/email-change/verify', ['controller' => 'Auth', 'action' => 'emailChangeVerify']);
    // Silent re-auth (token-at-rest hardening): reads the httpOnly refresh cookie,
    // rotates it, mints a fresh short-lived access token. logout revokes the family.
    $builder->post('/auth/refresh', ['controller' => 'Auth', 'action' => 'refresh']);
    $builder->post('/auth/logout', ['controller' => 'Auth', 'action' => 'logout']);

    // Slug → school resolution (authenticated, membership-checked).
    $builder->get('/schools/by-slug/{slug}', ['controller' => 'Schools', 'action' => 'bySlug'])
        ->setPass(['slug']);

    // --- Public admissions (§3.6, unauthenticated) ---
    // Intake page reads by slug; the submit is keyed by schoolId (the public
    // apply screen already resolved the tenant). Both are public actions.
    $builder->get('/public/apply/{slug}', ['controller' => 'Public', 'action' => 'intake'])
        ->setPass(['slug']);
    $builder->post('/public/schools/{schoolId}/apply', ['controller' => 'Public', 'action' => 'apply'])
        ->setPass(['schoolId']);

    // Book-a-demo lead capture: a prospect has no school, so this is tenant-less.
    $builder->post('/public/demo-requests', ['controller' => 'DemoRequests', 'action' => 'create']);

    // --- Platform: demo-requests inbox (CRM-lite) — tenant-less, platform staff.
    // Authenticated (not public); the PLATFORM tier in App\Ems\Policy gates every
    // action to the platform_staff role. Static /summary before the {id} route.
    $builder->get('/platform/demo-requests/summary', ['controller' => 'DemoInbox', 'action' => 'summary']);
    $builder->get('/platform/demo-requests', ['controller' => 'DemoInbox', 'action' => 'index']);
    $builder->get('/platform/demo-requests/{id}', ['controller' => 'DemoInbox', 'action' => 'view'])
        ->setPass(['id']);
    $builder->patch('/platform/demo-requests/{id}/status', ['controller' => 'DemoInbox', 'action' => 'updateStatus'])
        ->setPass(['id']);
    $builder->post('/platform/demo-requests/{id}/notes', ['controller' => 'DemoInbox', 'action' => 'addNote'])
        ->setPass(['id']);

    // --- Signed-link redemption (§3.8) ---
    // Tenant-less: the reader is identified from their Bearer token inside the
    // action (the ordered 410/401/403/404 checks live there, not in auth).
    $builder->get('/files/{token}', ['controller' => 'Files', 'action' => 'download'])
        ->setPass(['token']);

    // --- Tenant scope: every other endpoint ---
    $builder->scope('/schools/{schoolId}', function (RouteBuilder $s) {
        // Students & guardians (§3.10)
        $s->get('/students/class-groups', ['controller' => 'Students', 'action' => 'classGroups']);
        $s->get('/students', ['controller' => 'Students', 'action' => 'index']);
        $s->post('/students/admit', ['controller' => 'Students', 'action' => 'admit']);
        $s->post('/students', ['controller' => 'Students', 'action' => 'add']);
        $s->get('/students/{id}', ['controller' => 'Students', 'action' => 'view'])->setPass(['id']);
        $s->put('/students/{id}', ['controller' => 'Students', 'action' => 'edit'])->setPass(['id']);
        $s->delete('/students/{id}', ['controller' => 'Students', 'action' => 'delete'])->setPass(['id']);
        $s->post('/students/{id}/withdraw', ['controller' => 'Students', 'action' => 'withdraw'])->setPass(['id']);
        $s->post('/students/{id}/class', ['controller' => 'Students', 'action' => 'assignClass'])->setPass(['id']);
        $s->get('/students/{id}/guardians', ['controller' => 'Students', 'action' => 'guardians'])->setPass(['id']);
        $s->post('/students/{id}/guardians', ['controller' => 'Students', 'action' => 'addGuardian'])->setPass(['id']);
        $s->get('/students/{id}/attendance', ['controller' => 'Students', 'action' => 'attendance'])->setPass(['id']);
        $s->get('/students/{id}/academics', ['controller' => 'Students', 'action' => 'academics'])->setPass(['id']);
        $s->get('/students/{id}/enrolments', ['controller' => 'Students', 'action' => 'enrolments'])->setPass(['id']);
        $s->put('/guardians/{id}', ['controller' => 'Guardians', 'action' => 'edit'])->setPass(['id']);
        $s->post('/guardians/{id}/primary', ['controller' => 'Guardians', 'action' => 'setPrimary'])->setPass(['id']);
        $s->delete('/guardians/{id}', ['controller' => 'Guardians', 'action' => 'delete'])->setPass(['id']);

        // Teachers (§3.11) + teacher day schedule (§3.12)
        $s->get('/teachers/subjects', ['controller' => 'Teachers', 'action' => 'subjects']);
        $s->get('/teachers', ['controller' => 'Teachers', 'action' => 'index']);
        $s->post('/teachers', ['controller' => 'Teachers', 'action' => 'add']);
        $s->get('/teachers/{id}', ['controller' => 'Teachers', 'action' => 'view'])->setPass(['id']);
        $s->put('/teachers/{id}', ['controller' => 'Teachers', 'action' => 'edit'])->setPass(['id']);
        $s->delete('/teachers/{id}', ['controller' => 'Teachers', 'action' => 'delete'])->setPass(['id']);
        $s->post('/teachers/{id}/mark-former', ['controller' => 'Teachers', 'action' => 'markFormer'])->setPass(['id']);
        $s->get('/teachers/{id}/assignments', ['controller' => 'Teachers', 'action' => 'assignments'])->setPass(['id']);
        $s->get('/teachers/{id}/day', ['controller' => 'Teachers', 'action' => 'day'])->setPass(['id']);

        // Classes, timetable & registers (§3.12)
        $s->get('/classes/levels', ['controller' => 'Classes', 'action' => 'levels']);
        $s->get('/classes/options', ['controller' => 'Classes', 'action' => 'options']);
        $s->get('/classes', ['controller' => 'Classes', 'action' => 'index']);
        $s->get('/classes/{id}', ['controller' => 'Classes', 'action' => 'view'])->setPass(['id']);
        $s->get('/classes/{id}/roster', ['controller' => 'Classes', 'action' => 'roster'])->setPass(['id']);
        $s->get('/classes/{id}/allocations', ['controller' => 'Classes', 'action' => 'allocations'])->setPass(['id']);
        $s->get('/classes/{id}/timetable', ['controller' => 'Classes', 'action' => 'timetable'])->setPass(['id']);
        $s->get('/classes/{id}/register', ['controller' => 'Classes', 'action' => 'register'])->setPass(['id']);
        $s->put('/classes/{id}/register', ['controller' => 'Classes', 'action' => 'saveRegister'])->setPass(['id']);
        $s->post('/classes', ['controller' => 'Classes', 'action' => 'add']);
        $s->put('/classes/{id}', ['controller' => 'Classes', 'action' => 'edit'])->setPass(['id']);
        $s->delete('/classes/{id}', ['controller' => 'Classes', 'action' => 'delete'])->setPass(['id']);
        $s->post('/classes/{id}/allocations', ['controller' => 'Classes', 'action' => 'addAllocation'])->setPass(['id']);
        $s->put('/classes/{id}/allocations/{allocationId}', ['controller' => 'Classes', 'action' => 'editAllocation'])->setPass(['id', 'allocationId']);
        $s->delete('/classes/{id}/allocations/{allocationId}', ['controller' => 'Classes', 'action' => 'deleteAllocation'])->setPass(['id', 'allocationId']);
        $s->post('/classes/{id}/timetable', ['controller' => 'Classes', 'action' => 'addSlot'])->setPass(['id']);
        $s->put('/classes/{id}/timetable/{slotId}', ['controller' => 'Classes', 'action' => 'editSlot'])->setPass(['id', 'slotId']);
        $s->delete('/classes/{id}/timetable/{slotId}', ['controller' => 'Classes', 'action' => 'deleteSlot'])->setPass(['id', 'slotId']);

        // Calendar (§3.13)
        $s->get('/calendar/sessions', ['controller' => 'Calendar', 'action' => 'sessions']);
        $s->post('/calendar/sessions', ['controller' => 'Calendar', 'action' => 'createSession']);
        $s->put('/calendar/sessions/{id}', ['controller' => 'Calendar', 'action' => 'updateSession'])->setPass(['id']);
        $s->post('/calendar/sessions/{id}/terms', ['controller' => 'Calendar', 'action' => 'createTerm'])->setPass(['id']);
        $s->post('/calendar/sessions/{id}/close', ['controller' => 'Calendar', 'action' => 'closeSession'])->setPass(['id']);
        $s->post('/calendar/sessions/{id}/reopen', ['controller' => 'Calendar', 'action' => 'reopenSession'])->setPass(['id']);
        $s->put('/calendar/terms/{id}/dates', ['controller' => 'Calendar', 'action' => 'updateTermDates'])->setPass(['id']);
        $s->post('/calendar/terms/{id}/close', ['controller' => 'Calendar', 'action' => 'closeTerm'])->setPass(['id']);
        $s->post('/calendar/terms/{id}/reopen', ['controller' => 'Calendar', 'action' => 'reopenTerm'])->setPass(['id']);
        $s->get('/calendar/audit', ['controller' => 'Calendar', 'action' => 'calendarAudit']);

        // Campuses (§3.13)
        $s->get('/campuses', ['controller' => 'Campuses', 'action' => 'index']);
        $s->post('/campuses', ['controller' => 'Campuses', 'action' => 'add']);
        $s->put('/campuses/{id}', ['controller' => 'Campuses', 'action' => 'edit'])->setPass(['id']);
        $s->post('/campuses/{id}/active', ['controller' => 'Campuses', 'action' => 'setActive'])->setPass(['id']);

        // Settings: school profile + accounts (§3.14)
        $s->get('/school', ['controller' => 'School', 'action' => 'view']);
        $s->put('/school', ['controller' => 'School', 'action' => 'update']);
        $s->get('/users', ['controller' => 'Users', 'action' => 'index']);
        $s->post('/users/invite', ['controller' => 'Users', 'action' => 'invite']);
        $s->put('/users/{id}/role', ['controller' => 'Users', 'action' => 'updateRole'])->setPass(['id']);
        $s->put('/users/{id}/link', ['controller' => 'Users', 'action' => 'updateLink'])->setPass(['id']);
        $s->put('/users/{id}/status', ['controller' => 'Users', 'action' => 'updateStatus'])->setPass(['id']);
        $s->delete('/users/{id}/invite', ['controller' => 'Users', 'action' => 'revokeInvite'])->setPass(['id']);

        // --- Admissions (§3.6) — static segments before {id} ---
        $s->get('/admission-cycles/open', ['controller' => 'Admissions', 'action' => 'openCycle']);
        $s->post('/admission-cycles', ['controller' => 'Admissions', 'action' => 'addCycle']);
        $s->put('/admission-cycles/{id}', ['controller' => 'Admissions', 'action' => 'editCycle'])->setPass(['id']);
        $s->post('/admission-cycles/{id}/close', ['controller' => 'Admissions', 'action' => 'closeCycle'])->setPass(['id']);
        $s->get('/admission-cycles', ['controller' => 'Admissions', 'action' => 'cycles']);
        $s->get('/applications/summary', ['controller' => 'Admissions', 'action' => 'summary']);
        $s->get('/applications', ['controller' => 'Admissions', 'action' => 'index']);
        $s->get('/applications/{id}', ['controller' => 'Admissions', 'action' => 'view'])->setPass(['id']);
        $s->post('/applications/{id}/review', ['controller' => 'Admissions', 'action' => 'review'])->setPass(['id']);
        $s->post('/applications/{id}/enrol', ['controller' => 'Admissions', 'action' => 'enrol'])->setPass(['id']);

        // --- Documents & signed links (§3.8) ---
        $s->get('/documents', ['controller' => 'Documents', 'action' => 'index']);
        $s->post('/documents', ['controller' => 'Documents', 'action' => 'add']);
        $s->post('/documents/{id}/verify', ['controller' => 'Documents', 'action' => 'verify'])->setPass(['id']);
        $s->post('/documents/{id}/link', ['controller' => 'Documents', 'action' => 'link'])->setPass(['id']);
        $s->delete('/documents/{id}', ['controller' => 'Documents', 'action' => 'remove'])->setPass(['id']);

        // --- Exams, sittings, papers, results (§3.1) — static/sub paths first ---
        $s->delete('/exam-schedules/{id}', ['controller' => 'Exams', 'action' => 'removeSchedule'])->setPass(['id']);
        $s->get('/exams', ['controller' => 'Exams', 'action' => 'index']);
        $s->post('/exams', ['controller' => 'Exams', 'action' => 'add']);
        $s->get('/exams/{id}/schedules', ['controller' => 'Exams', 'action' => 'schedules'])->setPass(['id']);
        $s->post('/exams/{id}/schedules', ['controller' => 'Exams', 'action' => 'addSchedule'])->setPass(['id']);
        $s->get('/exams/{id}/paper', ['controller' => 'Exams', 'action' => 'paper'])->setPass(['id']);
        $s->put('/exams/{id}/paper', ['controller' => 'Exams', 'action' => 'savePaper'])->setPass(['id']);
        $s->get('/exams/{id}/gradesheet', ['controller' => 'Exams', 'action' => 'gradesheet'])->setPass(['id']);
        $s->put('/exams/{id}/grades', ['controller' => 'Exams', 'action' => 'saveGrades'])->setPass(['id']);
        $s->get('/exams/{id}/broadsheet', ['controller' => 'Exams', 'action' => 'broadsheet'])->setPass(['id']);
        $s->get('/exams/{id}/report-card/{studentId}', ['controller' => 'Exams', 'action' => 'reportCard'])->setPass(['id', 'studentId']);
        $s->get('/exams/{id}/releases', ['controller' => 'Exams', 'action' => 'releases'])->setPass(['id']);
        $s->get('/exams/{id}/release-preview', ['controller' => 'Exams', 'action' => 'releasePreview'])->setPass(['id']);
        $s->post('/exams/{id}/release', ['controller' => 'Exams', 'action' => 'release'])->setPass(['id']);
        $s->post('/exams/{id}/reopen', ['controller' => 'Exams', 'action' => 'reopen'])->setPass(['id']);
        $s->post('/exams/{id}/start-grading', ['controller' => 'Exams', 'action' => 'startGrading'])->setPass(['id']);
        $s->get('/exams/{id}', ['controller' => 'Exams', 'action' => 'view'])->setPass(['id']);
        $s->put('/exams/{id}', ['controller' => 'Exams', 'action' => 'edit'])->setPass(['id']);
        $s->delete('/exams/{id}', ['controller' => 'Exams', 'action' => 'delete'])->setPass(['id']);

        // --- Assessments / derived CA (§3.2) ---
        $s->get('/assessments', ['controller' => 'Assessments', 'action' => 'index']);
        $s->post('/assessments', ['controller' => 'Assessments', 'action' => 'add']);
        $s->get('/assessments/{id}', ['controller' => 'Assessments', 'action' => 'view'])->setPass(['id']);
        $s->put('/assessments/{id}', ['controller' => 'Assessments', 'action' => 'edit'])->setPass(['id']);
        $s->delete('/assessments/{id}', ['controller' => 'Assessments', 'action' => 'delete'])->setPass(['id']);
        $s->post('/assessments/{id}/status', ['controller' => 'Assessments', 'action' => 'setStatus'])->setPass(['id']);
        $s->get('/assessments/{id}/scoresheet', ['controller' => 'Assessments', 'action' => 'scoresheet'])->setPass(['id']);
        $s->put('/assessments/{id}/scores', ['controller' => 'Assessments', 'action' => 'saveScores'])->setPass(['id']);

        // --- Grading schemes (§3.3) ---
        $s->get('/grading/active', ['controller' => 'Grading', 'action' => 'active']);
        $s->get('/grading/history', ['controller' => 'Grading', 'action' => 'history']);
        $s->put('/grading', ['controller' => 'Grading', 'action' => 'update']);

        // --- Question bank (§3.4) — static segment before {id} ---
        $s->get('/questions/subjects', ['controller' => 'Questions', 'action' => 'subjects']);
        $s->get('/questions', ['controller' => 'Questions', 'action' => 'index']);
        $s->post('/questions', ['controller' => 'Questions', 'action' => 'add']);
        $s->get('/questions/{id}', ['controller' => 'Questions', 'action' => 'view'])->setPass(['id']);
        $s->put('/questions/{id}', ['controller' => 'Questions', 'action' => 'edit'])->setPass(['id']);
        $s->delete('/questions/{id}', ['controller' => 'Questions', 'action' => 'delete'])->setPass(['id']);

        // --- Transcripts (§3.5, read-only) ---
        $s->get('/students/{id}/transcript', ['controller' => 'Transcripts', 'action' => 'show'])->setPass(['id']);

        // --- Fees: structures & awards (§3.7) ---
        $s->get('/fee-plan-versions', ['controller' => 'FeePlanVersions', 'action' => 'index']);
        $s->post('/fee-plan-versions', ['controller' => 'FeePlanVersions', 'action' => 'add']);
        $s->post('/fee-plan-versions/{id}/decision', ['controller' => 'FeePlanVersions', 'action' => 'decide'])->setPass(['id']);
        $s->get('/fee-structures', ['controller' => 'FeeStructures', 'action' => 'index']);
        $s->post('/fee-structures', ['controller' => 'FeeStructures', 'action' => 'add']);
        $s->get('/fee-structures/{id}', ['controller' => 'FeeStructures', 'action' => 'view'])->setPass(['id']);
        $s->put('/fee-structures/{id}', ['controller' => 'FeeStructures', 'action' => 'edit'])->setPass(['id']);
        $s->delete('/fee-structures/{id}', ['controller' => 'FeeStructures', 'action' => 'delete'])->setPass(['id']);
        $s->get('/fee-awards', ['controller' => 'FeeAwards', 'action' => 'index']);
        $s->post('/fee-awards', ['controller' => 'FeeAwards', 'action' => 'add']);
        $s->post('/fee-awards/{id}/end', ['controller' => 'FeeAwards', 'action' => 'endAward'])->setPass(['id']);
        $s->post('/fee-awards/{id}/decision', ['controller' => 'FeeAwards', 'action' => 'decide'])->setPass(['id']);
        $s->get('/fee-award-end-requests', ['controller' => 'FeeAwards', 'action' => 'endRequests']);
        $s->post('/fee-award-end-requests/{id}/decision', ['controller' => 'FeeAwards', 'action' => 'decideEnd'])->setPass(['id']);

        // --- Fees: invoices (§3.7) — static/sub paths before {id} ---
        $s->get('/invoices', ['controller' => 'Invoices', 'action' => 'index']);
        $s->post('/invoices/preview', ['controller' => 'Invoices', 'action' => 'preview']);
        $s->post('/invoices', ['controller' => 'Invoices', 'action' => 'add']);
        $s->post('/invoices/{id}/cancel', ['controller' => 'Invoices', 'action' => 'cancel'])->setPass(['id']);
        $s->post('/invoices/{id}/reschedule', ['controller' => 'Invoices', 'action' => 'reschedule'])->setPass(['id']);
        $s->post('/invoices/{id}/payments', ['controller' => 'Invoices', 'action' => 'addPayment'])->setPass(['id']);
        $s->post('/invoices/{id}/payment-submissions', ['controller' => 'Invoices', 'action' => 'submitPayment'])->setPass(['id']);
        $s->post('/invoices/{id}/change-requests', ['controller' => 'Invoices', 'action' => 'requestChange'])->setPass(['id']);
        $s->post('/invoices/{id}/checkout', ['controller' => 'Invoices', 'action' => 'checkout'])->setPass(['id']);
        $s->get('/invoices/{id}', ['controller' => 'Invoices', 'action' => 'view'])->setPass(['id']);
        $s->get('/invoice-change-requests', ['controller' => 'InvoiceChangeRequests', 'action' => 'index']);
        $s->post('/invoice-change-requests/{id}/decision', ['controller' => 'InvoiceChangeRequests', 'action' => 'decide'])->setPass(['id']);

        // --- Fees: bulk invoicing (§3.7) — static/sub paths before {id} ---
        $s->post('/invoice-batches/preview', ['controller' => 'InvoiceBatches', 'action' => 'preview']);
        $s->post('/invoice-batches', ['controller' => 'InvoiceBatches', 'action' => 'add']);
        $s->get('/invoice-batches', ['controller' => 'InvoiceBatches', 'action' => 'index']);
        $s->post('/invoice-batches/{id}/decision', ['controller' => 'InvoiceBatches', 'action' => 'decide'])->setPass(['id']);
        $s->get('/invoice-batches/{id}', ['controller' => 'InvoiceBatches', 'action' => 'view'])->setPass(['id']);

        // --- Fees: reminders (§3.7) — overdue / due-soon nudges to families ---
        $s->post('/fee-reminders/preview', ['controller' => 'FeeReminders', 'action' => 'preview']);
        $s->post('/fee-reminders', ['controller' => 'FeeReminders', 'action' => 'send']);
        $s->get('/fee-reminders', ['controller' => 'FeeReminders', 'action' => 'index']);

        // --- Fees: payments, checkout confirm & refunds (§3.7) ---
        $s->post('/payments/{id}/reverse', ['controller' => 'Payments', 'action' => 'reverse'])->setPass(['id']);
        $s->post('/payments/{id}/adjustment-requests', ['controller' => 'Payments', 'action' => 'requestAdjustment'])->setPass(['id']);
        $s->get('/finance-adjustments', ['controller' => 'FinanceAdjustments', 'action' => 'index']);
        $s->post('/finance-adjustments/{id}/decision', ['controller' => 'FinanceAdjustments', 'action' => 'decide'])->setPass(['id']);
        $s->post('/finance-adjustments/{id}/payout-confirmation', ['controller' => 'FinanceAdjustments', 'action' => 'confirmPayout'])->setPass(['id']);
        $s->get('/payments/{id}/receipt', ['controller' => 'Payments', 'action' => 'receipt'])->setPass(['id']);
        $s->post('/checkout/{id}/confirm', ['controller' => 'Payments', 'action' => 'confirm'])->setPass(['id']);
        $s->get('/payment-submissions', ['controller' => 'PaymentSubmissions', 'action' => 'index']);
        $s->get('/payment-submissions/{id}/evidence', ['controller' => 'PaymentSubmissions', 'action' => 'evidence'])->setPass(['id']);
        $s->post('/payment-submissions/{id}/decision', ['controller' => 'PaymentSubmissions', 'action' => 'decide'])->setPass(['id']);
        $s->post('/statement-batches', ['controller' => 'StatementBatches', 'action' => 'add']);
        $s->get('/statement-batches', ['controller' => 'StatementBatches', 'action' => 'index']);
        $s->get('/statement-batches/{id}/rows', ['controller' => 'StatementBatches', 'action' => 'rows'])->setPass(['id']);
        $s->post('/cash-batches', ['controller' => 'CashBatches', 'action' => 'add']);
        $s->get('/cash-batches', ['controller' => 'CashBatches', 'action' => 'index']);
        $s->post('/cash-batches/{id}/close', ['controller' => 'CashBatches', 'action' => 'close'])->setPass(['id']);
        $s->post('/refunds', ['controller' => 'Refunds', 'action' => 'request']);
        $s->post('/refunds/{id}/process', ['controller' => 'Refunds', 'action' => 'process'])->setPass(['id']);
        $s->post('/refunds/{id}/reject', ['controller' => 'Refunds', 'action' => 'reject'])->setPass(['id']);

        // --- Fees: reconciliation, report & ledger (§3.7) ---
        $s->get('/reconciliation', ['controller' => 'Fees', 'action' => 'reconciliation']);
        $s->get('/fees/report', ['controller' => 'Fees', 'action' => 'report']);
        $s->get('/students/{id}/ledger', ['controller' => 'Invoices', 'action' => 'ledger'])->setPass(['id']);

        // ============================ PHASE 5 ============================

        // --- Staff dashboard — one aggregate read for the school's day ---
        $s->get('/dashboard', ['controller' => 'Dashboard', 'action' => 'index']);

        // --- Analytics (§3.22) ---
        $s->get('/analytics/overview', ['controller' => 'Analytics', 'action' => 'overview']);
        $s->get('/analytics/attendance', ['controller' => 'Analytics', 'action' => 'attendance']);
        $s->get('/analytics/academics', ['controller' => 'Analytics', 'action' => 'academics']);
        $s->get('/analytics/enrolment', ['controller' => 'Analytics', 'action' => 'enrolment']);

        // --- Portal (§3.19) --- (static /portal/dashboard before the {studentId} route)
        $s->get('/portal/identity', ['controller' => 'Portal', 'action' => 'identity']);
        $s->get('/portal/dashboard', ['controller' => 'Portal', 'action' => 'dashboard']);
        $s->get('/portal/notifications', ['controller' => 'Portal', 'action' => 'notifications']);
        $s->post('/portal/notifications/read', ['controller' => 'Portal', 'action' => 'markNotificationsRead']);
        // Family payment declarations — the deeper /payment-claims path before the
        // bare {studentId} overview.
        $s->get('/portal/wards/{studentId}/payment-claims', ['controller' => 'PortalPaymentClaims', 'action' => 'index'])
            ->setPass(['studentId']);
        $s->post('/portal/wards/{studentId}/payment-claims', ['controller' => 'PortalPaymentClaims', 'action' => 'add'])
            ->setPass(['studentId']);
        $s->get('/portal/wards/{studentId}', ['controller' => 'Portal', 'action' => 'wardOverview'])
            ->setPass(['studentId']);

        // --- Audit trail & privacy requests (§3.23) ---
        $s->get('/audit/events', ['controller' => 'Audit', 'action' => 'events']);
        $s->get('/privacy-requests', ['controller' => 'PrivacyRequests', 'action' => 'index']);
        $s->post('/privacy-requests', ['controller' => 'PrivacyRequests', 'action' => 'add']);
        $s->post('/privacy-requests/{id}/verify', ['controller' => 'PrivacyRequests', 'action' => 'verify'])->setPass(['id']);
        $s->post('/privacy-requests/{id}/decide', ['controller' => 'PrivacyRequests', 'action' => 'decide'])->setPass(['id']);
        $s->post('/privacy-requests/{id}/fulfil', ['controller' => 'PrivacyRequests', 'action' => 'fulfil'])->setPass(['id']);

        // --- Incidents (§3.24) ---
        $s->get('/incidents', ['controller' => 'Incidents', 'action' => 'index']);
        $s->get('/incidents/{id}', ['controller' => 'Incidents', 'action' => 'view'])->setPass(['id']);
        $s->post('/incidents', ['controller' => 'Incidents', 'action' => 'add']);
        $s->post('/incidents/{id}/advance', ['controller' => 'Incidents', 'action' => 'advance'])->setPass(['id']);
        $s->post('/incidents/{id}/notes', ['controller' => 'Incidents', 'action' => 'addNote'])->setPass(['id']);
        $s->post('/incidents/{id}/responders', ['controller' => 'Incidents', 'action' => 'addResponder'])->setPass(['id']);

        // --- Communication (§3.20) --- (static segments before {id}) ---
        $s->get('/announcements', ['controller' => 'Communication', 'action' => 'index']);
        $s->get('/announcements/feed', ['controller' => 'Communication', 'action' => 'feed']);
        $s->post('/announcements', ['controller' => 'Communication', 'action' => 'add']);
        $s->get('/notifications', ['controller' => 'Communication', 'action' => 'notifications']);
        $s->get('/me/preferences', ['controller' => 'Communication', 'action' => 'myPreferences']);
        $s->put('/me/preferences', ['controller' => 'Communication', 'action' => 'setPreference']);

        // --- Account & security (§3.18, self-service) --- (static before {id}) ---
        // Every authenticated member manages their OWN account here; the acting
        // user is the viewer, never a body-supplied id (Policy SELF tier).
        $s->get('/me/account', ['controller' => 'Account', 'action' => 'show']);
        $s->put('/me/account/profile', ['controller' => 'Account', 'action' => 'updateProfile']);
        $s->post('/me/account/password', ['controller' => 'Account', 'action' => 'changePassword']);
        $s->post('/me/account/email', ['controller' => 'Account', 'action' => 'requestEmailChange']);
        $s->post('/me/account/2fa/enable', ['controller' => 'Account', 'action' => 'enableTwoFactor']);
        $s->post('/me/account/2fa/confirm', ['controller' => 'Account', 'action' => 'confirmTwoFactor']);
        $s->post('/me/account/2fa/disable', ['controller' => 'Account', 'action' => 'disableTwoFactor']);
        $s->get('/me/account/sessions', ['controller' => 'Account', 'action' => 'sessions']);
        $s->post('/me/account/sessions/revoke-others', ['controller' => 'Account', 'action' => 'revokeOtherSessions']);
        $s->delete('/me/account/trusted-devices', ['controller' => 'Account', 'action' => 'forgetTrustedDevices']);
        $s->delete('/me/account/sessions/{id}', ['controller' => 'Account', 'action' => 'revokeSession'])->setPass(['id']);
        $s->get('/alerts', ['controller' => 'Communication', 'action' => 'alerts']);
        $s->post('/alerts/send', ['controller' => 'Communication', 'action' => 'sendAlert']);
        $s->get('/announcements/audience-preview', ['controller' => 'Communication', 'action' => 'audiencePreview']);
        $s->get('/announcements/{id}/delivery', ['controller' => 'Communication', 'action' => 'delivery'])->setPass(['id']);
        $s->post('/announcements/{id}/delivery/retry', ['controller' => 'Communication', 'action' => 'retry'])->setPass(['id']);
        $s->post('/announcements/{id}/deliver', ['controller' => 'Communication', 'action' => 'deliver'])->setPass(['id']);
        $s->post('/announcements/{id}/publish', ['controller' => 'Communication', 'action' => 'publish'])->setPass(['id']);
        $s->get('/announcements/{id}', ['controller' => 'Communication', 'action' => 'view'])->setPass(['id']);
        $s->put('/announcements/{id}', ['controller' => 'Communication', 'action' => 'edit'])->setPass(['id']);
        $s->delete('/announcements/{id}', ['controller' => 'Communication', 'action' => 'delete'])->setPass(['id']);

        // --- Reports (§3.21) ---
        $s->get('/reports/jobs', ['controller' => 'Reports', 'action' => 'jobs']);
        $s->post('/reports/jobs', ['controller' => 'Reports', 'action' => 'add']);
        $s->post('/reports/jobs/{id}/download', ['controller' => 'Reports', 'action' => 'download'])->setPass(['id']);

        // --- Register merge (§3.15) ---
        $s->get('/merge/candidates', ['controller' => 'Merge', 'action' => 'candidates']);
        $s->get('/merge/preview', ['controller' => 'Merge', 'action' => 'preview']);
        $s->post('/merge', ['controller' => 'Merge', 'action' => 'merge']);

        // --- Promotion (§3.16) ---
        $s->get('/promotion/preview', ['controller' => 'Promotion', 'action' => 'preview']);
        $s->post('/promotion/commit', ['controller' => 'Promotion', 'action' => 'commit']);

        // --- CSV imports (§3.17) --- (static segments before {batchId}) ---
        $s->get('/imports/template', ['controller' => 'Imports', 'action' => 'template']);
        $s->get('/imports', ['controller' => 'Imports', 'action' => 'index']);
        $s->post('/imports', ['controller' => 'Imports', 'action' => 'add']);
        $s->put('/imports/{batchId}/rows/{rowId}/decision', ['controller' => 'Imports', 'action' => 'setDecision'])->setPass(['batchId', 'rowId']);
        $s->post('/imports/{batchId}/skip-flagged', ['controller' => 'Imports', 'action' => 'skipFlagged'])->setPass(['batchId']);
        $s->post('/imports/{batchId}/commit', ['controller' => 'Imports', 'action' => 'commit'])->setPass(['batchId']);
        $s->post('/imports/{batchId}/discard', ['controller' => 'Imports', 'action' => 'discard'])->setPass(['batchId']);
        $s->get('/imports/{batchId}', ['controller' => 'Imports', 'action' => 'view'])->setPass(['batchId']);
    });

    // CORS preflight: answer OPTIONS under /api/ems before auth (204 in
    // Ems\AppController::beforeFilter).
    $builder->connect('/{controller}/**', ['action' => 'index', '_method' => 'OPTIONS']);
});
