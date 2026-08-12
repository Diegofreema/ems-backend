 <div class="content container-fluid">
  <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">View Subject</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><?= $this->Html->link(' Dashboard', ['controller' => 'Users', 'action' => 'dashboard', $this->GenerateUrl('Dashboard')], ['title' => 'Dashboard'])
  
                            ?></li>
                    <li class="breadcrumb-item"><?= $this->Html->link('Manage Subjects', ['controller' => 'Subjects', 'action' => 'managesubjects'], ['title' => 'manage subjects']) ?></li>
                    <li class="breadcrumb-item active">View Subject</li>
                </ul>
            </div>
        </div>
    </div>

          <!-- Page Heading -->
          <h1 class="h3 mb-4 text-gray-800">Subject : <?= h($subject->name.'  ('.$subject->subjectcode.')') ?></h1>

          <div class="row">

            <div class="col-lg-12">

              <!-- Circle Buttons -->
              <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary"><?=$subject->subjectcode?> 
            
     </h6>
                </div>
                <div class="card-body">
                  <p>List of Departments Offering this Course  </p>
                  <!-- Circle Buttons (Default) -->
                    <?php if (!empty($subject->departments)){
                   foreach ($subject->departments as $departments) {
          
                  echo h($departments->name.'('.$departments->deptcode).')<br />';
                  
                   }} ?>
                  <hr />
                   <p>Teachers  </p>
                   <?php if (!empty($subject->teachers)){
                      
                  foreach ($subject->teachers as $teachers){
          
                  echo h($teachers->firstname.'  '.$teachers->lastname).'<br />';
                  
                   }} ?>
               
                 
                </div>
              </div>

            </div>


          </div>

        </div>
        <!-- /.container-fluid -->

