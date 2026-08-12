<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>



<!-- Begin Page Content -->
        <div class="container-fluid">

         <div style="padding-bottom: 10px; margin-bottom: 20px;"><?= $this->Html->link(__(' '), ['controller'=>'Employees','action' => 'generatepayslips'],
                            ['class'=>'btn-circle btn-lg fa fa-plus float-right','title'=>'generate payslips']) ?>
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800">Manage Employees Payslips</h1></div>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Employees Payslip Manager</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
               <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                  <thead>
                    <tr>
                    <th>Name</th>
                    <th>EMPID</th>
                    <th>State of Origin</th>
                    <th>LGA</th>
                    <th>Phone</th>
                    <th>Photo</th>
                    <th>HQN</th>
                    <th>Grade</th>
                   
                    <th>Department</th>
                  <th>Email</th>
                    <th>Gender</th>
              
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payslips->employees as $employee): ?>
                <tr>
                
                    <td><?= h($employee->sname.' '.$employee->fname.' '.$employee->mname) ?></td>
              
                    <td><?= h($employee->empid) ?></td>
                    <td><?= $employee->has('state') ? $employee->state->name : '' ?></td>
                    <td><?= $employee->has('lga') ? $employee->lga->name : '' ?></td>
                  
                    <td><?= h($employee->phone) ?></td>
                    <td><?= $this->Html->image('../staff_files/'.$employee->photo, ['alt' =>$employee->sname, 'class' => 'img-circle profile_img',
          'style' => 'width:50px;height:50px;'])
      ?></td>
                  
                    <td><?= h($employee->hqn) ?></td>
                    <td><?= $employee->has('staffgrade') ? $employee->staffgrade->name : '' ?></td>
                    <td><?= $employee->has('staffdepartment') ? $employee->staffdepartment->name : '' ?></td>
                    <td><?= $employee->has('user') ? $employee->user->username : '' ?></td>
                    <td><?= h($employee->gender) ?></td>
                    
                   
                    <td class="actions">
                        <?= $this->Html->link(__(' View'), ['action' => 'view', $employee->id,$this->Generateurl($employee->sname)],['class' => 'btn btn-round btn-info fa fa-eye', 'title' => 'view employee details']) ?>
                        <?= $this->Html->link(__(' Edit'), ['action' => 'editemployee', $employee->id,$this->Generateurl($employee->sname)],['class' => 'btn btn-round btn-primary fa fa-edit', 'title' => 'update details']) ?>
                      <?php if($employee->user->userstatus=="Enabled"){
                      echo $this->Html->link(__(' Disable'), ['action' => 'disableemployee', $employee->user_id,$this->Generateurl($employee->sname)],['class' => 'btn btn-round btn-warning fa fa-edit', 'title' => 'disable employee']);    
                      }
                      else { echo $this->Html->link(__(' Enable'), ['action' => 'enableemployee', $employee->user_id,$this->Generateurl($employee->sname)],['class' => 'btn btn-round btn-success fa fa-edit', 'title' => 'enable employee']);}
                      ?>
  <?= $this->Form->postLink(__(' Delete'), ['action' => 'delete', $employee->id], ['confirm' => __('Are you sure you want to delete # {0}?', $employee->sname),'class'=>'btn btn-round btn-danger fa fa-times']) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
