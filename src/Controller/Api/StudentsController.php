<?php
declare(strict_types=1);

namespace App\Controller\Api;

use Cake\Http\Exception\NotFoundException;

/**
 * Students API — the pilot resource demonstrating the full pattern:
 * REST CRUD plus domain-specific custom actions, all reusing the existing
 * StudentsTable model layer (no business logic duplicated here).
 *
 * Routes (see config/routes.php):
 *   GET    /api/v1/students                 index (filter + paginate + search)
 *   GET    /api/v1/students/{id}            view
 *   POST   /api/v1/students                 add
 *   PUT    /api/v1/students/{id}            edit
 *   PATCH  /api/v1/students/{id}            edit
 *   DELETE /api/v1/students/{id}            delete
 *   GET    /api/v1/students/lookup?regno=   lookup by registration number
 *   GET    /api/v1/students/{id}/results    hasMany results
 *   GET    /api/v1/students/{id}/invoices   hasMany invoices
 *   GET    /api/v1/students/{id}/transactions hasMany transactions
 */
class StudentsController extends AppController
{
    /**
     * @var \App\Model\Table\StudentsTable
     */
    protected $Students;

    /**
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Students = $this->fetchTable('Students');

        // The model declares several belongsTo joins as INNER, which would drop
        // students that have a null FK from read results. Force LEFT for the API.
        foreach (['Departments', 'States', 'Countries', 'Lgas', 'Users', 'Sparents'] as $assoc) {
            if ($this->Students->hasAssociation($assoc)) {
                $this->Students->getAssociation($assoc)->setJoinType('LEFT');
            }
        }
    }

    /**
     * List students with optional filters, search and pagination.
     *
     * @return \Cake\Http\Response
     */
    public function index()
    {
        $this->request->allowMethod(['get']);
        ['page' => $page, 'limit' => $limit] = $this->paginationParams();

        $query = $this->Students->find()->contain(['Departments']);

        // Exact-match filters.
        foreach (['department_id', 'class_arm_id', 'level_id', 'session_id', 'status', 'studentstatus', 'gender'] as $field) {
            $value = $this->request->getQuery($field);
            if ($value !== null && $value !== '') {
                $query->where(['Students.' . $field => $value]);
            }
        }

        // Free-text search across name / regno / email.
        $search = trim((string)$this->request->getQuery('q'));
        if ($search !== '') {
            $like = '%' . $search . '%';
            $query->where(['OR' => [
                'Students.fname LIKE' => $like,
                'Students.lname LIKE' => $like,
                'Students.regno LIKE' => $like,
                'Students.email LIKE' => $like,
            ]]);
        }

        $total = (clone $query)->count();
        $rows = $query->orderDesc('Students.id')
            ->limit($limit)
            ->offset(($page - 1) * $limit)
            ->all()
            ->map(fn ($s) => $this->transform($s))
            ->toList();

        return $this->respond($rows, 200, [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'pages' => (int)ceil($total / $limit),
        ]);
    }

    /**
     * View a single student with associations.
     *
     * @param string|null $id Student id.
     * @return \Cake\Http\Response
     */
    public function view($id = null)
    {
        $this->request->allowMethod(['get']);
        $student = $this->findStudentOrFail((int)$id, [
            'Departments', 'States', 'Countries', 'Lgas', 'Users', 'Sparents',
        ]);

        return $this->respond($this->transform($student, true));
    }

    /**
     * Create a student.
     *
     * @return \Cake\Http\Response
     */
    public function add()
    {
        $this->request->allowMethod(['post']);
        $this->requireRole([1, 5, 7]); // admins (or API-key clients)

        $student = $this->Students->newEntity($this->request->getData());
        if (!$this->Students->save($student)) {
            return $this->respondError('Validation failed.', 422, $student->getErrors());
        }

        return $this->respond($this->transform($student), 201);
    }

    /**
     * Update a student.
     *
     * @param string|null $id Student id.
     * @return \Cake\Http\Response
     */
    public function edit($id = null)
    {
        $this->request->allowMethod(['put', 'patch']);
        $this->requireRole([1, 5, 7]);

        $student = $this->findStudentOrFail((int)$id);
        $student = $this->Students->patchEntity($student, $this->request->getData());
        if (!$this->Students->save($student)) {
            return $this->respondError('Validation failed.', 422, $student->getErrors());
        }

        return $this->respond($this->transform($student));
    }

