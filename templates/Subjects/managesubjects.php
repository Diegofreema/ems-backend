<?php
$userdata = $this->request->getSession()->read('usersinfo');
$userrole = $this->request->getSession()->read('usersroles');
?>
<!-- Begin Page Content -->
        <div class="content container-fluid">
          <div class="page-header">
                <div class="row">
                    <div class="col-sm-12">
                        <h3 class="page-title">Manage Subjects</h3>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard'])

                                ?></li>
                            <li class="breadcrumb-item active">Manage Subjects</li>
                        </ul>
                    </div>
                </div>
            </div>
         <div style="padding-bottom: 10px; margin-bottom: 20px;"><?= $this->Html->link(__(' '), ['action' => 'newsubject'],
                            ['class'=>'btn-circle btn-lg fa fa-plus float-right','title'=>'add new subject']) ?>
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800"> &nbsp; </h1></div>

          <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Subject Manager</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
               <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                       <thead>
                    <tr>
                        <th> NAME</th>
                       <th>CODE</th>
                       <th>Class</th>
                      
                       <th>ACTIONS</th>
                    </tr>
                  </thead>
                  <tfoot>
                    <tr>
                       <th> NAME</th>
                       <th>CODE</th>
                       <th>Class</th>
                       
                       <th>ACTIONS</th>
                    </tr>
                  </tfoot>
                  <tbody>
                  <?php foreach ($subjects as $subjects): ?>
                                        <tr>

                                            <td><?= h($subjects->name) ?></td>
                                            <td><?= h($subjects->subjectcode) ?></td>
                                         <td><?= h($subjects->department->name ?? '') ?></td>
                                            


                                            <td class="actions">
                                                <?= $this->Html->link(__('View'), ['action' => 'viewsubject', $subjects->id, $this->GenerateUrl($subjects->name)], ['class'=>'btn btn-round btn-info fa fa-eye']) ?>
                                                <?= $this->Html->link(__('Update'), ['controller'=>'Subjects','action' => 'updatesubject', $subjects->id, $this->GenerateUrl($subjects->name)], ['class'=>'btn btn-round btn-primary fa fa-edit']) ?>
                                              <?= $this->Html->link(__('Add Contaent'), ['controller'=>'Topics','action' => 'addtopic', $subjects->id, $this->GenerateUrl($subjects->name)], ['class'=>'btn btn-round btn-success fa fa-eye','title'=>'add contents']) ?> 
                                                 <?= $this->Html->link(__('View Contaents'), ['controller'=>'Topics','action' => 'viewcontents', $subjects->id, $this->GenerateUrl($subjects->name)], ['class'=>'btn btn-round btn-info fa fa-eye','title'=>'view contents']) ?> 
 <?php if ($subjects->status == 1){
							echo $this->Form->postLink(__('Disable'), ['action' => 'changesubjectstatus', $subjects->id, 0], ['confirm' => __('Are you sure you want disable {0}?', $subjects->name),'class'=>'btn btn-round btn-danger']);
													
                                                    } else{
                                                        echo $this->Form->postLink(__('Enable'), ['action' => 'changesubjectstatus', $subjects->id, 1], ['confirm' => __('Are you sure you want to enable {0}?', $subjects->name),'class'=>'btn btn-round btn-success']); 
                                                    
							} ?>
										
												 
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
               
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>
        <!-- /.container-fluid -->


