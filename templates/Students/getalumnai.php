<?php
  $userdata = $this->request->getSession()->read('usersinfo');
  $userrole = $this->request->getSession()->read('usersroles');
?>
<?=
  $this->Html->css(['custom.min', 'dataTables.bootstrap.min', 'buttons.bootstrap.min', 'fixedHeader.bootstrap.min', 'responsive.bootstrap.min', 'scroller.bootstrap.min'])
?>
<?= $this->fetch('css') ?>

<!-- Begin Page Content -->
<div class="container-fluid">
    <div style="padding-bottom: 10px; margin-bottom: 20px;">
       
        <!-- Page Heading -->
        <div class="p-5">
            <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Search Alumni </h1>
            </div>
            <?= $this->Form->create(null) ?>
            <fieldset>
                <div class="form-group row">
<!--                    <div class="col-sm-4 mb-3 mb-sm-0">
                        <?php
                          echo $this->Form->control('startdate', ['label' => 'Start Date', 'placeholder' => 'Start Date',
                              'class' => 'form-control form-control-user2', 'type' => 'text', 'id' => 'datepicker']);
                        ?>
                    </div>
                    <div class="col-sm-4 mb-3 mb-sm-0">
                        <?php
                          echo $this->Form->control('enddate', ['label' => 'End Date', 'placeholder' => 'End Date',
                              'class' => 'form-control form-control-user2', 'type' => 'text', 'id' => 'datepicker2']);
                        ?>
                    </div>-->
                    <div class="col-sm-8 mb-3 mb-sm-0">
<?= $this->Form->control('department_id', ['options' => $departments, 'label' => 'Select Department', 'empty' => 'Select Department', 'class' => 'select2_multiple form-control form-control-user']) ?>
                    </div>

                </div>
            </fieldset>
            <br /> <br />
<?= $this->Form->button('Search', ['class' => 'btn btn-primary btn-user btn-block']) ?>   
            <?= $this->Form->end() ?>
        </div>
        <h1 class="h3 mb-2 text-gray-800">Manage Alumni</h1></div>


    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary"> Alumni</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                    <thead>
                        <tr>

                            <th scope="col"><?= $this->Paginator->sort('Name') ?></th>



                            <th scope="col"><?= $this->Paginator->sort('Regno') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('Department') ?></th>
                             <th>Class</th>
                              <th>Programme</th>
                            <th scope="col"><?= $this->Paginator->sort('Passport') ?></th>

                            <th scope="col"><?= $this->Paginator->sort('DOB') ?></th>
                            <th>State</th>
                            <th>LGA</th>
                            <th>Autonomous Community</th>
                            <th>Phone</th>
                            <th>Gender</th>
                            <th>Email</th>
                            <th>App_No</th>
                            <th>Admission Date</th>
                              <th>Status</th>
                            <th scope="col" class="actions"><?= __('Actions') ?></th>
                        </tr>
                    </thead>


                    <tfoot>
                        <tr>

                            <th scope="col"><?= $this->Paginator->sort('Name') ?></th>



                            <th scope="col"><?= $this->Paginator->sort('Regno') ?></th>
                            <th scope="col"><?= $this->Paginator->sort('Department') ?></th>
                          
                              <th>Class</th>
                              <th>Programme</th>
                            <th scope="col"><?= $this->Paginator->sort('Passport') ?></th>

                            <th scope="col"><?= $this->Paginator->sort('DOB') ?></th>
                            <th>State</th>
                            <th>LGA</th>
                            <th>Autonomous Community</th>
                            <th>Phone</th>
                            <th>Gender</th>
                            <th>Email</th>
                            <th>App_No</th>
                            <th>Admission Date</th>
                              <th>Status</th>
                            <th scope="col" class="actions"><?= __('Actions') ?></th>
                        </tr>
                    </tfoot>


                    <tbody>
