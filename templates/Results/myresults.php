<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
$settings = $this->request->getSession()->read('settings');
?>

<!-- Begin Page Content -->
<div class="content container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">My Results</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Students', 'action' => 'dashboard', $this->GenerateUrl('Student dashboard')], ['title' => 'Student dashboard'])
                        ?></li> 
                    <li class="breadcrumb-item active">My Results</li>
                </ul>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
    
    <div class="col-lg-12">
        <div class="p-5">
            <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Search Results</h1>
            </div>
            
            <?= $this->Form->create(null) ?>
            <fieldset>
                <div class="form-group row">
                    <div class="col-sm-6">
                        <?= $this->Form->control('semester_id', [
                            'options' => $semesters,
                            'label' => 'Select Term', 
                            'placeholder' => 'Select Term',
                            'class' => 'form-control',
                            'empty' => 'Select Term'
                        ]) ?>
                    </div>  
                    
                    <div class="col-sm-6">
                        <?= $this->Form->control('session_id', [
                            'options' => $sessions,
                            'label' => 'Select Session', 
                            'required', 
                            'placeholder' => 'Select Session',
                            'class' => 'form-control',
                            'empty' => 'Select Session'
                        ]) ?>
                    </div>
                </div>
            </fieldset>
            <br /> <br />
            <?= $this->Form->button('Search', ['class' => 'btn btn-primary btn-user btn-block']) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
    
    <?php if(!empty($results)){ ?>
        <div class="card shadow mb-4 PrintDis" id="printableArea" style="overflow-x: hidden; max-width: 100%;">
        <br /><br />
            <div class="row">
                <div class="col-sm-3 m-b-20">
                    <?= $this->Html->image($settings->logo, [
                        'alt' => 'LOGO', 
                        'class' => 'img-responsive float-left',
                        'height' => 100,
                        'style' => "margin-left: 15px;"
                    ]) ?>
                    <br /><br /><br />
                </div>
                
                <div class="col-sm-6 m-b-20 text-center">
                    <h1 class="h4 text-gray-900 mb-4">
                        <strong style="font-size: 30px;"><?= $settings->name ?></strong><br />
                        <b style="font-size: 23px;">
                            <?= $settings->address ?><br />
                            <?= $settings->email ?><br />
                        </b>
                        <b style="font-size: 21px;"> TERMINAL REPORT SHEET </b>
                    </h1>
                    <br />
                </div>
                
                <div class="col-sm-3 m-b-20">
                    <?= $this->Html->image('../student_files/' . $student->passporturl, [
                        'alt' => 'Passport', 
                        'class' => 'img-responsive float-right',
                        'height' => 100,
                        'style' => 'margin-right: 15px;'
                    ]) ?>
                </div>
            </div>
            
            <!-- Student Information - 2 Column Layout -->
            <div class="row" style="margin: 20px 0; font-size: 16px; font-family: sans-serif;">
                <div class="col-md-6">
                    <div class="card" style="border: 1px solid #ddd; margin-bottom: 10px;">
                        <div class="card-body" style="padding: 15px;">
                            <h6 style="font-weight: bold; margin-bottom: 15px; color: #333; border-bottom: 1px solid #eee; padding-bottom: 5px;">STUDENT INFORMATION</h6>
                            <div style="margin-bottom: 8px;"><strong>Student Name:</strong> <?= ucfirst($student->fname . ' ' . $student->lname . ' ' . $student->mname) ?></div>
                            <div style="margin-bottom: 8px;"><strong>Registration Number:</strong> <?= $student->regno ?></div>
                            <div style="margin-bottom: 8px;"><strong>Class:</strong> <?= $student->department->name ?><?= !empty($student->class_arm) ? ' - ' . $student->class_arm->arm_name : '' ?></div>
                            <div style="margin-bottom: 8px;"><strong>Term:</strong> <?= $settings->semester->name ?></div>
                            <div style="margin-bottom: 8px;"><strong>Session:</strong> <?= $settings->session->name ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card" style="border: 1px solid #ddd; margin-bottom: 10px;">
                        <div class="card-body" style="padding: 15px;">
                            <h6 style="font-weight: bold; margin-bottom: 15px; color: #333; border-bottom: 1px solid #eee; padding-bottom: 5px;">ACADEMIC & ATTENDANCE</h6>
                <?php
                // Get values directly from database for display
                $connection = \Cake\Datasource\ConnectionManager::get('default');
                $sql = "SELECT currenttermends, nexttermbegins FROM settings WHERE id = ?";
                $result = $connection->execute($sql, [$settings->id])->fetch('assoc');
                
                // Calculate the converted values
                $currenttermends_value = !empty($result['currenttermends']) ? 
                    date('d-m-Y', strtotime(str_replace('/', '-', $result['currenttermends']))) : '';
                $nexttermbegins_value = !empty($result['nexttermbegins']) ? 
                    date('d-m-Y', strtotime(str_replace('/', '-', $result['nexttermbegins']))) : '';
                            
                            // Get attendance data for the current term
                            $attendanceTable = \Cake\ORM\TableRegistry::getTableLocator()->get('Attendances');
                            
                            // Calculate term start and end dates (approximate)
                            $termStart = date('Y-m-01'); // Start of current month
                            $termEnd = date('Y-m-t'); // End of current month
                            
                            // Get all attendance records for this student in current term
                            $attendanceRecords = $attendanceTable->find()
                                ->where([
                                    'student_id' => $student->id,
                                    'attendance_date >=' => $termStart,
                                    'attendance_date <=' => $termEnd
                                ])
                                ->all();
                            
                            $totalDays = $attendanceRecords->count();
                            $presentDays = 0;
                            $attendancePercentage = 0;
                            
                            foreach ($attendanceRecords as $record) {
                                if (in_array($record->status, ['present', 'late'])) {
                                    $presentDays++;
                                }
                            }
                            
                            $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0;
                            ?>
                            <div style="margin-bottom: 8px;"><strong>Current Term Ends:</strong> <?= $currenttermends_value ?></div>
                            <div style="margin-bottom: 8px;"><strong>Next Term Begins:</strong> <?= $nexttermbegins_value ?></div>
                            <div style="margin-bottom: 8px;"><strong>Total School Days:</strong> <?= $totalDays ?></div>
                            <div style="margin-bottom: 8px;"><strong>Days Present:</strong> <?= $presentDays ?></div>
                            <div style="margin-bottom: 8px;"><strong>Attendance:</strong> 
                                <span class="badge badge-<?= $attendancePercentage >= 80 ? 'success' : ($attendancePercentage >= 70 ? 'warning' : 'danger') ?>" style="font-size: 12px;">
                                    <?= $attendancePercentage ?>%
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <br />
            <!-- Results Report Card -->
            <div class="card shadow mb-4" style="border: 2px solid #333;">
                <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #333;">
                    <h6 class="m-0 font-weight-bold text-center" style="font-size: 18px; color: #333;">ACADEMIC PERFORMANCE REPORT</h6>
                </div>
                
                <div class="card-body" style="padding: 20px;">
                    <div class="table-responsive">
                        <table class="table table-bordered" style="border: 2px solid #333; margin-bottom: 0; min-width: 800px;">
                            <thead style="background-color: #e9ecef;">
                                <tr>
                                    <th style="border: 1px solid #333; padding: 12px; font-weight: bold; text-align: center; background-color: #dee2e6;">SUBJECT</th>
                                    <th style="border: 1px solid #333; padding: 12px; font-weight: bold; text-align: center; background-color: #dee2e6;">CA</th>
                                    <th style="border: 1px solid #333; padding: 12px; font-weight: bold; text-align: center; background-color: #dee2e6;">1ST EXAM</th>
                                    <th style="border: 1px solid #333; padding: 12px; font-weight: bold; text-align: center; background-color: #dee2e6;">2ND EXAM</th>
                                    <th style="border: 1px solid #333; padding: 12px; font-weight: bold; text-align: center; background-color: #dee2e6;">3RD EXAM</th>
                                    <th style="border: 1px solid #333; padding: 12px; font-weight: bold; text-align: center; background-color: #dee2e6;">TOTAL</th>
                                    <th style="border: 1px solid #333; padding: 12px; font-weight: bold; text-align: center; background-color: #dee2e6;">GRADE</th>
                                    <th style="border: 1px solid #333; padding: 12px; font-weight: bold; text-align: center; background-color: #dee2e6;">REMARK</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total_score = 0;
                                $subjects = 0;
                                $total_marks = 0;
                                foreach ($results as $result):
                                    $total_score += $result->total;
                                    $total_marks += $result->total;
                                    $subjects++;
                                ?>
                                    <tr>
                                        <td style="border: 1px solid #333; padding: 10px; font-weight: 500;"><?= $result->has('subject') ? $result->subject->name : '' ?></td>
                                        <td style="border: 1px solid #333; padding: 10px; text-align: center;"><?= $this->Number->format($result->ca ?? 0) ?></td>
                                        <td style="border: 1px solid #333; padding: 10px; text-align: center;"><?= $this->Number->format($result->first_exam ?? 0) ?></td>
                                        <td style="border: 1px solid #333; padding: 10px; text-align: center;"><?= $this->Number->format($result->second_exam ?? 0) ?></td>
                                        <td style="border: 1px solid #333; padding: 10px; text-align: center;"><?= $this->Number->format($result->third_exam ?? 0) ?></td>
                                        <td style="border: 1px solid #333; padding: 10px; text-align: center; font-weight: bold;"><?= $this->Number->format($result->total) ?></td>
                                        <td style="border: 1px solid #333; padding: 10px; text-align: center;">
                                            <span class="badge badge-<?= $result->grade == 'A' ? 'success' : ($result->grade == 'B' ? 'primary' : ($result->grade == 'C' ? 'info' : ($result->grade == 'D' ? 'warning' : ($result->grade == 'E' ? 'secondary' : 'danger')))) ?>" style="font-size: 14px; padding: 6px 12px;">
                                                <?= h($result->grade) ?>
                                            </span>
                                        </td>
                                        <td style="border: 1px solid #333; padding: 10px; text-align: center; font-style: italic;"><?= h($result->remark) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                
                                <!-- Summary Row -->
                                <tr style="background-color: #f8f9fa; font-weight: bold;">
                                    <td style="border: 1px solid #333; padding: 12px; text-align: center;">TOTAL</td>
                                    <td style="border: 1px solid #333; padding: 12px; text-align: center;">-</td>
                                    <td style="border: 1px solid #333; padding: 12px; text-align: center;">-</td>
                                    <td style="border: 1px solid #333; padding: 12px; text-align: center;">-</td>
                                    <td style="border: 1px solid #333; padding: 12px; text-align: center;">-</td>
                                    <td style="border: 1px solid #333; padding: 12px; text-align: center; background-color: #e9ecef;"><?= $this->Number->format($total_marks) ?></td>
                                    <td style="border: 1px solid #333; padding: 12px; text-align: center; background-color: #e9ecef;">
                                        <?php 
                                        $average = $subjects > 0 ? $total_marks / $subjects : 0;
                                        $overall_grade = '';
                                        if ($average >= 75) $overall_grade = 'A';
                                        elseif ($average >= 70) $overall_grade = 'B';
                                        elseif ($average >= 50) $overall_grade = 'C';
                                        elseif ($average >= 45) $overall_grade = 'D';
                                        elseif ($average >= 40) $overall_grade = 'E';
                                        else $overall_grade = 'NI';
                                        ?>
                                        <span class="badge badge-<?= $overall_grade == 'A' ? 'success' : ($overall_grade == 'B' ? 'primary' : ($overall_grade == 'C' ? 'info' : ($overall_grade == 'D' ? 'warning' : ($overall_grade == 'E' ? 'secondary' : 'danger')))) ?>" style="font-size: 16px; padding: 8px 16px;">
                                            <?= $overall_grade ?>
                                        </span>
                                    </td>
                                    <td style="border: 1px solid #333; padding: 12px; text-align: center; background-color: #e9ecef;">
                                        <?php
                                        switch($overall_grade) {
                                            case 'A': echo 'Excellent'; break;
                                            case 'B': echo 'Very Good'; break;
                                            case 'C': echo 'Good'; break;
                                            case 'D': echo 'Average'; break;
                                            case 'E': echo 'Pass'; break;
                                            default: echo 'Needs Improvement'; break;
                                        }
                                        ?>
                                    </td>
                                </tr>
                            </tbody>
                            <div class="watermark"> 
                                <p>STUDENT COPY!</p>
                            </div>
                        </table>
                        </div>
                        
                        <!-- Grading Key Section -->
                        <div style="margin-top: 20px; padding: 15px; background-color: #f8f9fa; border: 1px solid #333;">
                            <h6 style="font-weight: bold; margin-bottom: 10px; text-align: center;">GRADING KEY</h6>
                            <div style="display: flex; justify-content: space-around; flex-wrap: wrap; text-align: center;">
                                <div style="margin: 5px;">
                                    <span class="badge badge-success" style="font-size: 12px; padding: 4px 8px;">A</span>
                                    <small style="display: block; margin-top: 2px;">Excellent (100 - 75)</small>
                                </div>
                                <div style="margin: 5px;">
                                    <span class="badge badge-primary" style="font-size: 12px; padding: 4px 8px;">B</span>
                                    <small style="display: block; margin-top: 2px;">Very Good (74 - 70)</small>
                                </div>
                                <div style="margin: 5px;">
                                    <span class="badge badge-info" style="font-size: 12px; padding: 4px 8px;">C</span>
                                    <small style="display: block; margin-top: 2px;">Good (69 - 50)</small>
                                </div>
                                <div style="margin: 5px;">
                                    <span class="badge badge-warning" style="font-size: 12px; padding: 4px 8px;">D</span>
                                    <small style="display: block; margin-top: 2px;">Average (49 - 45)</small>
                                </div>
                                <div style="margin: 5px;">
                                    <span class="badge badge-secondary" style="font-size: 12px; padding: 4px 8px;">E</span>
                                    <small style="display: block; margin-top: 2px;">Pass (44 - 40)</small>
                                </div>
                                <div style="margin: 5px;">
                                    <span class="badge badge-danger" style="font-size: 12px; padding: 4px 8px;">NI</span>
                                    <small style="display: block; margin-top: 2px;">Needs Improvement (39 - 0)</small>
                                </div>
                            </div>
                        </div>

                        <?php
                        $average = $subjects > 0 ? $total_marks / $subjects : 0;
                        $performance_tier = '';
                        $tier_color = '';
                        
                        if ($average >= 90) {
                            $performance_tier = 'Exceptional';
                            $tier_color = 'success';
                        } elseif ($average >= 75) {
                            $performance_tier = 'Excellent';
                            $tier_color = 'primary';
                        } elseif ($average >= 60) {
                            $performance_tier = 'Very Good';
                            $tier_color = 'info';
                        } elseif ($average >= 50) {
                            $performance_tier = 'Good/Average';
                            $tier_color = 'warning';
                        } else {
                            $performance_tier = 'Needs Improvement';
                            $tier_color = 'danger';
                        }
                        ?>
                        <div style="text-align: center; margin: 20px 0; padding: 15px; background-color: #f8f9fa; border-radius: 8px;">
                            <span><strong>Average: </strong><?= $this->Number->format($average, ['places' => 1]) ?>%</span>
                            <span class="badge badge-<?= $tier_color ?>" style="margin-left: 10px; font-size: 14px; padding: 6px 12px;">
                                <?= $performance_tier ?>
                            </span>
                        </div>
                        
                        <!-- Comments and Stamp Section -->
                        <div class="row" style="margin-top: 20px; overflow-x: hidden;">
                            <div class="col-md-6" style="word-wrap: break-word; overflow-wrap: break-word;">
                                <br /> 
                                <?php
                                // Calculate overall average for comments
                                $overall_average = $subjects > 0 ? $total_marks / $subjects : 0;
                                
                                // Form Master's Comment based on performance
                                $form_master_comment = '';
                                if ($overall_average >= 90) {
                                    $form_master_comment = 'A model student; highly engaged and motivated.';
                                } elseif ($overall_average >= 75) {
                                    $form_master_comment = 'Excellent attitude and active class participation.';
                                } elseif ($overall_average >= 60) {
                                    $form_master_comment = 'Shows good focus and consistent effort in class.';
                                } elseif ($overall_average >= 50) {
                                    $form_master_comment = 'Generally well-behaved; encourage more active participation.';
                                } else {
                                    $form_master_comment = 'Must improve behavior and classroom focus to progress.';
                                }
                                
                                // Principal's Comment based on performance
                                $principal_comment = '';
                                if ($overall_average >= 90) {
                                    $principal_comment = 'Truly outstanding performance.';
                                } elseif ($overall_average >= 75) {
                                    $principal_comment = 'Excellent achievement this term.';
                                } elseif ($overall_average >= 60) {
                                    $principal_comment = 'Very good progress and effort.';
                                } elseif ($overall_average >= 50) {
                                    $principal_comment = 'Performance meets expectations.';
                                } else {
                                    $principal_comment = 'Requires immediate focus and support.';
                                }
                                
                                // Get Principal name from settings (rector field)
                                $principal_name = !empty($settings->rector) ? $settings->rector : 'Principal';
                                ?>
                                <div style="margin-bottom: 15px;">
                                    <strong>Form Master's Comment:</strong><br />
                                    <span style="font-style: italic; color: #555;"><?= $form_master_comment ?></span>
                                </div>
                                
                                <div style="margin-bottom: 15px;">
                                    <strong>Principal's Comment:</strong><br />
                                    <span style="font-style: italic; color: #555;"><?= $principal_comment ?></span>
                                </div>
                                
                                <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #ddd;">
                                    <strong>Name of Principal:</strong> <?= $principal_name ?>
                                </div>
                            </div>
                            <div class="col-md-6 text-center">
                                <div style="padding: 15px; display: inline-block; text-align: center; background-color: #f8f9fa; margin-top: 20px;">
                                    <div style="height: 90px; width: 120px; margin: 0 auto; background-color: white; display: flex; align-items: center; justify-content: center;">
                                        <?php 
                                        // Check for dynamic stamp from settings
                                        $stampFile = '';
                                        $stampPath = '';
                                        
                                        if (!empty($settings->school_stamp)) {
                                            $stampFile = $settings->school_stamp;
                                            $stampPath = WWW_ROOT . 'img' . DS . $stampFile;
                                        } else {
                                            // No custom stamp set, show placeholder
                                            $stampFile = '';
                                            $stampPath = '';
                                        }
                                        
                                        if (!empty($stampFile) && file_exists($stampPath)): ?>
                                            <img src="<?= $this->Url->build('/img/' . $stampFile) ?>" alt="School Stamp" style="width: 100%; height: 100%; object-fit: contain;" />
                                        <?php else: ?>
                                            <span style="color: #666; font-size: 10px;">STAMP</span>
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-size: 10px; margin-top: 5px; color: #666;">
                                        Date: <?= date('d-m-Y') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Print and Download Buttons - At the very bottom -->
                        <div style="clear: both; margin-top: 30px; text-align: right;">
                            <a href="<?= $this->Url->build(['controller' => 'Results', 'action' => 'printResult', $student->id, $session_id ?? '', $semester_id ?? '']) ?>" class="btn btn-success" target="_blank" style="margin-right: 10px;">
                                <i class="fa fa-print"></i> Print Result
                            </a>
                            <a href="<?= $this->Url->build(['controller' => 'Results', 'action' => 'downloadPdf', $student->id, $session_id ?? '', $semester_id ?? '']) ?>" class="btn btn-primary" target="_blank">
                                <i class="fa fa-download"></i> Download PDF
                            </a>
                        </div>
                    </div>
                    <br /> <br />
                </div>
            </div>
    </div>
    <?php } ?>
