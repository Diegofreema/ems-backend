<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * AdminAttendances Controller
 *
 * @property \App\Model\Table\AttendancesTable $Attendances
 */
class AdminAttendancesController extends AppController
{

    /**
     * Check whether the current request should be answered as JSON.
     *
     * @return bool
     */
    private function isApiRequest(): bool
    {
        $extension = $this->request->getParam('_ext');
        if (in_array($extension, ['json', 'xml'], true)) {
            return true;
        }

        $accept = $this->request->getHeaderLine('Accept');
        return strpos($accept, 'application/json') !== false || strpos($accept, 'application/xml') !== false;
    }

    /**
     * Build a JSON response payload.
     *
     * @param array $payload Payload data.
     * @param int $statusCode HTTP status code.
     * @return \Cake\Http\Response
     */
    private function respondJson(array $payload, int $statusCode = 200)
    {
        return $this->response
            ->withType('application/json')
            ->withStatus($statusCode)
            ->withStringBody((string)json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    /**
     * Index method - Admin attendance dashboard
     */
    public function index()
    {
        try {
            $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
            $attendancesTable = TableRegistry::getTableLocator()->get('Attendances');
            $studentsTable = TableRegistry::getTableLocator()->get('Students');
        } catch (\Exception $e) {
            if ($this->isApiRequest()) {
                return $this->respondJson([
                    'success' => false,
                    'message' => 'Error loading attendance data: ' . $e->getMessage(),
                ], 500);
            }

            $this->Flash->error('Error loading attendance data: ' . $e->getMessage());
            return $this->redirect(['controller' => 'Users', 'action' => 'dashboard']);
        }

        // Get all departments for filter dropdown
        $departments = $departmentsTable->find()
            ->order(['name' => 'ASC'])
            ->all();

        // Get today's date
        $today = date('Y-m-d');
        
        // Get attendance statistics for today by department and class arms
        $todayStats = [];
        foreach ($departments as $department) {
            // Get class arms for this department
            $classArmsTable = TableRegistry::getTableLocator()->get('ClassArms');
            $classArms = $classArmsTable->find()
                ->where(['department_id' => $department->id, 'status' => 'active'])
                ->all();
            
            if (!empty($classArms)) {
                // If department has class arms, show statistics for each arm
                foreach ($classArms as $classArm) {
                    $totalStudents = $studentsTable->find()
                        ->where(['class_arm_id' => $classArm->id, 'status' => 'Admitted'])
                        ->count();
                    
                    $presentToday = $attendancesTable->find()
                        ->contain(['Students'])
                        ->where([
                            'Attendances.attendance_date' => $today,
                            'Students.class_arm_id' => $classArm->id,
                            'Attendances.status' => 'present'
                        ])
                        ->count();
                    
                    $todayStats[] = (object)[
                        'department_name' => $department->name . ' - ' . $classArm->arm_name,
                        'total_students' => $totalStudents,
                        'present_count' => $presentToday
                    ];
                }
            } else {
                // If department has no class arms, show department-level statistics
                $totalStudents = $studentsTable->find()
                    ->where(['department_id' => $department->id, 'status' => 'Admitted'])
                    ->count();
                
                $presentToday = $attendancesTable->find()
                    ->contain(['Students'])
                    ->where([
                        'Attendances.attendance_date' => $today,
                        'Students.department_id' => $department->id,
                        'Attendances.status' => 'present'
                    ])
                    ->count();
                
                $todayStats[] = (object)[
                    'department_name' => $department->name,
                    'total_students' => $totalStudents,
                    'present_count' => $presentToday
                ];
            }
        }

        // Get overall statistics
        $overallStats = (object)[
            'total_records' => $attendancesTable->find()->count(),
            'present_count' => $attendancesTable->find()->where(['status' => 'present'])->count(),
            'absent_count' => $attendancesTable->find()->where(['status' => 'absent'])->count(),
            'late_count' => $attendancesTable->find()->where(['status' => 'late'])->count(),
            'excused_count' => $attendancesTable->find()->where(['status' => 'excused'])->count()
        ];

        if ($this->isApiRequest()) {
            return $this->respondJson([
                'success' => true,
                'data' => [
                    'departments' => $departments->toArray(),
                    'todayStats' => array_map(static function ($item) {
                        return (array)$item;
                    }, $todayStats),
                    'overallStats' => [
                        'total_records' => $overallStats->total_records,
                        'present_count' => $overallStats->present_count,
                        'absent_count' => $overallStats->absent_count,
                        'late_count' => $overallStats->late_count,
                        'excused_count' => $overallStats->excused_count,
                    ],
                    'today' => $today,
                ],
            ]);
        }

        $this->set(compact('departments', 'todayStats', 'overallStats', 'today'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Report method - Generate attendance reports with filters
     */
    public function report()
    {
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $attendancesTable = TableRegistry::getTableLocator()->get('Attendances');

        // Get all departments for filter dropdown
        $departments = $departmentsTable->find()
            ->order(['name' => 'ASC'])
            ->all();

        // Get filter parameters
        $departmentId = $this->request->getQuery('department_id');
        $classArmId = $this->request->getQuery('class_arm_id');
        $startDate = $this->request->getQuery('start_date', date('Y-m-01'));
        $endDate = $this->request->getQuery('end_date', date('Y-m-d'));
        $status = $this->request->getQuery('status');

        $attendanceRecords = [];
        $attendanceStats = [];

        if ($departmentId || $classArmId || $startDate || $endDate || $status) {
            // Build query conditions
            $conditions = [];
            
            if ($departmentId) {
                $conditions['Students.department_id'] = $departmentId;
            }
            
            if ($classArmId) {
                $conditions['Students.class_arm_id'] = $classArmId;
            }
            
            if ($startDate) {
                $conditions['Attendances.attendance_date >='] = $startDate;
            }
            
            if ($endDate) {
                $conditions['Attendances.attendance_date <='] = $endDate;
            }
            
            if ($status) {
                $conditions['Attendances.status'] = $status;
            }

            // Get attendance records
            $attendanceRecords = $attendancesTable->find()
                ->contain(['Students.Departments', 'Students.ClassArms', 'Teachers'])
                ->where($conditions)
                ->order(['Attendances.attendance_date' => 'DESC', 'Students.fname' => 'ASC'])
                ->all();

            // Calculate statistics
            $attendanceStats = (object)[
                'present' => $attendancesTable->find()
                    ->contain(['Students'])
                    ->where(array_merge($conditions, ['Attendances.status' => 'present']))
                    ->count(),
                'absent' => $attendancesTable->find()
                    ->contain(['Students'])
                    ->where(array_merge($conditions, ['Attendances.status' => 'absent']))
                    ->count(),
                'late' => $attendancesTable->find()
                    ->contain(['Students'])
                    ->where(array_merge($conditions, ['Attendances.status' => 'late']))
                    ->count(),
                'excused' => $attendancesTable->find()
                    ->contain(['Students'])
                    ->where(array_merge($conditions, ['Attendances.status' => 'excused']))
                    ->count()
            ];
        }

        if ($this->isApiRequest()) {
            return $this->respondJson([
                'success' => true,
                'data' => [
                    'departments' => $departments->toArray(),
                    'attendanceRecords' => $attendanceRecords instanceof \Cake\Datasource\ResultSetInterface ? $attendanceRecords->toArray() : $attendanceRecords,
                    'attendanceStats' => [
                        'present' => $attendanceStats->present ?? 0,
                        'absent' => $attendanceStats->absent ?? 0,
                        'late' => $attendanceStats->late ?? 0,
                        'excused' => $attendanceStats->excused ?? 0,
                    ],
                    'filters' => [
                        'department_id' => $departmentId,
                        'class_arm_id' => $classArmId,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'status' => $status,
                    ],
                ],
            ]);
        }

        $this->set(compact('departments', 'attendanceRecords', 'attendanceStats', 'departmentId', 'classArmId', 'startDate', 'endDate', 'status'));
        $this->viewBuilder()->setLayout('backend');
    }

    /**
     * Print method - Print-friendly attendance report
     */
    public function print()
    {
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $attendancesTable = TableRegistry::getTableLocator()->get('Attendances');

        // Get filter parameters
        $departmentId = $this->request->getQuery('department_id');
        $startDate = $this->request->getQuery('start_date', date('Y-m-01'));
        $endDate = $this->request->getQuery('end_date', date('Y-m-d'));
        $status = $this->request->getQuery('status');

        $attendanceRecords = [];
        $attendanceStats = [];
        $department = null;

        if ($departmentId) {
            $department = $departmentsTable->get($departmentId);
        }

        if ($departmentId || $startDate || $endDate || $status) {
            // Build query conditions
            $conditions = [];
            
            if ($departmentId) {
                $conditions['Students.department_id'] = $departmentId;
            }
            
            if ($startDate) {
                $conditions['Attendances.attendance_date >='] = $startDate;
            }
            
            if ($endDate) {
                $conditions['Attendances.attendance_date <='] = $endDate;
            }
            
            if ($status) {
                $conditions['Attendances.status'] = $status;
            }

            // Get attendance records
            $attendanceRecords = $attendancesTable->find()
                ->contain(['Students.Departments', 'Students.ClassArms', 'Teachers'])
                ->where($conditions)
                ->order(['Attendances.attendance_date' => 'DESC', 'Students.fname' => 'ASC'])
                ->all();

            // Calculate statistics
            $attendanceStats = (object)[
                'present' => $attendancesTable->find()
                    ->contain(['Students'])
                    ->where(array_merge($conditions, ['Attendances.status' => 'present']))
                    ->count(),
                'absent' => $attendancesTable->find()
                    ->contain(['Students'])
                    ->where(array_merge($conditions, ['Attendances.status' => 'absent']))
                    ->count(),
                'late' => $attendancesTable->find()
                    ->contain(['Students'])
                    ->where(array_merge($conditions, ['Attendances.status' => 'late']))
                    ->count(),
                'excused' => $attendancesTable->find()
                    ->contain(['Students'])
                    ->where(array_merge($conditions, ['Attendances.status' => 'excused']))
                    ->count()
            ];
        }

        if ($this->isApiRequest()) {
            return $this->respondJson([
                'success' => true,
                'data' => [
                    'attendanceRecords' => $attendanceRecords instanceof \Cake\Datasource\ResultSetInterface ? $attendanceRecords->toArray() : $attendanceRecords,
                    'attendanceStats' => [
                        'present' => $attendanceStats->present ?? 0,
                        'absent' => $attendanceStats->absent ?? 0,
                        'late' => $attendanceStats->late ?? 0,
                        'excused' => $attendanceStats->excused ?? 0,
                    ],
                    'department' => $department ? (array)$department : null,
                    'filters' => [
                        'department_id' => $departmentId,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'status' => $status,
                    ],
                ],
            ]);
        }

        $this->set(compact('attendanceRecords', 'attendanceStats', 'department', 'startDate', 'endDate', 'status'));
        $this->viewBuilder()->setLayout('ajax');
    }

    /**
     * Export method - Export attendance data to CSV
     */
    public function export()
    {
        $attendancesTable = TableRegistry::getTableLocator()->get('Attendances');

        // Get filter parameters
        $departmentId = $this->request->getQuery('department_id');
        $startDate = $this->request->getQuery('start_date', date('Y-m-01'));
        $endDate = $this->request->getQuery('end_date', date('Y-m-d'));
        $status = $this->request->getQuery('status');

        // Build query conditions
        $conditions = [];
        
        if ($departmentId) {
            $conditions['Students.department_id'] = $departmentId;
        }
        
        if ($startDate) {
            $conditions['Attendances.attendance_date >='] = $startDate;
        }
        
        if ($endDate) {
            $conditions['Attendances.attendance_date <='] = $endDate;
        }
        
        if ($status) {
            $conditions['Attendances.status'] = $status;
        }

        // Get attendance records
        $attendanceRecords = $attendancesTable->find()
            ->contain(['Students.Departments', 'Teachers'])
            ->where($conditions)
            ->order(['Attendances.attendance_date' => 'DESC', 'Students.fname' => 'ASC'])
            ->all();

        if ($this->isApiRequest()) {
            return $this->respondJson([
                'success' => true,
                'data' => [
                    'attendanceRecords' => $attendanceRecords instanceof \Cake\Datasource\ResultSetInterface ? $attendanceRecords->toArray() : $attendanceRecords,
                    'filters' => [
                        'department_id' => $departmentId,
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'status' => $status,
                    ],
                ],
            ]);
        }

        // Set headers for CSV download
        $this->response = $this->response->withType('csv');
        $this->response = $this->response->withDownload('attendance_report_' . date('Y-m-d') . '.csv');

        $this->set(compact('attendanceRecords', 'startDate', 'endDate'));
    }
}
