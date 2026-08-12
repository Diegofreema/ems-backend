          <?= $this->Form->create(null,['url'=>['controller' => 'Students','action' => 'promotestudents']]); ?>
  <div class="p-5">
<div class="form-group row">
     <div class="col-sm-6 mb-3 mb-sm-0">
        
        </div>
<div class="col-sm-6 mb-3 mb-sm-0">
        <?php
            echo $this->Form->control('slevel_id',['options'=>$levels,'required','label'=>'Select New Level','empty'=>'Select New Level','class' => 'form-control form-control-user2']);
        ?>
        </div>
     </div>
  </div>
<!-- DataTales Example -->
          <div class="card shadow mb-4" id="students">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Promote Students</h6>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
            <tr>
           <th ><input type="checkbox" onclick="toggleAllApplicants(this);" name="parentCheck" /> </th>
                 <th scope="col"><?= $this->Paginator->sort('Name') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Department') ?></th>
                  <th>Level</th>

                <th scope="col"><?= $this->Paginator->sort('Passport') ?></th>
               
                <th scope="col"><?= $this->Paginator->sort('Regno') ?></th>
                
            </tr>
                  </thead>
            
            
              <tfoot>
            <tr>
           <th ><input type="checkbox" onclick="toggleAllApplicants(this);" name="parentCheck" /> </th>
                 <th scope="col"><?= $this->Paginator->sort('Name') ?></th>
                <th scope="col"><?= $this->Paginator->sort('Department') ?></th>
                  <th>Level</th>

                <th scope="col"><?= $this->Paginator->sort('Passport') ?></th>
               
                <th scope="col"><?= $this->Paginator->sort('Regno') ?></th>
              
            </tr>
              </tfoot>
            
      
         <tbody>
            <?php foreach ($students as $student): ?>
            <tr>
                 <td><?php 
	    echo $this->Form->checkbox('studentids[]', ['id' => $student->id,'hiddenField' => 'N','value' => $student->id]);
	    
	    ?>
                    
	     </td>
                      <td><?= h($student->fname.' '.$student->lname.' '.$student->mname) ?></td>  
               
                
                <td><?= $student->has('department') ? $this->Html->link($student->department->name, ['controller' => 'Departments', 'action' => 'view', $student->department->id]) : '' ?></td>

                <td><?=$student->level->name?></td>
                <td> <?= $this->Html->image($student->passporturl, ['alt' => 'IMG', 'class' => 'img-circle profile_img',
                                    'style' => 'width:80px;height:80px;']) ?>
               </td>
              
                <td><?= h($student->regno) ?></td>
                
            </tr>
            <?php endforeach; ?>
        </tbody>
                </table>
                   <?= $this->Form->button(' Promote ',['type' => 'submit','class'=>'btn btn-large btn-success pull-right','onclick'=>'transferEmails(this)']) ?>
                 
                  <?= $this->Form->end() ?>
              </div>
            </div>
          </div>

<script>
    
    function getstudents(deptid){
      $.ajax({
        url: '../Students/getstudentstopromote/'+deptid,
        method: 'GET',
        dataType: 'text',
        success: function(response) {
           // console.log(response);
            document.getElementById('students').innerHTML = "";
            document.getElementById('students').innerHTML = response;
            //location.href = redirect;
        }
    });

    }
    
    
    </script>