</div>

<script>
function printDiv(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;

    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
}
</script>

<style>
body {
    overflow-x: hidden;
    max-width: 100%;
}

.container-fluid {
    overflow-x: hidden;
    max-width: 100%;
}

table {
    width: 100%;
    table-layout: fixed;
    word-wrap: break-word;
}

.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

@media (max-width: 768px) {
    table {
        font-size: 12px;
    }
    
    td, th {
        padding: 5px !important;
    }
}

/* Fix only the print button styling */
#printResultBtn {
    display: inline-block !important;
    width: auto !important;
    max-width: none !important;
    float: right !important;
    padding: 8px 16px !important;
    font-size: 14px !important;
    min-width: auto !important;
    box-sizing: content-box !important;
}

/* (A) PAGE WATERMARK */
#watermark {
    /* STICK AT BOTTOM RIGHT */
    position: fixed;
    bottom: 10px;
    right: 10px;
    z-index: 999;
 
    /* TRANSPARENCY */
    opacity: 0.5;
 
    /* COSMETICS */
    text-align: right;
    color: red;
    font-size: 52px;
    /* disable select and copy  */
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

.watermark {
    position: fixed;
    opacity: 0.2;
    /* Safari */
    -webkit-transform: rotate(-60deg);
    /* Firefox */
    -moz-transform: rotate(-60deg);
    /* IE */
    -ms-transform: rotate(-60deg);
    /* Opera */
    -o-transform: rotate(-60deg);
    /* Internet Explorer */
    filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
    position: absolute;
    font-size: 100px;
    margin-top: -50px;
    margin-left: -30px;
    white-space: nowrap;
}
 
@media Print {
    body {
        visibility: hidden
    }
    .PrintDis {
        visibility: visible;
        position: fixed; 
        top: 0; 
        left: 0
    }
}
</style>
        

