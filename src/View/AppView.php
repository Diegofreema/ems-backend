<?php
declare(strict_types=1);

/**
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link      https://cakephp.org CakePHP(tm) Project
 * @since     3.0.0
 * @license   https://opensource.org/licenses/mit-license.php MIT License
 */
namespace App\View;
use Cake\ORM\TableRegistry;
use Cake\View\View;

/**
 * Application View
 *
 * Your application's default view class
 *
 * @link https://book.cakephp.org/4/en/views.html#the-app-view
 */
class AppView extends View
{
    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading helpers.
     *
     * e.g. `$this->loadHelper('Html');`
     *
     * @return void
     */
    public function initialize(): void
    {
    }
    
       
    //method that transforms the url into something prety
       public  function GenerateUrl($s) {
  //Convert accented characters, and remove parentheses and apostrophes
  $from = explode (',', "ç,æ,œ,á,é,í,ó,ú,à,è,ì,ò,ù,ä,ë,ï,ö,ü,ÿ,â,ê,î,ô,û,å,e,i,ø,u,(,),[,],'");
  $to = explode (',', 'c,ae,oe,a,e,i,o,u,a,e,i,o,u,a,e,i,o,u,y,a,e,i,o,u,a,e,i,o,u,,,,,,');
  //Do the replacements, and convert all other non-alphanumeric characters to spaces
  $s = preg_replace ('~[^a-zA-Z0-9]+~', '-', str_replace ($from, $to, trim ($s)));
  //Remove a - at the beginning or end and make lowercase
  return strtolower (preg_replace ('/^-/', '', preg_replace ('/-$/', '', $s)));
}

//method that gets the department name for business intelligense
public function getdeptname($id){
     $departments_table = TableRegistry::get('Departments');
     $department = $departments_table->get($id);
     return $department->name;
}

//method for getting state name in business intelligense
public function getstate($id){
    $states_table = TableRegistry::get('States');
     $state = $states_table->get($id);
     return $state->name;
}


//method for getting lga name in business intelligense
public function getlga($id){
    $lgas_table = TableRegistry::get('Lgas');
     $lga = $lgas_table->get($id);
     return $lga->name;
}

  //calculate CGPA
    public function calculateCGPA($regnumb) {
        if(!empty($regnumb)){
        $results_table = TableRegistry::get('Results');
        $courses_table = TableRegistry::get('Subjects');
        $constants_table = TableRegistry::get('Constants');
        $total = 0;
        $totalUnits = 0;
        $results = $results_table->find()->where(['regno' => $regnumb]);
        $l = 0;

         // debug(json_encode( $regnumb, JSON_PRETTY_PRINT)); exit;
        foreach ($results as $result) {
            //remove any F in the result
             if($result->grade != "F"){
            //$credit_unit = $result->creditload;
            $grade_point_quality = $constants_table->find()->where(['name' => $result->grade])->first();
            $course_point = $grade_point_quality->value * $result->creditload;
            $total += $course_point;
            $totalUnits += $result->creditload;
            $l++;
             }
        }
        if( $totalUnits>0){
        return number_format($total / $totalUnits, 2);
        }else{return "no records found";}
        }else{return "Reg number not found";}
    }
      
     //function that return the grade point value
    public function getgradepoint($course_id,$grade){
        $courses_table = TableRegistry::get('Subjects');
        $constants_table = TableRegistry::get('Constants');
        $credit_unit = $courses_table->get($course_id);
            $grade_point_quality = $constants_table->find()->where(['name' => $grade])->first();
            $course_point = $grade_point_quality->value * $credit_unit->creditload;
            return $course_point;
        
    }
    
    
    //method that calculates the GPA for a semester
    public function calculategpa($grade,$creditload) {
              $constants_table = TableRegistry::get('Constants');  
             $grade_point_quality = $constants_table->find()->where(['name' => $grade])->first();
            return $grade_point_quality->value * $creditload;
    }


    //method that return a payment ref of an invoice for retrial in the case of webpay failure
    public function getrefno($student_id,$invoice_id){
      $transactions_table = TableRegistry::get('Transactions');  
      $transaction = $transactions_table->find()->where(['student_id'=>$student_id, 'invoice_id'=>$invoice_id])
              ->first();
      if(!empty($transaction->payref)){
      return $transaction->payref;
      
      }
      else{ return 0;}
        
    }

    
    //method that gets a student result for display on the composite result page
    public function getstudentresult($sid,$levelid,$semesterid,$subject_id,$sessionid){
     $results_table = TableRegistry::get('Results'); 
     //retriev chosen level from session
     $chosen_levelid =  $this->request->getSession()->read('dlevelid');
     $sresult = $results_table->find()->contain(['Subjects'])
             ->where(['student_id'=>$sid,'Results.level_id'=> $levelid,'Results.semester_id'=>$semesterid,
         'Results.subject_id'=>$subject_id,'Results.session_id'=>$sessionid])
            ->first();
    // debug(json_encode(  $sresult, JSON_PRETTY_PRINT)); exit;
     return  $sresult;
     
        
        
    }
    
