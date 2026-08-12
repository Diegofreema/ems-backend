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

use Cake\Http\Middleware\CsrfProtectionMiddleware;
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
 * JSON REST API (stateless, token/API-key auth) — additive, does not affect
 * the existing web app. All controllers live in src/Controller/Api/.
 * ---------------------------------------------------------------------------
 */
$routes->prefix('Api', ['path' => '/api/v1'], function (RouteBuilder $builder) {
    $builder->setExtensions(['json']);

    // --- Authentication ---
    $builder->post('/auth/login', ['controller' => 'Auth', 'action' => 'login']);
    $builder->post('/auth/refresh', ['controller' => 'Auth', 'action' => 'refresh']);
    $builder->get('/auth/me', ['controller' => 'Auth', 'action' => 'me']);
    $builder->post('/auth/api-keys', ['controller' => 'Auth', 'action' => 'apiKeys']);

    // --- Students (pilot resource): custom actions BEFORE resources() so they
    //     are not shadowed by the generic /students/{id} route. ---
    $builder->get('/students/lookup', ['controller' => 'Students', 'action' => 'lookup']);
    $builder->get('/students/{id}/results', ['controller' => 'Students', 'action' => 'results'])
        ->setPass(['id'])->setPatterns(['id' => '\d+']);
    $builder->get('/students/{id}/invoices', ['controller' => 'Students', 'action' => 'invoices'])
        ->setPass(['id'])->setPatterns(['id' => '\d+']);
    $builder->get('/students/{id}/transactions', ['controller' => 'Students', 'action' => 'transactions'])
        ->setPass(['id'])->setPatterns(['id' => '\d+']);

    // Standard REST CRUD for students (index/view/add/edit/delete).
    $builder->resources('Students');

    // --- Phase 2: academic structure & staff (generic CRUD resources) ---
    // NOTE: 'Employees' omitted — the `employees` table does not exist in this
    // DB (staff records live in `teachers`). Controller kept for when it does.
    $phase2Resources = [
        'Teachers', 'Subjects', 'Departments', 'ClassArms',
        'Levels', 'Sessions', 'Semesters', 'Faculties', 'Courses', 'Sparents',
    ];
    foreach ($phase2Resources as $resource) {
        $builder->resources($resource);
    }

    // --- Phase 3: assessment ---
    // NOTE: exams/quizzes and their question/attempt tables do not exist in the
    // current `tss` database, so those resources are intentionally NOT routed
    // here (their controllers exist and can be enabled once the tables are
    // created). Only tables that exist are exposed: results, assignments,
    // setassignments (+ questions/student_answers via relations).
    // Custom actions first so they aren't shadowed by /{resource}/{id}.
    $builder->get('/results/transcript', ['controller' => 'Results', 'action' => 'transcript']);
    $builder->get('/results/pending-approval', ['controller' => 'Results', 'action' => 'pendingApproval']);
    $builder->post('/results/approve-batch', ['controller' => 'Results', 'action' => 'approveBatch']);
    $builder->post('/results/reject-batch', ['controller' => 'Results', 'action' => 'rejectBatch']);
    $builder->post('/results/{id}/approve', ['controller' => 'Results', 'action' => 'approve'])
        ->setPass(['id'])->setPatterns(['id' => '\d+']);
    $builder->post('/results/{id}/reject', ['controller' => 'Results', 'action' => 'reject'])
        ->setPass(['id'])->setPatterns(['id' => '\d+']);
    $builder->get('/setassignments/{id}/questions', ['controller' => 'Setassignments', 'action' => 'questions'])
        ->setPass(['id'])->setPatterns(['id' => '\d+']);
    $builder->get('/setassignments/{id}/submissions', ['controller' => 'Setassignments', 'action' => 'submissions'])
        ->setPass(['id'])->setPatterns(['id' => '\d+']);

    $phase3Resources = ['Results', 'Assignments', 'Setassignments'];
    foreach ($phase3Resources as $resource) {
        $builder->resources($resource);
    }

    // --- Phase 4: finance ---
    // Custom actions first. Most "custom" web actions map to generic filters:
    //   student invoices  = /invoices?student_id=
    //   pending payments  = /transactions?paystatus=initialized
    //   employee payslips = /payslips?teacher_id=
    $builder->post('/fees/{id}/activate', ['controller' => 'Fees', 'action' => 'activate'])
        ->setPass(['id'])->setPatterns(['id' => '\d+']);
    $builder->post('/fees/{id}/deactivate', ['controller' => 'Fees', 'action' => 'deactivate'])
        ->setPass(['id'])->setPatterns(['id' => '\d+']);

    $phase4Resources = [
        'Fees', 'Invoices', 'Transactions', 'Payslips', 'Sponsorships',
        'Spendings', 'Sponsorshippayments', 'Feeallocations', 'Paylogs', 'Donations',
    ];
    foreach ($phase4Resources as $resource) {
        $builder->resources($resource);
    }

    // --- Phase 5: attendance & scheduling ---
    // (admin_attendances, liveclasses, events tables don't exist in this DB, so
    //  those resources are not routed.)
    $builder->get('/attendances/roster', ['controller' => 'Attendances', 'action' => 'roster']);
    $builder->post('/attendances/mark', ['controller' => 'Attendances', 'action' => 'mark']);

    foreach (['Attendances', 'Timetables'] as $resource) {
        $builder->resources($resource);
    }

    // --- Phase 6: library & materials ---
    foreach (['Books', 'Borrowedbooks', 'Coursematerials', 'Eresources'] as $resource) {
        $builder->resources($resource);
    }

    // --- Phase 7: communication ---
    // (posts, comments, forum, postcategories, muteds tables don't exist here.)
    $builder->post('/news/{id}/publish', ['controller' => 'News', 'action' => 'publish'])
        ->setPass(['id'])->setPatterns(['id' => '\d+']);
    $builder->post('/news/{id}/unpublish', ['controller' => 'News', 'action' => 'unpublish'])
        ->setPass(['id'])->setPatterns(['id' => '\d+']);

    $phase7Resources = [
        'News', 'Notifications', 'Staffmessages', 'Studentmessages', 'Letters',
        'Subcategory', 'Votes', 'Topics', 'Trequests', 'Couriers',
    ];
    foreach ($phase7Resources as $resource) {
        $builder->resources($resource);
    }

    // --- Phase 8: reference/lookup tables + remaining resources (bulk CRUD) ---
    // Sensitive ones (Users, Admins, privileges, logins, logs, settings) enforce
    // admin-only reads and block API-key writes via the controller class.
    $phase8Resources = [
        'Admins', 'AdminsPrivileges', 'Admisionconditions', 'Approvedresults',
        'Cafcredit', 'Candidates', 'Categories', 'Constants', 'Countries',
        'Courseassignments', 'CourseassignmentsSubjects', 'Courseregistrations',
        'CourseregistrationsSubjects', 'DepartmentsFees', 'DepartmentsLevels',
        'DepartmentsProgrames', 'DepartmentsProgrammes', 'DepartmentsSemesters',
        'DepartmentsSubjects', 'FeesLevels', 'FeesStudents', 'Lgas', 'Logs',
        'Modes', 'Positions', 'Privileges', 'Programes', 'Programetypes', 'Roles',
        'Settings', 'SparentsStudents', 'Sponsors', 'SponsorshipsStudents',
        'Staffdepartments', 'Staffgrades', 'States', 'SubjectsStudents',
        'SubjectsTeachers', 'Userlogins', 'Users',
    ];
    foreach ($phase8Resources as $resource) {
        $builder->resources($resource);
    }

    // --- Phase 9: hostel ---
    $builder->get('/hostelrooms/{id}/students', ['controller' => 'Hostelrooms', 'action' => 'students'])
        ->setPass(['id'])->setPatterns(['id' => '\d+']);
    foreach (['Hostels', 'Hostelrooms', 'HostelroomsStudents'] as $resource) {
        $builder->resources($resource);
    }

    // CORS preflight: route ONLY OPTIONS requests under /api/v1 to a controller
    // so the base controller's beforeFilter can answer with 204 before auth.
    // (_method must live in the defaults array to actually restrict the method.)
    $builder->connect('/{controller}/**', ['action' => 'index', '_method' => 'OPTIONS']);
});

