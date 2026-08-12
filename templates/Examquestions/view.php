<!-- Page Content -->
<div class="content container-fluid">
    <?php $settings = $this->request->getSession()->read('settings') ?>
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Exams Questions</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item">
                        <?= $this->Html->link(' Dashboard', ['controller' => 'Admins', 'action' => 'dashboard'], ['title' => 'admins dashboard']) ?>
                    </li>
                    <li class="breadcrumb-item active">Exam Questions Manager - <?=  $exam->exam->examname ?></li>
                </ul>
                Session : <?=  $exam->exam->session->name ?><br />
                Semester : <?=  $exam->exam->semester->name ?><br />
                Department : <?=  $exam->department->name ?><br />
                Level : <?=  $exam->level->name ?><br />
            </div>
            <div class="col-auto float-right ml-auto">
                <a href="#" class="btn add-btn" data-toggle="modal" data-target="#add_leave"><i class="fa fa-plus"></i> Add Questions</a>
            </div>
        </div>
    </div>
    <!-- /Page Header -->
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped custom-table mb-0 datatable">
                    <thead>
                        <tr>
                            <th>Question</th>
                            <th>Option A</th>
                            <th>Option B</th>
                            <th>Option C</th>
                            <th>Option D</th>
                            <th>Answer</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($examquestions as $examquestion): ?>
                            <tr>
                             
                                <td><?= $examquestion->question ?></td>
                                <td><?= $examquestion->op1 ?></td>
                                <td><?= $examquestion->op2?></td>
                                <td><?= $examquestion->op3 ?></td>
                                <td><?= $examquestion->op4?></td>
                                <td><?= $examquestion->correctans ?></td>
               
                                <td class="text-right">
                                    <div class="dropdown dropdown-action">
                                        <a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
                                        <div class="dropdown-menu dropdown-menu-right">
                                           
                                            <?= $this->Form->postLink(__(' Delete'), ['controller' => 'Examquestions', 'action' => 'delete', $examquestion->id], ['confirm' => __('Are you sure you want to delete # {0}?', $examquestion->question), 'class' => 'dropdown-item fa fa-trash-o m-r-5', 'title' => 'delete question'])
                                            ?>

                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>


                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
