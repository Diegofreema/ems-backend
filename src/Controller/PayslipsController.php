<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Payslips Controller
 *
 * @property \App\Model\Table\PayslipsTable $Payslips
 * @method \App\Model\Entity\Payslip[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PayslipsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Employees'],
        ];
        $payslips = $this->paginate($this->Payslips);
           // debug(json_encode($payslips, JSON_PRETTY_PRINT)); exit;
        $this->set(compact('payslips'));
         $this->viewBuilder()->setLayout('backend'); 
    }

    /**
     * View method
     *
     * @param string|null $id Payslip id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $payslip = $this->Payslips->get($id, [
            'contain' => ['Teachers'],
        ]);

        $this->set(compact('payslip'));
    }

    
      public function viewpayslip($id = null)
    {
        $payslip = $this->Payslips->get($id, [
            'contain' => ['Employees.Staffdepartments','Employees.Users','Employees.Users']
        ]);

        $this->set('payslip', $payslip);
        $this->viewBuilder()->setLayout('backend'); 
    }
    
    
    
    //method that returns an employee payslips
    public function employeepayslips($employee_id){
        
        $payslips = $this->Payslips->find()->where(['employee_id'=>$employee_id])
                ->contain(['Employees.Staffdepartments','Employees.States','Employees.Lgas','Employees.Users','Employees.Staffgrades']);

         $this->set('payslips', $payslips);
        $this->viewBuilder()->setLayout('backend'); 
    }






    //admin method for generating staff pay slips
    public function generatepayslips(){
          $employeesTable = TableRegistry::get('Employees');
          
       $employees = $employeesTable->find()->contain(['Grades','Departments','Jobtypes'])->order(['fname'=>'ASC']);
       
         if ($this->request->is('post')) {
               $count = 0; $duplicate = 0;
             //  debug(json_encode($this->request->getData(), JSON_PRETTY_PRINT)); exit;
               $gradeid = $this->request->getData('gradeid');
             //check selected staff and generate pay slip
                if (!empty($this->request->getData('employeeids'))) {
                  foreach ($this->request->getData('employeeids') as $employee_id) {
                      if (is_numeric($employee_id)) {
                          //ensure no duplicate entries for same month
          $haspayslip = $this->Payslips->find()->where(['employee_id'=>$employee_id,'formonth'=>DATE('M, Y')])
                  ->first();
          if(empty($haspayslip)){
               //call the method that generates the pay slip
                    $this->createpayslip($employee_id, $gradeid);

                          $count++;
          }
          else{
              
             $duplicate ++; 
          }
                //log activity
                  $usercontroller = new UsersController();

                  $title = "Generated Payslips " .  $count;
                  $user_id = $this->Auth->user('id');
                  $description = "Pay Slip Generation ";
                  $ip = $this->request->clientIp();
                  $type = "Add";
                  $usercontroller->makeLog($title, $user_id, $description, $ip, $type);          
                            
                      }
                     // $this->Flash->error(__(' Wrong data format. Please try again'));
                  }
                  $this->Flash->success(__($count . ' Pay slips has been generated. Duplicates not generated ( '.$duplicate.')'));
                  return $this->redirect(['action' => 'index']);
              } else {
                  $this->Flash->error(__(' Unable to generate pay slip. Please try again'));
                  return $this->redirect(['action' => 'createpayslips']);
              }
               
           }
        
         $this->set(compact('employees'));
         $this->viewBuilder()->setLayout('backend'); 
    }
    
    
    
    
    //the method that actually create the pay slip
    private function createpayslip($employeeid, $gradeid){
        $duplicate = 0;
          $lonesTable = TableRegistry::get('loans');
           $employeesTable = TableRegistry::get('Employees');
          $earningsTable = TableRegistry::get('Earnings');
         $employee = $employeesTable->get($employeeid,['contain'=>['Grades']]);
          $earnings =  $earningsTable->find()->where(['grade_id'=>$employee->grade->id])->first();
          //check if this officer has an unfinished loan
          $monthlypayback = 0;
          $loan = $lonesTable->find()->where(['employee_id'=>$employee->id,'balance >'=>10,'status'=>'Approved'])->first();
          if(!empty($loan) && ($loan->mpayback>0)){
           $monthlypayback =    $loan->mpayback;
           $loan->balance = $loan->balance-$loan->mpayback;
           $lonesTable->save($loan);
          }
         // debug(json_encode(  $earnings, JSON_PRETTY_PRINT)); exit;
         $deduction = (($earnings->deductions*$earnings->basicpay)/100);
         $houseallowance = (($earnings->houseallowance*$earnings->basicpay)/100);
        $transportallowance = (($earnings->transallowance*$earnings->basicpay)/100);
       $tax = (($earnings->tax*$earnings->basicpay)/100);
      $medicalallowance = (($earnings->medallowance*$earnings->basicpay)/100);
       
         // $grade =   $gradesTable->get($gradeid);
             $grosspay = $earnings->basicsalary+$houseallowance+$transportallowance+$medicalallowance;
            $payslip = $this->Payslips->newEntity();
                   $payslip->employee_id = $employeeid;
                   $payslip->formonth = date('M, Y');
                   $payslip->deduction =  $deduction+$tax+$monthlypayback;
                   $payslip->grosspay =    $grosspay;
                   $payslip->netpay = ( $grosspay - ($deduction+$tax+$monthlypayback));
                  // debug(json_encode(  $payslip, JSON_PRETTY_PRINT)); exit;
                  $this->Payslips->save($payslip);
                  return;
         
    }

    
    //the method that show an officer all deductions from his monthly pay
    public function deductions(){
        $employeesTable = TableRegistry::get('Employees');
         $employee =  $employeesTable->find()->where(['user_id'=>$this->Auth->user('id')])->first();
      $monthlydeductions =   $this->Payslips->find()->where(['employee_id'=>  $employee->id]);
        $this->set(compact('monthlydeductions'));
            $this->viewBuilder()->setLayout('backend'); 
    }

    
 public function editpayslip($id = null)
    {
        $payslip = $this->Payslips->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $payslip = $this->Payslips->patchEntity($payslip, $this->request->getData());
            if ($this->Payslips->save($payslip)) {
               // $this->Flash->alart('The pay slip has been updated', ['params' => ['type' => 'success']]);
                 //log activity
                  $usercontroller = new UsersController();

                  $title = "Updated a Payslips " . $payslip->id;
                  $user_id = $this->Auth->user('id');
                  $description = "Pay Slip Update ";
                  $ip = $this->request->clientIp();
                  $type = "Edit";
                  $usercontroller->makeLog($title, $user_id, $description, $ip, $type);   
                $this->Flash->success(__('The payslip has been updated.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The payslip could not be saved. Please, try again.'));
        }
        $employees = $this->Payslips->Employees->find('list', ['limit' => 200]);
        $this->set(compact('payslip', 'employees'));
        $this->viewBuilder()->setLayout('backend'); 
    }
    
    
    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $payslip = $this->Payslips->newEmptyEntity();
        if ($this->request->is('post')) {
            $payslip = $this->Payslips->patchEntity($payslip, $this->request->getData());
            if ($this->Payslips->save($payslip)) {
                $this->Flash->success(__('The payslip has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The payslip could not be saved. Please, try again.'));
        }
        $teachers = $this->Payslips->Teachers->find('list', ['limit' => 200]);
        $this->set(compact('payslip', 'teachers'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Payslip id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $payslip = $this->Payslips->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $payslip = $this->Payslips->patchEntity($payslip, $this->request->getData());
            if ($this->Payslips->save($payslip)) {
                $this->Flash->success(__('The payslip has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The payslip could not be saved. Please, try again.'));
        }
        $teachers = $this->Payslips->Teachers->find('list', ['limit' => 200]);
        $this->set(compact('payslip', 'teachers'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Payslip id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $payslip = $this->Payslips->get($id);
        if ($this->Payslips->delete($payslip)) {
            //log activity
                  $usercontroller = new UsersController();

                  $title = "Deleted a Payslip " . $payslip->id;
                  $user_id = $this->Auth->user('id');
                  $description = "Pay Slip Update ";
                  $ip = $this->request->clientIp();
                  $type = "Delete";
                  $usercontroller->makeLog($title, $user_id, $description, $ip, $type); 
            $this->Flash->success(__('The payslip has been deleted.'));
        } else {
            $this->Flash->error(__('The payslip could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