<?php foreach ($alumni as $student): ?>
                              <tr>

                                  <td>
                                     <?= $this->Html->link($student->fname . ' ' . $student->lname, ['controller' => 'Students', 'action' => 'viewstudent', $student->id,$this->generateurl($student->lname)])?>
   </td>



                                  <td><?= h($student->regno) ?></td>
                                  <td><?= $student->has('department') ? $this->Html->link($student->department->name, ['controller' => 'Departments', 'action' => 'viewdepartment', $student->department->id]) : '' ?></td>
                                  <td><?= h($student->level->name) ?></td>
                                    <td><?= h($student->programe->name) ?></td>
                  <!--                <td><?= h($student->olevelresulturl) ?></td>
                                  <td><?= $this->Number->format($student->jamb) ?></td>
                                  <td><?= h($student->birthcerturl) ?></td>
                                  <td><?= h($student->othercerts) ?></td>-->

                                  <!--                
                                                  <td><?= $student->has('country') ? $this->Html->link($student->country->name, ['controller' => 'Countries', 'action' => 'view', $student->country->id]) : '' ?></td>
                                                  <td><?= h($student->address) ?></td>
                                                  <td><?= h($student->phone) ?></td>
                                                  <td><?= h($student->fathersname) ?></td>
                                                  <td><?= h($student->mothersname) ?></td>
                                                  <td><?= h($student->fatherphone) ?></td>
                                                  <td><?= h($student->motherphone) ?></td>
                                                  <td><?= h($student->fathersjob) ?></td>
                                                  <td><?= h($student->mothersjob) ?></td>-->
                                  <td> <?= $this->Html->image($student->passporturl, ['alt' => 'IMG', 'class' => 'img-circle profile_img',
          'style' => 'width:80px;height:80px;'])
      ?>
                                  </td>
                                  <td><?= h($student->dob) ?></td>
                                  <td> <?= $student->has('state') ? $student->state->name : '' ?> </td>
                                  <td><?= h($student->lga->name) ?></td>
                                  <td><?= h($student->community) ?></td>
                                  <td><?= h($student->phone) ?></td>
                                  <td><?= h($student->gender) ?></td>
                                  <td><?= h($student->user->username) ?></td>
                                  <td><?= h($student->application_no) ?></td>
                                  <td><?= h($student->admissiondate) ?></td>
                                   <td><?= h($student->status) ?></td>
                                  <td class="actions">
                                      
                                      <?= $this->Html->link(__(' '), ['action' => 'updatestudent', $student->id, $this->Generateurl($student->fname)], ['class' => 'btn btn-round btn-primary fa fa-edit', 'title' => 'view student details'])
                                      ?>
                                      &nbsp;<?= $this->Html->link(__(' Update Email'), ['action' => 'validateemail', $student->id, $this->Generateurl($student->fname)], ['class' => 'btn btn-round btn-info fa fa-edit', 'title' => 'update username'])
                                      ?>
                                       &nbsp;<?= $this->Html->link(__(' Reset Password'), ['action' => 'resetpassword', $student->user_id, $this->Generateurl($student->fname)], ['class' => 'btn btn-round btn-warning fa fa-edit', 'title' => 'reset password'])
                                      ?>
                                  </td>
                              </tr>
                          <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
<?=
  $this->Html->script(['jquery.min', 'jquery.dataTables.min', 'dataTables.bootstrap.min',
      'dataTables.buttons.min', 'buttons.flash.min', 'buttons.html5.min', 'dataTables.fixedHeader.min', 'buttons.print.min'
      , 'dataTables.keyTable.min', 'dataTables.responsive.min', 'dataTables.scroller.min',
      'jszip.min', 'pdfmake.min', 'vfs_fonts', 'fastclick', 'jspdf'])
?>

<?= $this->fetch('script') ?>


<script>
    $(document).ready(function () {
        var handleDataTableButtons = function () {
            if ($("#datatable-buttons").length) {
                $("#datatable-buttons").DataTable({
                    dom: "Bfrtip",
                    buttons: [
                        {
                            extend: "copy",
                            className: "btn-sm"
                        },
                        {
                            extend: "csv",
                            className: "btn-sm"
                        },
                        {
                            extend: "excel",
                            className: "btn-sm"
                        },
                        {
                            extend: "pdfHtml5",
                            className: "btn-sm"
                        },
                        {
                            extend: "print",
                            className: "btn-sm"
                        },
                    ],
                    responsive: true
                });
            }
        };

        TableManageButtons = function () {
            "use strict";
            return {
                init: function () {
                    handleDataTableButtons();
                }
            };
        }();

        $('#datatable').dataTable();

        $('#datatable-keytable').DataTable({
            keys: true
        });

        $('#datatable-responsive').DataTable();

        $('#datatable-scroller').DataTable({
            ajax: "js/datatables/json/scroller-demo.json",
            deferRender: true,
            scrollY: 380,
            scrollCollapse: true,
            scroller: true
        });

        $('#datatable-fixed-header').DataTable({
            fixedHeader: true
        });

        var $datatable = $('#datatable-checkbox');

        $datatable.dataTable({
            'order': [[1, 'asc']],
            'columnDefs': [
                {orderable: false, targets: [0]}
            ]
        });
        $datatable.on('draw.dt', function () {
            $('input').iCheck({
                checkboxClass: 'icheckbox_flat-green'
            });
        });

        TableManageButtons.init();
    });
</script>