    /**
     * Delete a student.
     *
     * @param string|null $id Student id.
     * @return \Cake\Http\Response
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['delete']);
        $this->requireRole([1, 5], false); // super/admin users only, no API keys

        $student = $this->findStudentOrFail((int)$id);
        if (!$this->Students->delete($student)) {
            return $this->respondError('Could not delete student.', 409);
        }

        return $this->respond(['deleted' => true, 'id' => (int)$id]);
    }

    /**
     * Look up a student by registration number.
     *
     * @return \Cake\Http\Response
     */
    public function lookup()
    {
        $this->request->allowMethod(['get']);
        $regno = trim((string)$this->request->getQuery('regno'));
        if ($regno === '') {
            return $this->respondError('A regno query parameter is required.', 422);
        }

        $student = $this->Students->find()
            ->contain(['Departments'])
            ->where(['Students.regno' => $regno])
            ->first();
        if ($student === null) {
            return $this->respondError('No student found with that regno.', 404);
        }

        return $this->respond($this->transform($student, true));
    }

    /**
     * Related results for a student.
     *
     * @param string|null $id Student id.
     * @return \Cake\Http\Response
     */
    public function results($id = null)
    {
        return $this->relatedList((int)$id, 'Results');
    }

    /**
     * Related invoices for a student.
     *
     * @param string|null $id Student id.
     * @return \Cake\Http\Response
     */
    public function invoices($id = null)
    {
        return $this->relatedList((int)$id, 'Invoices');
    }

    /**
     * Related transactions for a student.
     *
     * @param string|null $id Student id.
     * @return \Cake\Http\Response
     */
    public function transactions($id = null)
    {
        return $this->relatedList((int)$id, 'Transactions');
    }

    /**
     * Generic hasMany lister for a student's related records.
     *
     * @param int $id Student id.
     * @param string $assoc Association name.
     * @return \Cake\Http\Response
     */
    protected function relatedList(int $id, string $assoc)
    {
        $this->request->allowMethod(['get']);
        $this->findStudentOrFail($id); // 404 if student missing

        if (!$this->Students->hasAssociation($assoc)) {
            return $this->respondError("Association {$assoc} is not available.", 404);
        }

        ['page' => $page, 'limit' => $limit] = $this->paginationParams();
        $table = $this->Students->getAssociation($assoc)->getTarget();
        $foreignKey = $this->Students->getAssociation($assoc)->getForeignKey();

        $query = $table->find()->where([$table->getAlias() . '.' . $foreignKey => $id]);
        $total = (clone $query)->count();
        $rows = $query->limit($limit)->offset(($page - 1) * $limit)->all()->toList();

        return $this->respond($rows, 200, [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'pages' => (int)ceil($total / $limit),
        ]);
    }

    /**
     * Fetch a student by id (LEFT joins) or throw 404.
     *
     * @param int $id Student id.
     * @param array $contain Associations to eager-load.
     * @return \App\Model\Entity\Student
     * @throws \Cake\Http\Exception\NotFoundException
     */
    protected function findStudentOrFail(int $id, array $contain = [])
    {
        $student = $this->Students->find()
            ->contain($contain)
            ->where(['Students.id' => $id])
            ->first();
        if ($student === null) {
            throw new NotFoundException('Student not found.');
        }

        return $student;
    }

    /**
     * Shape a Student entity for API output. Keeps the pilot explicit about what
     * gets exposed rather than dumping every column.
     *
     * @param \App\Model\Entity\Student $s Student entity.
     * @param bool $full Include the extended field set.
     * @return array
     */
    protected function transform($s, bool $full = false): array
    {
        $out = [
            'id' => $s->id,
            'regno' => $s->regno,
            'fname' => $s->fname,
            'lname' => $s->lname,
            'mname' => $s->mname,
            'email' => $s->email,
            'phone' => $s->phone,
            'gender' => $s->gender,
            'status' => $s->status,
            'studentstatus' => $s->studentstatus,
            'department_id' => $s->department_id,
            'department' => isset($s->department) ? ($s->department->name ?? null) : null,
            'class_arm_id' => $s->class_arm_id,
            'level_id' => $s->level_id,
            'session_id' => $s->session_id,
            'passporturl' => $s->passporturl,
        ];

        if ($full) {
            $out += [
                'dob' => $s->dob,
                'address' => $s->address,
                'community' => $s->community,
                'religion' => $s->religion,
                'nationality_state' => isset($s->state) ? ($s->state->name ?? null) : null,
                'country' => isset($s->country) ? ($s->country->name ?? null) : null,
                'lga' => isset($s->lga) ? ($s->lga->name ?? null) : null,
                'user_id' => $s->user_id,
                'username' => isset($s->user) ? ($s->user->username ?? null) : null,
                'sparent_id' => $s->sparent_id,
                'admissiondate' => $s->admissiondate,
                'joindate' => $s->joindate,
                'application_no' => $s->application_no,
                'jambregno' => $s->jambregno,
                'faculty_id' => $s->faculty_id,
                'programme_id' => $s->programme_id,
            ];
        }

        return $out;
    }
}
