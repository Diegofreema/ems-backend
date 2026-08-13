<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

/**
 * Attendances Model
 *
 * @property \App\Model\Table\StudentsTable&\Cake\ORM\Association\BelongsTo $Students
 * @property \App\Model\Table\TeachersTable&\Cake\ORM\Association\BelongsTo $Teachers
 * @property \App\Model\Table\DepartmentsTable&\Cake\ORM\Association\BelongsTo $Departments
 * @method \App\Model\Entity\Attendance newEmptyEntity()
 * @method \App\Model\Entity\Attendance newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\Attendance[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Attendance get($primaryKey, $options = [])
 * @method \App\Model\Entity\Attendance findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\Attendance patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Attendance[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\Attendance|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Attendance saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Attendance[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Attendance[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\Attendance[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\Attendance[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 */
class AttendancesTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('attendances');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Teachers', [
            'foreignKey' => 'teacher_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('ClassArms', [
            'foreignKey' => 'class_arm_id',
            'joinType' => 'LEFT',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('student_id')
            ->requirePresence('student_id', 'create')
            ->notEmptyString('student_id');

        $validator
            ->integer('teacher_id')
            ->requirePresence('teacher_id', 'create')
            ->notEmptyString('teacher_id');

        $validator
            ->integer('department_id')
            ->requirePresence('department_id', 'create')
            ->notEmptyString('department_id');

        $validator
            ->integer('class_arm_id')
            ->allowEmptyString('class_arm_id');

        $validator
            ->date('attendance_date')
            ->requirePresence('attendance_date', 'create')
            ->notEmptyDate('attendance_date');

        $validator
            ->scalar('status')
            ->maxLength('status', 20)
            ->requirePresence('status', 'create')
            ->notEmptyString('status')
            ->inList('status', ['present', 'absent', 'late', 'excused']);

        $validator
            ->scalar('notes')
            ->allowEmptyString('notes');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['student_id'], 'Students'));
        $rules->add($rules->existsIn(['teacher_id'], 'Teachers'));
        $rules->add($rules->existsIn(['department_id'], 'Departments'));

        // Prevent duplicate attendance for same student on same date
        $rules->add($rules->isUnique(['student_id', 'attendance_date'], 'Attendance already recorded for this student on this date'));

        return $rules;
    }

    /**
     * Get students in a department for attendance
     *
     * @param int $departmentId
     * @return \Cake\ORM\Query
     */
    public function getStudentsForAttendance(int $departmentId): Query
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');

        return $studentsTable->find()
            ->select(['id', 'fname', 'lname', 'regno', 'department_id'])
            ->where(['department_id' => $departmentId, 'status' => 'Admitted'])
            ->order(['fname' => 'ASC', 'lname' => 'ASC']);
    }

    /**
     * Get attendance for a specific date and department
     *
     * @param int $departmentId
     * @param string $date
     * @return \Cake\ORM\Query
     */
    public function getAttendanceForDate(int $departmentId, string $date): Query
    {
        return $this->find()
            ->contain(['Students'])
            ->where([
                'Attendances.department_id' => $departmentId,
                'Attendances.attendance_date' => $date,
            ])
            ->order(['Students.fname' => 'ASC', 'Students.lname' => 'ASC']);
    }

    /**
     * Get attendance statistics for a department
     *
     * @param int $departmentId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public function getAttendanceStats(int $departmentId, string $startDate, string $endDate): array
    {
        $stats = $this->find()
            ->select([
                'status',
                'count' => $this->find()->func()->count('*'),
            ])
            ->where([
                'Attendances.department_id' => $departmentId,
                'Attendances.attendance_date >=' => $startDate,
                'Attendances.attendance_date <=' => $endDate,
            ])
            ->group(['status'])
            ->toArray();

        $result = [
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'excused' => 0,
            'total' => 0,
        ];

        foreach ($stats as $stat) {
            $result[$stat->status] = $stat->count;
            $result['total'] += $stat->count;
        }

        return $result;
    }

    /**
     * Check if attendance has been taken for a department on a specific date
     *
     * @param int $departmentId
     * @param string $date
     * @return bool
     */
    public function isAttendanceTaken(int $departmentId, string $date): bool
    {
        $count = $this->find()
            ->where([
                'Attendances.department_id' => $departmentId,
                'Attendances.attendance_date' => $date,
            ])
            ->count();

        return $count > 0;
    }
}
