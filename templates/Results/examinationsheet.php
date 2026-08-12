<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
$settings  = $this->request->getSession()->read('settings');
?>

<!-- Begin Page Content -->
<div class="content container-fluid">
    <div class="page-header donotprint">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Examination Sheet</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard']) ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link(' Manage Results', ['controller' => 'Results', 'action' => 'manageresults'], ['title' => 'Manage Results']) ?></li>
                    <li class="breadcrumb-item active">Examination Sheet</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="row donotprint">
        <div class="col-lg-12">
            <div class="card shadow mb-4 donotprint">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Filter Examination Sheet</h6>
                </div>
                <div class="card-body">
                    <?= $this->Form->create(null) ?>
                    <div class="row">
                        <div class="col-md-3">
                            <?= $this->Form->control('department_id', [
                                'options' => $departments,
                                'label' => 'Class',
                                'empty' => 'Select Class',
                                'class' => 'form-control form-control-user2',
                                'onChange' => 'getClassArms(this.value)'
                            ]) ?>
                        </div>
                        <div class="col-md-3" id="classArms">
                            <label for="class_arm_id">Class Arm</label>
                            <select name="class_arm_id" class="form-control form-control-user2">
                                <option value="">Select Class Arm</option>
                                <?php if (!empty($classArms)): ?>
                                    <?php 
                                    $selectedClassArmId = $this->request->getData('class_arm_id');
                                    foreach ($classArms as $classArm): 
                                    ?>
                                        <option value="<?= $classArm->id ?>" <?= $classArm->id == $selectedClassArmId ? 'selected' : '' ?>>
                                            <?= h($classArm->department->name . ' - ' . $classArm->arm_name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <?= $this->Form->control('session_id', [
                                'options' => $sessions,
                                'label' => 'Session',
                                'empty' => 'Select Session',
                                'class' => 'form-control form-control-user2'
                            ]) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $this->Form->control('semester_id', [
                                'options' => $semesters,
                                'label' => 'Term',
                                'empty' => 'Select Term',
                                'class' => 'form-control form-control-user2'
                            ]) ?>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <?= $this->Form->button('Generate Examination Sheet', [
                                'class' => 'btn btn-primary btn-user btn-block'
                            ]) ?>
                        </div>
                    </div>
                    <?= $this->Form->end() ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($examinationData)): ?>
    <!-- Examination Sheet -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Examination Sheet - <?= h($classInfo->name) ?>
                        <?php if (!empty($class_arm_id)): ?>
                            - <?= h($examinationData[0]['student']->class_arm->arm_name ?? '') ?>
                        <?php endif; ?>
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Print/Download Buttons -->
                    <div class="row mb-3 donotprint">
                        <div class="col-md-12 text-right">
                            <button onclick="printExaminationSheet()" class="btn btn-success">
                                <i class="fa fa-print"></i> Print
                            </button>
                            <button onclick="downloadPDF()" class="btn btn-info">
                                <i class="fa fa-download"></i> Download PDF
                            </button>
                        </div>
                    </div>

                    <!-- Examination Sheet Content -->
                    <div id="examination-sheet" class="examination-sheet">
                        <!-- School Header -->
                        <div class="row mb-4">
                            <div class="col-md-2">
                                <img src="<?= $this->Url->image('logolta.png') ?>" alt="School Logo" class="school-logo" style="max-width: 150px; max-height: 150px;">
                            </div>
                            <div class="col-md-8 text-center">
                                <h2 class="mb-1"><?= $settings->name?></h2>
                                <p class="mb-1">OWERRI, IMO STATE, NIGERIA</p>
                                <p class="mb-3"><em>"...Pride and Power..."</em></p>
                                <?php 
                                $classType = '';
                                if (strpos($classInfo->name, 'JSS') !== false) {
                                    $classType = 'JUNIOR SECONDARY';
                                } elseif (strpos($classInfo->name, 'SSS') !== false) {
                                    $classType = 'SENIOR SECONDARY';
                                } else {
                                    $classType = 'SECONDARY';
                                }
                                ?>
                                <h3 class="mb-1"><?= h($classType) ?></h3>
                                <h2 class="mb-4" style="background-color: #000; color: #fff; padding: 10px; display: inline-block; font-size: 24px; font-weight: bold;">EXAMINATION SHEET</h2>
                            </div>
                            <div class="col-md-2"></div>
                        </div>

                        <!-- Class Information -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <p><strong>Class:</strong> <?= h($classInfo->name) ?></p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Class Arm:</strong> <?= h($examinationData[0]['student']->class_arm->arm_name ?? 'N/A') ?></p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>No. In Class:</strong> <?= count($examinationData) ?></p>
                            </div>
                        </div>

                        <!-- Results Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered examination-table">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="text-center">S/N</th>
                                        <th rowspan="2" class="text-center">NAMES</th>
                                        <?php foreach ($subjects as $subject): ?>
                                        <th colspan="5" class="text-center"><?= h($subject->name) ?></th>
                                        <?php endforeach; ?>
                                        <th rowspan="2" class="text-center">GRAND TOTAL</th>
                                        <th rowspan="2" class="text-center">AVERAGE (%)</th>
                                        <th rowspan="2" class="text-center">POSITION</th>
                                        <th rowspan="2" class="text-center">REMARK</th>
                                    </tr>
                                    <tr>
                                        <?php foreach ($subjects as $subject): ?>
                                        <th class="text-center">C.A</th>
                                        <th class="text-center">1st Exam</th>
                                        <th class="text-center">2nd Exam</th>
                                        <th class="text-center">3rd Exam</th>
                                        <th class="text-center">Total</th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $serialNumber = 1; foreach ($examinationData as $data): ?>
                                    <tr>
                                        <td class="text-center"><?= $serialNumber ?></td>
                                        <td><?= h($data['student']->fname . ' ' . $data['student']->lname . ' ' . $data['student']->mname) ?></td>
                                        <?php foreach ($data['results'] as $result): ?>
                                        <td class="text-center"><?= $result['ca'] > 0 ? $result['ca'] : '-' ?></td>
                                        <td class="text-center"><?= $result['first_exam'] > 0 ? $result['first_exam'] : '-' ?></td>
                                        <td class="text-center"><?= $result['second_exam'] > 0 ? $result['second_exam'] : '-' ?></td>
                                        <td class="text-center"><?= $result['third_exam'] > 0 ? $result['third_exam'] : '-' ?></td>
                                        <td class="text-center"><strong><?= $result['total'] > 0 ? $result['total'] : '-' ?></strong></td>
                                        <?php endforeach; ?>
                                        <td class="text-center"><strong><?= $data['total'] ?></strong></td>
                                        <td class="text-center"><strong><?= $data['average'] ?>%</strong></td>
                                        <td class="text-center"><strong><?= $data['position'] !== null ? $data['position'] : '-' ?></strong></td>
                                        <td class="text-center">
                                            <?php 
                                            $remark = '';
                                            if ($data['average'] >= 70) $remark = 'Excellent';
                                            elseif ($data['average'] >= 60) $remark = 'Good';
                                            elseif ($data['average'] >= 50) $remark = 'Pass';
                                            else $remark = 'Fail';
                                            echo $remark;
                                            ?>
                                        </td>
                                    </tr>
                                    <?php $serialNumber++; endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.examination-sheet {
    font-family: Arial, sans-serif;
    font-size: 12px;
}

