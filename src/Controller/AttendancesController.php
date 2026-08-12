<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;
use Cake\Event\EventInterface;

/**
 * Attendances Controller
 *
 * @property \App\Model\Table\AttendancesTable $Attendances
 *
 * @method \App\Model\Entity\Attendance[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class AttendancesController extends AppController
{
    /**
     * Before filter callback
     *
     * @param \Cake\Event\EventInterface $event The beforeFilter event.
     * @return \Cake\Http\Response|void|null
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        
        // Allow teachers to access attendance functionality
        $this->Auth->allow(['index', 'take', 'save', 'view', 'report']);
    }

    /**
     * Index method - Show attendance dashboard for teachers
     *
     * @return \Cake\Http\Response|void|null
     */
    public function index()
    {
        // Get current teacher
        $teacher = $this->getCurrentTeacher();
        if (!$teacher) {
            $this->Flash->error(__('Teacher not found. Please contact administrator.'));
            return $this->redirect(['controller' => 'Teachers', 'action' => 'viewprofile']);
        }

        // Get teacher's assigned class arms
        $classArmsTable = TableRegistry::getTableLocator()->get('ClassArms');
        $teacherClassArms = $classArmsTable->find()
            ->where(['class_teacher_id' => $teacher->id, 'ClassArms.status' => 'active'])
            ->contain(['Departments'])
            ->all();
        
        // Get students in teacher's assigned class arms
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $students = [];
        if (!empty($teacherClassArms) && $teacherClassArms->count() > 0) {
            $classArmIds = [];
            foreach ($teacherClassArms as $classArm) {
                $classArmIds[] = $classArm->id;
            }
            if (!empty($classArmIds)) {
                $students = $studentsTable->find()
                    ->select(['id', 'fname', 'lname', 'regno', 'class_arm_id'])
                    ->contain(['ClassArms'])
                    ->where(['class_arm_id IN' => $classArmIds, 'Students.status' => 'Admitted'])
                    ->order(['fname' => 'ASC', 'lname' => 'ASC'])
                    ->all();
            }
            // If no valid class arm IDs, students array remains empty
        }
        // If no class arms assigned, students array remains empty (no fallback to department)

        // Get today's attendance
        $today = date('Y-m-d');
        $todayAttendance = [];
        $attendanceTaken = false;
        $monthlyStats = ['total' => 0, 'present' => 0, 'absent' => 0, 'late' => 0, 'excused' => 0];
        
        if (!empty($teacherClassArms) && $teacherClassArms->count() > 0) {
            // For class arms, we need to get attendance for each class arm
            $classArmIds = [];
            foreach ($teacherClassArms as $classArm) {
                $classArmIds[] = $classArm->id;
            }
            if (!empty($classArmIds)) {
                // Try to get attendance by class_arm_id first
                $todayAttendance = $this->Attendances->find()
                    ->where(['class_arm_id IN' => $classArmIds, 'attendance_date' => $today])
                    ->toArray();
                
                // If no results found, try by department_id as fallback
                if (empty($todayAttendance)) {
                    $departmentIds = [];
                    foreach ($teacherClassArms as $classArm) {
                        $departmentIds[] = $classArm->department_id;
                    }
                    if (!empty($departmentIds)) {
                        $todayAttendance = $this->Attendances->find()
                            ->where(['department_id IN' => $departmentIds, 'attendance_date' => $today])
                            ->toArray();
                    }
                }
                
                $attendanceTaken = count($todayAttendance) > 0;
                
                // Calculate monthly statistics
                $startOfMonth = date('Y-m-01');
                $endOfMonth = date('Y-m-t');
                $monthlyAttendance = $this->Attendances->find()
                    ->where([
                        'class_arm_id IN' => $classArmIds,
                        'attendance_date >=' => $startOfMonth,
                        'attendance_date <=' => $endOfMonth
                    ])
                    ->toArray();
                
                // If no results found, try by department_id as fallback
                if (empty($monthlyAttendance)) {
                    $departmentIds = [];
                    foreach ($teacherClassArms as $classArm) {
                        $departmentIds[] = $classArm->department_id;
                    }
                    if (!empty($departmentIds)) {
                        $monthlyAttendance = $this->Attendances->find()
                            ->where([
                                'department_id IN' => $departmentIds,
                                'attendance_date >=' => $startOfMonth,
                                'attendance_date <=' => $endOfMonth
                            ])
                            ->toArray();
                    }
                }
                
                foreach ($monthlyAttendance as $attendance) {
                    $monthlyStats['total']++;
                    if (isset($monthlyStats[$attendance->status])) {
                        $monthlyStats[$attendance->status]++;
                    }
                }
            }
            // If no valid class arm IDs, attendance arrays remain empty
        }
        // If no class arms assigned, attendance arrays remain empty (no fallback to department)

        $this->set(compact('teacher', 'students', 'todayAttendance', 'attendanceTaken', 'monthlyStats', 'today', 'teacherClassArms'));
        $this->viewBuilder()->setLayout('teachersbackend');
    }

    /**
     * Take attendance method
     *
     * @return \Cake\Http\Response|void|null
     */
    public function take()
    {
        // Get current teacher
        $teacher = $this->getCurrentTeacher();
        if (!$teacher) {
            $this->Flash->error(__('Teacher not found. Please contact administrator.'));
            return $this->redirect(['action' => 'index']);
        }

        // Get teacher's assigned class arms
        $classArmsTable = TableRegistry::getTableLocator()->get('ClassArms');
        $teacherClassArms = $classArmsTable->find()
            ->where(['class_teacher_id' => $teacher->id, 'ClassArms.status' => 'active'])
            ->contain(['Departments'])
            ->all();

        $attendanceDate = $this->request->getQuery('date', date('Y-m-d'));

        // Get students in teacher's assigned class arms
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $students = [];
        if (!empty($teacherClassArms) && $teacherClassArms->count() > 0) {
            $classArmIds = [];
            foreach ($teacherClassArms as $classArm) {
                $classArmIds[] = $classArm->id;
            }
            if (!empty($classArmIds)) {
                $students = $studentsTable->find()
                    ->select(['id', 'fname', 'lname', 'regno', 'class_arm_id', 'department_id'])
                    ->contain(['ClassArms', 'Departments'])
                    ->where(['class_arm_id IN' => $classArmIds, 'Students.status' => 'Admitted'])
                    ->order(['fname' => 'ASC', 'lname' => 'ASC'])
                    ->all();
            }
            // If no valid class arm IDs, students array remains empty
        }
        // If no class arms assigned, students array remains empty (no fallback to department)

        // Get existing attendance for the date
        $existingAttendance = [];
        if (!empty($teacherClassArms) && $teacherClassArms->count() > 0) {
            $classArmIds = [];
            foreach ($teacherClassArms as $classArm) {
                $classArmIds[] = $classArm->id;
            }
            if (!empty($classArmIds)) {
                $existingAttendance = $this->Attendances->find()
                    ->where(['class_arm_id IN' => $classArmIds, 'attendance_date' => $attendanceDate])
                    ->toArray();
            }
        }
        
        // Convert to associative array for easy lookup
        $attendanceData = [];
        foreach ($existingAttendance as $attendance) {
            $attendanceData[$attendance->student_id] = $attendance;
        }

        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $attendanceDate = $data['attendance_date'];
            
            // Delete existing attendance for this date and class arms
            if (!empty($teacherClassArms) && $teacherClassArms->count() > 0) {
                $classArmIds = [];
                foreach ($teacherClassArms as $classArm) {
                    $classArmIds[] = $classArm->id;
                }
                if (!empty($classArmIds)) {
                    $this->Attendances->deleteAll([
                        'class_arm_id IN' => $classArmIds,
                        'attendance_date' => $attendanceDate
                    ]);
                }
            }

            // Save new attendance records
            $attendanceEntities = [];
            foreach ($students as $student) {
                $status = $data['attendance'][$student->id] ?? 'absent';
                $notes = $data['notes'][$student->id] ?? '';

                $attendanceEntity = $this->Attendances->newEntity([
                    'student_id' => $student->id,
                    'teacher_id' => $teacher->id,
                    'department_id' => $student->department_id,
                    'class_arm_id' => $student->class_arm_id,
                    'attendance_date' => $attendanceDate,
                    'status' => $status,
                    'notes' => $notes
                ]);

                $attendanceEntities[] = $attendanceEntity;
            }

            if ($this->Attendances->saveMany($attendanceEntities)) {
                $this->Flash->success(__('Attendance has been saved successfully.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('There was an error saving attendance. Please try again.'));
            }
        }

        $this->set(compact('teacher', 'students', 'attendanceData', 'attendanceDate', 'teacherClassArms'));
        $this->viewBuilder()->setLayout('teachersbackend');
    }

    /**
     * View attendance method
     *
     * @return \Cake\Http\Response|void|null
     */
    public function view()
    {
        // Get current teacher
        $teacher = $this->getCurrentTeacher();
        if (!$teacher) {
            $this->Flash->error(__('Teacher not found. Please contact administrator.'));
            return $this->redirect(['action' => 'index']);
        }

        // Get teacher's assigned class arms
        $classArmsTable = TableRegistry::getTableLocator()->get('ClassArms');
        $teacherClassArms = $classArmsTable->find()
            ->where(['class_teacher_id' => $teacher->id, 'ClassArms.status' => 'active'])
            ->contain(['Departments'])
            ->all();

        $date = $this->request->getQuery('date', date('Y-m-d'));

        // Get attendance for the specified date
        $attendance = [];
        if (!empty($teacherClassArms) && $teacherClassArms->count() > 0) {
            $classArmIds = [];
            foreach ($teacherClassArms as $classArm) {
                $classArmIds[] = $classArm->id;
            }
            if (!empty($classArmIds)) {
                $attendance = $this->Attendances->find()
                    ->where(['class_arm_id IN' => $classArmIds, 'attendance_date' => $date])
                    ->all();
            }
        }

        // Get students in teacher's assigned class arms
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $students = [];
        if (!empty($teacherClassArms) && $teacherClassArms->count() > 0) {
            $classArmIds = [];
            foreach ($teacherClassArms as $classArm) {
                $classArmIds[] = $classArm->id;
            }
            if (!empty($classArmIds)) {
                $students = $studentsTable->find()
                    ->select(['id', 'fname', 'lname', 'regno', 'class_arm_id'])
                    ->contain(['ClassArms'])
                    ->where(['class_arm_id IN' => $classArmIds, 'Students.status' => 'Admitted'])
                    ->order(['fname' => 'ASC', 'lname' => 'ASC'])
                    ->all();
            }
        }

        $this->set(compact('teacher', 'attendance', 'students', 'date', 'teacherClassArms'));
        $this->viewBuilder()->setLayout('teachersbackend');
    }

    /**
     * Attendance report method
     *
     * @return \Cake\Http\Response|void|null
     */
    public function report()
    {
        // Get current teacher
        $teacher = $this->getCurrentTeacher();
        if (!$teacher) {
            $this->Flash->error(__('Teacher not found. Please contact administrator.'));
            return $this->redirect(['action' => 'index']);
        }

        // Get teacher's assigned class arms
        $classArmsTable = TableRegistry::getTableLocator()->get('ClassArms');
        $teacherClassArms = $classArmsTable->find()
            ->where(['class_teacher_id' => $teacher->id, 'ClassArms.status' => 'active'])
            ->contain(['Departments'])
            ->all();
        
        // Get date range from query parameters
        $startDate = $this->request->getQuery('start_date', date('Y-m-01'));
        $endDate = $this->request->getQuery('end_date', date('Y-m-d'));

        // Get attendance statistics
        $stats = ['total' => 0, 'present' => 0, 'absent' => 0, 'late' => 0, 'excused' => 0];
        if (!empty($teacherClassArms) && $teacherClassArms->count() > 0) {
            $classArmIds = [];
            foreach ($teacherClassArms as $classArm) {
                $classArmIds[] = $classArm->id;
            }
            if (!empty($classArmIds)) {
                $attendanceRecords = $this->Attendances->find()
                    ->where([
                        'class_arm_id IN' => $classArmIds,
                        'attendance_date >=' => $startDate,
                        'attendance_date <=' => $endDate
                    ])
                    ->all();
                
                foreach ($attendanceRecords as $record) {
                    $stats['total']++;
                    $stats[$record->status]++;
                }
            }
        }

        // Get students in teacher's assigned class arms
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $students = [];
        if (!empty($teacherClassArms) && $teacherClassArms->count() > 0) {
            $classArmIds = [];
            foreach ($teacherClassArms as $classArm) {
                $classArmIds[] = $classArm->id;
            }
            if (!empty($classArmIds)) {
                $students = $studentsTable->find()
                    ->select(['id', 'fname', 'lname', 'regno', 'class_arm_id'])
                    ->contain(['ClassArms'])
                    ->where(['class_arm_id IN' => $classArmIds, 'Students.status' => 'Admitted'])
                    ->order(['fname' => 'ASC', 'lname' => 'ASC'])
                    ->all();
            }
        }

        // Get individual student attendance records
        $studentAttendance = [];
        foreach ($students as $student) {
            $studentAttendance[$student->id] = $this->Attendances->find()
                ->where([
                    'Attendances.student_id' => $student->id,
                    'Attendances.attendance_date >=' => $startDate,
                    'Attendances.attendance_date <=' => $endDate
                ])
                ->order(['Attendances.attendance_date' => 'DESC'])
                ->all();
        }

        $this->set(compact('teacher', 'stats', 'students', 'studentAttendance', 'startDate', 'endDate', 'teacherClassArms'));
        $this->viewBuilder()->setLayout('teachersbackend');
    }

    /**
     * Print attendance report
     *
     * @return \Cake\Http\Response|void|null
     */
    public function print()
    {
        // Get current teacher
        $teacher = $this->getCurrentTeacher();
        if (!$teacher) {
            $this->Flash->error(__('Teacher not found. Please contact administrator.'));
            return $this->redirect(['action' => 'index']);
        }

        $departmentId = $teacher->department_id;
        $date = $this->request->getQuery('date', date('Y-m-d'));

        // Get attendance for the specified date
        $attendance = $this->Attendances->getAttendanceForDate($departmentId, $date)->all();

        // Get students in department
        $students = $this->Attendances->getStudentsForAttendance($departmentId)->all();

        $this->set(compact('teacher', 'attendance', 'students', 'date'));
        $this->viewBuilder()->setLayout('ajax'); // Use minimal layout for printing
    }

    /**
     * Export attendance report to CSV
     *
     * @return \Cake\Http\Response|void|null
     */
    public function export()
    {
        // Get current teacher
        $teacher = $this->getCurrentTeacher();
        if (!$teacher) {
            $this->Flash->error(__('Teacher not found. Please contact administrator.'));
            return $this->redirect(['action' => 'index']);
        }

        $departmentId = $teacher->department_id;
        $startDate = $this->request->getQuery('start_date', date('Y-m-01'));
        $endDate = $this->request->getQuery('end_date', date('Y-m-d'));

        // Get students in department
        $students = $this->Attendances->getStudentsForAttendance($departmentId)->all();

        // Get individual student attendance records
        $studentAttendance = [];
        foreach ($students as $student) {
            $studentAttendance[$student->id] = $this->Attendances->find()
                ->where([
                    'Attendances.student_id' => $student->id,
                    'Attendances.attendance_date >=' => $startDate,
                    'Attendances.attendance_date <=' => $endDate
                ])
                ->order(['Attendances.attendance_date' => 'DESC'])
                ->all();
        }

        // Set response headers for CSV download
        $this->response = $this->response->withType('csv');
        $this->response = $this->response->withDownload('attendance_report_' . $startDate . '_to_' . $endDate . '.csv');

        $this->set(compact('teacher', 'students', 'studentAttendance', 'startDate', 'endDate'));
        $this->viewBuilder()->setLayout('ajax'); // Use minimal layout for CSV
    }

    /**
     * Get current teacher from session
     *
     * @return \App\Model\Entity\Teacher|null
     */
    private function getCurrentTeacher()
    {
        $userId = $this->Auth->user('id');
        if (!$userId) {
            return null;
        }

        $teachersTable = TableRegistry::getTableLocator()->get('Teachers');
        return $teachersTable->find()
            ->where(['user_id' => $userId])
            ->contain(['Departments'])
            ->first();
    }
}