/*
 * ---------------------------------------------------------------------------
 * EMS contract API (document.md) — the surface the React ems frontend
 * consumes. Fully parallel to /api/v1: its own controllers in
 * src/Controller/Ems/, its own `ems_` tables, its own token type.
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

        // --- Fees: invoices (§3.7) — static/sub paths before {id} ---
        $s->get('/invoices', ['controller' => 'Invoices', 'action' => 'index']);
        $s->post('/invoices/preview', ['controller' => 'Invoices', 'action' => 'preview']);
        $s->post('/invoices', ['controller' => 'Invoices', 'action' => 'add']);
        $s->post('/invoices/{id}/cancel', ['controller' => 'Invoices', 'action' => 'cancel'])->setPass(['id']);
        $s->post('/invoices/{id}/reschedule', ['controller' => 'Invoices', 'action' => 'reschedule'])->setPass(['id']);
        $s->post('/invoices/{id}/payments', ['controller' => 'Invoices', 'action' => 'addPayment'])->setPass(['id']);
        $s->post('/invoices/{id}/payment-submissions', ['controller' => 'Invoices', 'action' => 'submitPayment'])->setPass(['id']);
        $s->post('/invoices/{id}/checkout', ['controller' => 'Invoices', 'action' => 'checkout'])->setPass(['id']);
        $s->get('/invoices/{id}', ['controller' => 'Invoices', 'action' => 'view'])->setPass(['id']);

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
        $s->get('/statement-batches/{id}/rows', ['controller' => 'StatementBatches', 'action' => 'rows'])->setPass(['id']);
        $s->post('/cash-batches', ['controller' => 'CashBatches', 'action' => 'add']);
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

$routes->scope('/', function (RouteBuilder $builder) {
    // Register scoped middleware for in scopes.
    //$builder->registerMiddleware('csrf', new CsrfProtectionMiddleware([
    //    'httpOnly' => true,
  //  ]));

    /*
     * Apply a middleware to the current route scope.
     * Requires middleware to be registered through `Application::routes()` with `registerMiddleware()`
     */
  //  $builder->applyMiddleware('csrf');

    /*
     * Here, we are connecting '/' (base path) to a controller called 'Pages',
     * its action called 'display', and we pass a param to select the view file
     * to use (in this case, templates/Pages/home.php)...
     */
    $builder->connect('/', ['controller' => 'Users', 'action' => 'login', 'home']);

    /*
     * ...and connect the rest of 'Pages' controller's URLs.
     */
    $builder->connect('/pages/*', ['controller' => 'Pages', 'action' => 'display']);

    $builder->scope('/api/admin-attendances', function (RouteBuilder $adminAttendancesRoutes) {
        $adminAttendancesRoutes->setExtensions(['json']);
        $adminAttendancesRoutes->connect('/', ['controller' => 'AdminAttendances', 'action' => 'index']);
        $adminAttendancesRoutes->connect('/index', ['controller' => 'AdminAttendances', 'action' => 'index']);
        $adminAttendancesRoutes->connect('/report', ['controller' => 'AdminAttendances', 'action' => 'report']);
        $adminAttendancesRoutes->connect('/print', ['controller' => 'AdminAttendances', 'action' => 'print']);
        $adminAttendancesRoutes->connect('/export', ['controller' => 'AdminAttendances', 'action' => 'export']);
    });
           
    /*
     * Connect catchall routes for all controllers.
     *
     * The `fallbacks` method is a shortcut for
     *
     * ```
     * $builder->connect('/:controller', ['action' => 'index']);
     * $builder->connect('/:controller/:action/*', []);
     * ```
     *
     * You can remove these routes once you've connected the
     * routes you want in your application.
     */
    $builder->fallbacks();
});

 $routes->scope('/apis', function (RouteBuilder $routes) {
            $routes->setExtensions(['json','xml']);
            $routes->resources('Apis');
        });


/*
 * If you need a different set of middleware or none at all,
 * open new scope and define routes there.
 *
 * ```
 * $routes->scope('/api', function (RouteBuilder $builder) {
 *     // No $builder->applyMiddleware() here.
 *     // Connect API actions here.
 * });
 * ```
 */
