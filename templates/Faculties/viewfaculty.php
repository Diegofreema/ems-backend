 <div class="container-fluid">

          <!-- Page Heading -->
          <h1 class="h3 mb-4 text-gray-800"> <?= h($faculty->name) ?></h1>

          <div class="row">

            <div class="col-lg-6">

              <!-- Circle Buttons -->
              <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Departments/Codes</h6>
                </div>
                <div class="card-body">
                    <p><b>A list of Departments</b></p>
                  <!-- Circle Buttons (Default) -->
                  <?php if (!empty($faculty->departments)){
                   foreach ($faculty->departments as $departments){
          
                  echo h($departments->name.' - '.$departments->deptcode).'<br />';
                  
                   }} ?>
                 
                </div>
              </div>

            </div>

            <div class="col-lg-6">

              <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Programes/Codes</h6>
                </div>
                <div class="card-body">
                  <p>A list of Programmes in this Faculty.</p>
                  <?php 
//                    if (!empty($faculty->departments)){
//                   foreach ($faculty->departments as $departments){
//                       foreach ($departments->programes as $programe){
//                           echo $programe->name.' / '.$programe->programecode.'<br />';
//                       }
//          
//                  
//                   }}
                   ?>
                 
                </div>
              </div>

            </div>

          </div>

        </div>
        <!-- /.container-fluid -->