    //get carry over for the carry over section
       public function getstudentcarryoverresult($sid,$levelid,$semesterid,$subject_id,$session_id){
     $results_table = TableRegistry::get('Results'); 
     $sresult = $results_table->find()->where(['student_id'=>$sid,'level_id'=> $levelid,'semester_id'=>$semesterid,'subject_id'=>$subject_id,'session_id'=>$session_id])
            ->first();
    // debug(json_encode(  $sresult, JSON_PRETTY_PRINT)); exit;
     return  $sresult;
     
        
        
    }
    
    //method that gets the students past results when not in year 1 and first semester
     public function getstudentpresult($sid,$levelid,$semesterid){
         //retriev chosen level from session
     $chosen_levelid =  $this->request->getSession()->read('dlevelid');
         $lev = 0; $semid = 0;
         if($semesterid == 2){
         $semid = 1; $lev = $chosen_levelid; 
         }else{ $lev = $chosen_levelid-1; $semid = 2; }
     $results_table = TableRegistry::get('Results');  
     $sresult = $results_table->find()->contain(['Subjects'])->where(['student_id'=>$sid,
         'Results.level_id'=>$lev,'Results.semester_id'=>$semid])
             ->order(['Results.subject_id'=>'DESC'])->all();
    // debug(json_encode(  $sresult, JSON_PRETTY_PRINT)); exit;
     return  $sresult;
     
        
        
    }
    
    
    
    //method that gets all students results for cumulative
    public function getcumulative($studentid){
        $results_table = TableRegistry::get('Results');  
     $cum_results = $results_table->find()->contain(['Subjects'])->where(['student_id'=>$studentid])
             ->order(['Results.subject_id'=>'DESC'])->all();
    // debug(json_encode($cum_results, JSON_PRETTY_PRINT)); exit;
     return $cum_results;
        
    }
    


    //converts a property post date to hours
 function timeago($jobpsotdate)
{
     date_default_timezone_set("Africa/Lagos");
	
    $estimate_time = time() - $jobpsotdate ; 

    if( $estimate_time < 1 )
    {
        return '1 sec ago';
    }

    $condition = array(
                12 * 30 * 24 * 60 * 60  =>  'year',
                30 * 24 * 60 * 60       =>  'month',
                24 * 60 * 60            =>  'day',
                60 * 60                 =>  'hr',
                60                      =>  'min',
                1                       =>  'sec'
    );

    foreach( $condition as $secs => $str )
    {
        $d = $estimate_time / $secs;
		
	

        if( $d >= 1 )
        {
            $r = round($d);
            return  $r . ' ' . $str . ( $r > 1 ? 's' : '' ) . ' ago';
        }
    }
}
  
//get the meesgae count for the loggedin user
      public function countmessage($id = null){
            $studentmessage_table = TableRegistry::get('Studentmessages');
          $studentmessages = $studentmessage_table->find()
                 ->where(['Studentmessages.status'=>'Unseen','mfor'=>'Student','Studentmessages.user_id'=>$id])
                 ->count();
          return $studentmessages;
          
      }
      
      
      //method for geting student result during teacher update of result
      public function checkandgetresult($deptid,$subjectid,$studentid){
        $results_table = TableRegistry::get('Results'); 
        $result = $results_table->find()->where(['student_id'=>$studentid,'subject_id'=>$subjectid,
            'department_id'=>$deptid])->first();
        return $result;
          
          
      }
      
      //count unread message for admin
       public function countadminmessage(){
            $studentmessage_table = TableRegistry::get('Studentmessages');
          $studentmessages = $studentmessage_table->find()
                 ->where(['Studentmessages.status'=>'Unseen','mfor'=>'Admin'])
                 ->count();
          return $studentmessages;
          
      }
      
      //method to get the number of submitted assignments in a course
      public function getsubmittedass($subid,$setassgntid){
          $assignments = TableRegistry::get('Assignments');
          $settings = $this->request->getSession()->read('settings');
         $submitted = $assignments->find()->where(['subject_id'=>$subid,
             'session_id'=>$settings->session_id,'setassignment_id'=>$setassgntid])->count();
         return $submitted;
          
      }
      
}
