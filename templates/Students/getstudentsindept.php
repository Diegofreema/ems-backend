          <!-- DataTales Example -->
          <div class="card shadow mb-4" id="students">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Student Manager</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
            <tr>
          
                 <th scope="col"><?= $this->Paginator->sort('first Name') ?></th>
                <th scope="col"><?= $this->Paginator->sort('last Name') ?></th>
                <th scope="col"><?= $this->Paginator->sort('M Name') ?></th>
                <th scope="col"><?= $this->Paginator->sort('DOB') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Date Admitted') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Department') ?></th>
                <th scope="col"><?= $this->Paginator->sort('email') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Passport') ?></th>
               
                <th scope="col"><?= $this->Paginator->sort('Regno') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
                  </thead>
            
            
              <tfoot>
            <tr>
          
                 <th scope="col"><?= $this->Paginator->sort('first Name') ?></th>
                <th scope="col"><?= $this->Paginator->sort('last Name') ?></th>
                <th scope="col"><?= $this->Paginator->sort('M Name') ?></th>
                <th scope="col"><?= $this->Paginator->sort('DOB') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Date Admitted') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Department') ?></th>

                <th scope="col"><?= $this->Paginator->sort('email') ?></th>

                <th scope="col"><?= $this->Paginator->sort('Passport') ?></th>
               
                <th scope="col"><?= $this->Paginator->sort('Regno') ?></th>
                <th scope="col" class="actions"><?= __('Actions') ?></th>
            </tr>
              </tfoot>
            
        </thead>
         <tbody>
            <?php foreach ( $students as $student): ?>
            <tr>
                
                <td><?= h($student->fname) ?></td>
                <td><?= h($student->lname) ?></td>
                <td><?= h($student->mname) ?></td>
                <td><?= h($student->dob) ?></td>
                <td><?= h(date('D, d M Y H:i a', strtotime($student->joindate))) ?></td>
                <td><?= $student->department->name ?></td>
                <td><?= h($student->email) ?></td>
                <td> <?= $this->Html->image($student->passporturl, ['alt' => 'IMG', 'class' => 'img-circle profile_img',
                                    'style' => 'width:80px;height:80px;']) ?>
               </td>
              
                <td><?= h($student->regno) ?></td>
                <td class="actions">
                    <?= $this->Html->link(__(' '), ['action' => 'viewstudent', $student->id,$this->Generateurl($student->fname)],
                            ['class'=>'btn btn-round btn-info fa fa-eye','title'=>'view student details']) ?>
                    <?= $this->Html->link(__(' '), ['action' => 'updatestudent', $student->id,$this->Generateurl($student->fname)],
                            ['class'=>'btn btn-round btn-primary fa fa-edit','title'=>'update student details']) ?>
                    <?= $this->Form->postLink(__(' '), ['action' => 'deactivate', $student->id], ['confirm' => __('Are you sure you want to deactivate # {0}?', $student->fname),'class'=>'btn btn-round btn-danger fa fa-times','title'=>'deactivate student']) ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
                </table>
              </div>
            </div>
          </div>

        

<script>
    
    function getstudents(deptid){
      $.ajax({
        url: '../Students/getstudentsindept/'+deptid,
        method: 'GET',
        dataType: 'text',
        success: function(response) {
            console.log(response);
            document.getElementById('states1').innerHTML = "";
            document.getElementById('states1').innerHTML = response;
            //location.href = redirect;
        }
    });

    }
    
    
    </script>