.examination-table {
    font-size: 11px;
}

.examination-table th {
    background-color: #f8f9fa;
    font-weight: bold;
    padding: 8px 4px;
    border: 1px solid #dee2e6;
}

.examination-table td {
    padding: 6px 4px;
    border: 1px solid #dee2e6;
    text-align: center;
}

.examination-table td:first-child {
    text-align: left;
    font-weight: bold;
}

@media print {
    /* Set page orientation to landscape */
    @page {
        size: A4 landscape;
        margin: 0.5in;
    }
    
    /* Hide all non-printable elements */
    .donotprint, .card-header, .btn, .breadcrumb, .page-header, .form-control, .form-group, .mt-3, .mb-3, .text-right {
        display: none !important;
    }
    
    /* Show only the examination sheet content */
    .examination-sheet {
        font-size: 8px;
        display: block !important;
        width: 100%;
        max-width: 100%;
    }
    
    .examination-table {
        font-size: 7px;
        width: 100%;
        table-layout: fixed;
        border-collapse: collapse;
    }
    
    /* Ensure table fits in landscape */
    .examination-table th,
    .examination-table td {
        padding: 2px 1px;
        border: 1px solid #000;
        text-align: center;
        font-size: 6px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    /* Make names column wider but still fit */
    .examination-table td:nth-child(2) {
        width: 15%;
        text-align: left;
        padding-left: 2px;
    }
    
    /* Make other columns narrower to fit */
    .examination-table th:not(:nth-child(2)),
    .examination-table td:not(:nth-child(2)) {
        width: 3%;
    }
    
    /* Ensure proper page breaks */
    .examination-sheet {
        page-break-inside: avoid;
    }
    
    /* Hide the filter form completely */
    .card:first-of-type {
        display: none !important;
    }
    
    /* Ensure examination sheet content is visible */
    .card:last-of-type {
        display: block !important;
        box-shadow: none !important;
        border: none !important;
    }
    
    .card-body {
        padding: 0 !important;
    }
    
    /* Make sure all examination sheet elements are visible */
    .examination-sheet * {
        display: block !important;
    }
    
    .examination-sheet table {
        display: table !important;
    }
    
    .examination-sheet tr {
        display: table-row !important;
    }
    
    .examination-sheet td, .examination-sheet th {
        display: table-cell !important;
    }
    
    /* Ensure the table container doesn't overflow */
    .table-responsive {
        overflow: visible !important;
    }
    
    /* Adjust header sections for landscape */
    .examination-sheet .row {
        margin-bottom: 10px !important;
    }
    
    .examination-sheet h2, .examination-sheet h3 {
        margin: 5px 0 !important;
        font-size: 12px !important;
    }
    
    .examination-sheet p {
        margin: 2px 0 !important;
        font-size: 8px !important;
    }
    
    /* Fix class information section for print */
    .examination-sheet .row.mb-4 {
        display: flex !important;
        flex-wrap: nowrap !important;
        margin-bottom: 15px !important;
    }
    
    .examination-sheet .row.mb-4 .col-md-4 {
        flex: 1 !important;
        max-width: 33.333% !important;
        padding: 0 10px !important;
        display: block !important;
    }
    
    .examination-sheet .row.mb-4 p {
        margin: 3px 0 !important;
        font-size: 9px !important;
        font-weight: bold !important;
        line-height: 1.2 !important;
    }
    
    /* Ensure class info is properly aligned */
    .examination-sheet .row.mb-4 p strong {
        font-weight: bold !important;
        font-size: 9px !important;
    }
}
</style>

<script>
function getClassArms(departmentid) {
    if (departmentid) {
        $.ajax({
            url: '../ClassArms/getArmsForDepartment/' + departmentid,
            method: 'GET',
            dataType: 'text',
            success: function(response) {
                var label = '<label for="class_arm_id">Class Arm</label>';
                document.getElementById('classArms').innerHTML = label + response;
            }
        });
    } else {
        document.getElementById('classArms').innerHTML = '<label for="class_arm_id">Class Arm</label><select name="class_arm_id" class="form-control form-control-user2"><option value="">Select Class Arm</option></select>';
    }
}

// Preserve form values on page load
document.addEventListener('DOMContentLoaded', function() {
    // Get the selected department value
    var selectedDept = document.querySelector('select[name="department_id"]').value;
    if (selectedDept) {
        // Trigger class arms loading
        getClassArms(selectedDept);
    }
});

function printExaminationSheet() {
    // Hide all non-printable elements
    var elementsToHide = document.querySelectorAll('.donotprint, .card-header, .btn, .breadcrumb, .page-header, .form-control, .form-group, .card:first-of-type');
    elementsToHide.forEach(function(element) {
        element.style.display = 'none';
    });
    
    // Print the page
    window.print();
    
    // Restore elements after printing
    elementsToHide.forEach(function(element) {
        element.style.display = '';
    });
}

function downloadPDF() {
    // For now, trigger print dialog with PDF option
    // In a real implementation, you would generate a PDF server-side
    printExaminationSheet();
}
</script>