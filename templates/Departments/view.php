<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Department $department
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Department'), ['action' => 'edit', $department->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Department'), ['action' => 'delete', $department->id], ['confirm' => __('Are you sure you want to delete # {0}?', $department->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Departments'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Department'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="departments view content">
            <h3><?= h($department->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Faculty') ?></th>
                    <td><?= $department->has('faculty') ? $this->Html->link($department->faculty->name, ['controller' => 'Faculties', 'action' => 'view', $department->faculty->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($department->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Deptcode') ?></th>
                    <td><?= h($department->deptcode) ?></td>
                </tr>
                <tr>
                    <th><?= __('Description') ?></th>
                    <td><?= h($department->description) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($department->id) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Subjects') ?></h4>
                <?php if (!empty($department->subjects)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Name') ?></th>
                            <th><?= __('Subjectcode') ?></th>
                            <th><?= __('Department Id') ?></th>
                            <th><?= __('Creditload') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Created Date') ?></th>
                            <th><?= __('Status') ?></th>
                            <th><?= __('Semester Id') ?></th>
                            <th><?= __('Level Id') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($department->subjects as $subjects) : ?>
                        <tr>
                            <td><?= h($subjects->id) ?></td>
                            <td><?= h($subjects->name) ?></td>
                            <td><?= h($subjects->subjectcode) ?></td>
                            <td><?= h($subjects->department_id) ?></td>
                            <td><?= h($subjects->creditload) ?></td>
                            <td><?= h($subjects->user_id) ?></td>
                            <td><?= h($subjects->created_date) ?></td>
                            <td><?= h($subjects->status) ?></td>
                            <td><?= h($subjects->semester_id) ?></td>
                            <td><?= h($subjects->level_id) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Subjects', 'action' => 'view', $subjects->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Subjects', 'action' => 'edit', $subjects->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Subjects', 'action' => 'delete', $subjects->id], ['confirm' => __('Are you sure you want to delete # {0}?', $subjects->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Fees') ?></h4>
                <?php if (!empty($department->fees)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Name') ?></th>
                            <th><?= __('Amount') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Status') ?></th>
                            <th><?= __('Startdate') ?></th>
                            <th><?= __('Enddate') ?></th>
                            <th><?= __('Feetype') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($department->fees as $fees) : ?>
                        <tr>
                            <td><?= h($fees->id) ?></td>
                            <td><?= h($fees->name) ?></td>
                            <td><?= h($fees->amount) ?></td>
                            <td><?= h($fees->user_id) ?></td>
                            <td><?= h($fees->status) ?></td>
                            <td><?= h($fees->startdate) ?></td>
                            <td><?= h($fees->enddate) ?></td>
                            <td><?= h($fees->feetype) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Fees', 'action' => 'view', $fees->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Fees', 'action' => 'edit', $fees->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Fees', 'action' => 'delete', $fees->id], ['confirm' => __('Are you sure you want to delete # {0}?', $fees->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Levels') ?></h4>
                <?php if (!empty($department->levels)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Name') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($department->levels as $levels) : ?>
                        <tr>
                            <td><?= h($levels->id) ?></td>
                            <td><?= h($levels->name) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Levels', 'action' => 'view', $levels->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Levels', 'action' => 'edit', $levels->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Levels', 'action' => 'delete', $levels->id], ['confirm' => __('Are you sure you want to delete # {0}?', $levels->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Programes') ?></h4>
                <?php if (!empty($department->programes)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Name') ?></th>
                            <th><?= __('Programecode') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($department->programes as $programes) : ?>
                        <tr>
                            <td><?= h($programes->id) ?></td>
                            <td><?= h($programes->name) ?></td>
                            <td><?= h($programes->programecode) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Programes', 'action' => 'view', $programes->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Programes', 'action' => 'edit', $programes->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Programes', 'action' => 'delete', $programes->id], ['confirm' => __('Are you sure you want to delete # {0}?', $programes->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Semesters') ?></h4>
                <?php if (!empty($department->semesters)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Name') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($department->semesters as $semesters) : ?>
                        <tr>
                            <td><?= h($semesters->id) ?></td>
                            <td><?= h($semesters->name) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Semesters', 'action' => 'view', $semesters->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Semesters', 'action' => 'edit', $semesters->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Semesters', 'action' => 'delete', $semesters->id], ['confirm' => __('Are you sure you want to delete # {0}?', $semesters->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Admins') ?></h4>
                <?php if (!empty($department->admins)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Surname') ?></th>
                            <th><?= __('Lastname') ?></th>
                            <th><?= __('Status') ?></th>
                            <th><?= __('Date Created') ?></th>
                            <th><?= __('Adminphoto') ?></th>
                            <th><?= __('Gender') ?></th>
                            <th><?= __('Department Id') ?></th>
                            <th><?= __('Phone') ?></th>
                            <th><?= __('Address') ?></th>
                            <th><?= __('Dob') ?></th>
                            <th><?= __('Profile') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($department->admins as $admins) : ?>
                        <tr>
                            <td><?= h($admins->id) ?></td>
                            <td><?= h($admins->user_id) ?></td>
                            <td><?= h($admins->surname) ?></td>
                            <td><?= h($admins->lastname) ?></td>
                            <td><?= h($admins->status) ?></td>
                            <td><?= h($admins->date_created) ?></td>
                            <td><?= h($admins->adminphoto) ?></td>
                            <td><?= h($admins->gender) ?></td>
                            <td><?= h($admins->department_id) ?></td>
                            <td><?= h($admins->phone) ?></td>
                            <td><?= h($admins->address) ?></td>
                            <td><?= h($admins->dob) ?></td>
                            <td><?= h($admins->profile) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Admins', 'action' => 'view', $admins->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Admins', 'action' => 'edit', $admins->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Admins', 'action' => 'delete', $admins->id], ['confirm' => __('Are you sure you want to delete # {0}?', $admins->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Courseassignments') ?></h4>
                <?php if (!empty($department->courseassignments)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Department Id') ?></th>
                            <th><?= __('Semester Id') ?></th>
                            <th><?= __('Level Id') ?></th>
                            <th><?= __('Assignedon') ?></th>
                            <th><?= __('Updatedon') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($department->courseassignments as $courseassignments) : ?>
                        <tr>
                            <td><?= h($courseassignments->id) ?></td>
                            <td><?= h($courseassignments->department_id) ?></td>
                            <td><?= h($courseassignments->semester_id) ?></td>
                            <td><?= h($courseassignments->level_id) ?></td>
                            <td><?= h($courseassignments->assignedon) ?></td>
                            <td><?= h($courseassignments->updatedon) ?></td>
                            <td><?= h($courseassignments->user_id) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Courseassignments', 'action' => 'view', $courseassignments->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Courseassignments', 'action' => 'edit', $courseassignments->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Courseassignments', 'action' => 'delete', $courseassignments->id], ['confirm' => __('Are you sure you want to delete # {0}?', $courseassignments->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Coursematerials') ?></h4>
                <?php if (!empty($department->coursematerials)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Subject Id') ?></th>
                            <th><?= __('Teacher Id') ?></th>
                            <th><?= __('Title') ?></th>
                            <th><?= __('Fileurl') ?></th>
                            <th><?= __('Date Created') ?></th>
                            <th><?= __('Department Id') ?></th>
                            <th><?= __('Comment') ?></th>
                            <th><?= __('Updatedon') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($department->coursematerials as $coursematerials) : ?>
                        <tr>
                            <td><?= h($coursematerials->id) ?></td>
                            <td><?= h($coursematerials->subject_id) ?></td>
                            <td><?= h($coursematerials->teacher_id) ?></td>
                            <td><?= h($coursematerials->title) ?></td>
                            <td><?= h($coursematerials->fileurl) ?></td>
                            <td><?= h($coursematerials->date_created) ?></td>
                            <td><?= h($coursematerials->department_id) ?></td>
                            <td><?= h($coursematerials->comment) ?></td>
                            <td><?= h($coursematerials->updatedon) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Coursematerials', 'action' => 'view', $coursematerials->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Coursematerials', 'action' => 'edit', $coursematerials->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Coursematerials', 'action' => 'delete', $coursematerials->id], ['confirm' => __('Are you sure you want to delete # {0}?', $coursematerials->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Dstudents') ?></h4>
                <?php if (!empty($department->dstudents)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Fname') ?></th>
                            <th><?= __('Lname') ?></th>
                            <th><?= __('Mname') ?></th>
                            <th><?= __('Dob') ?></th>
                            <th><?= __('Address') ?></th>
                            <th><?= __('State Id') ?></th>
                            <th><?= __('Country Id') ?></th>
                            <th><?= __('Phone') ?></th>
                            <th><?= __('Email') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Department Id') ?></th>
                            <th><?= __('Olevelcerturl') ?></th>
                            <th><?= __('Jambscore') ?></th>
                            <th><?= __('Birthcerturl') ?></th>
                            <th><?= __('Otherfile') ?></th>
                            <th><?= __('Fathersname') ?></th>
                            <th><?= __('Fathersphone') ?></th>
                            <th><?= __('Mothersname') ?></th>
                            <th><?= __('Mothersphone') ?></th>
                            <th><?= __('Fathersjob') ?></th>
                            <th><?= __('Mothersjob') ?></th>
                            <th><?= __('Appliedon') ?></th>
                            <th><?= __('Status') ?></th>
                            <th><?= __('Passporturl') ?></th>
                            <th><?= __('Regno') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($department->dstudents as $dstudents) : ?>
                        <tr>
                            <td><?= h($dstudents->id) ?></td>
                            <td><?= h($dstudents->fname) ?></td>
                            <td><?= h($dstudents->lname) ?></td>
                            <td><?= h($dstudents->mname) ?></td>
                            <td><?= h($dstudents->dob) ?></td>
                            <td><?= h($dstudents->address) ?></td>
                            <td><?= h($dstudents->state_id) ?></td>
                            <td><?= h($dstudents->country_id) ?></td>
                            <td><?= h($dstudents->phone) ?></td>
                            <td><?= h($dstudents->email) ?></td>
                            <td><?= h($dstudents->user_id) ?></td>
                            <td><?= h($dstudents->department_id) ?></td>
                            <td><?= h($dstudents->olevelcerturl) ?></td>
                            <td><?= h($dstudents->jambscore) ?></td>
                            <td><?= h($dstudents->birthcerturl) ?></td>
                            <td><?= h($dstudents->otherfile) ?></td>
                            <td><?= h($dstudents->fathersname) ?></td>
                            <td><?= h($dstudents->fathersphone) ?></td>
                            <td><?= h($dstudents->mothersname) ?></td>
                            <td><?= h($dstudents->mothersphone) ?></td>
                            <td><?= h($dstudents->fathersjob) ?></td>
                            <td><?= h($dstudents->mothersjob) ?></td>
                            <td><?= h($dstudents->appliedon) ?></td>
                            <td><?= h($dstudents->status) ?></td>
                            <td><?= h($dstudents->passporturl) ?></td>
                            <td><?= h($dstudents->regno) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Dstudents', 'action' => 'view', $dstudents->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Dstudents', 'action' => 'edit', $dstudents->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Dstudents', 'action' => 'delete', $dstudents->id], ['confirm' => __('Are you sure you want to delete # {0}?', $dstudents->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Examquestions') ?></h4>
                <?php if (!empty($department->examquestions)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Subject Id') ?></th>
                            <th><?= __('Question') ?></th>
                            <th><?= __('Op1') ?></th>
                            <th><?= __('Op2') ?></th>
                            <th><?= __('Op3') ?></th>
                            <th><?= __('Op4') ?></th>
                            <th><?= __('Correctans') ?></th>
                            <th><?= __('Mark') ?></th>
                            <th><?= __('Dateadded') ?></th>
                            <th><?= __('Admin Id') ?></th>
                            <th><?= __('Exam Id') ?></th>
                            <th><?= __('Department Id') ?></th>
                            <th><?= __('Level Id') ?></th>
                            <th><?= __('Faculty Id') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($department->examquestions as $examquestions) : ?>
                        <tr>
                            <td><?= h($examquestions->id) ?></td>
                            <td><?= h($examquestions->subject_id) ?></td>
                            <td><?= h($examquestions->question) ?></td>
                            <td><?= h($examquestions->op1) ?></td>
                            <td><?= h($examquestions->op2) ?></td>
                            <td><?= h($examquestions->op3) ?></td>
                            <td><?= h($examquestions->op4) ?></td>
                            <td><?= h($examquestions->correctans) ?></td>
                            <td><?= h($examquestions->mark) ?></td>
                            <td><?= h($examquestions->dateadded) ?></td>
                            <td><?= h($examquestions->admin_id) ?></td>
                            <td><?= h($examquestions->exam_id) ?></td>
                            <td><?= h($examquestions->department_id) ?></td>
                            <td><?= h($examquestions->level_id) ?></td>
                            <td><?= h($examquestions->faculty_id) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Examquestions', 'action' => 'view', $examquestions->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Examquestions', 'action' => 'edit', $examquestions->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Examquestions', 'action' => 'delete', $examquestions->id], ['confirm' => __('Are you sure you want to delete # {0}?', $examquestions->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Feeallocations') ?></h4>
                <?php if (!empty($department->feeallocations)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Fee Id') ?></th>
                            <th><?= __('Department Id') ?></th>
                            <th><?= __('Startdate') ?></th>
                            <th><?= __('Enddate') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($department->feeallocations as $feeallocations) : ?>
                        <tr>
                            <td><?= h($feeallocations->id) ?></td>
                            <td><?= h($feeallocations->fee_id) ?></td>
                            <td><?= h($feeallocations->department_id) ?></td>
                            <td><?= h($feeallocations->startdate) ?></td>
                            <td><?= h($feeallocations->enddate) ?></td>
                            <td><?= h($feeallocations->user_id) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Feeallocations', 'action' => 'view', $feeallocations->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Feeallocations', 'action' => 'edit', $feeallocations->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Feeallocations', 'action' => 'delete', $feeallocations->id], ['confirm' => __('Are you sure you want to delete # {0}?', $feeallocations->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Quizzes') ?></h4>
                <?php if (!empty($department->quizzes)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Faculty Id') ?></th>
                            <th><?= __('Department Id') ?></th>
                            <th><?= __('Semester Id') ?></th>
                            <th><?= __('Session Id') ?></th>
                            <th><?= __('Subject Id') ?></th>
                            <th><?= __('Quizname') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($department->quizzes as $quizzes) : ?>
                        <tr>
                            <td><?= h($quizzes->id) ?></td>
                            <td><?= h($quizzes->faculty_id) ?></td>
                            <td><?= h($quizzes->department_id) ?></td>
                            <td><?= h($quizzes->semester_id) ?></td>
                            <td><?= h($quizzes->session_id) ?></td>
                            <td><?= h($quizzes->subject_id) ?></td>
                            <td><?= h($quizzes->quizname) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Quizzes', 'action' => 'view', $quizzes->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Quizzes', 'action' => 'edit', $quizzes->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Quizzes', 'action' => 'delete', $quizzes->id], ['confirm' => __('Are you sure you want to delete # {0}?', $quizzes->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Results') ?></h4>
                <?php if (!empty($department->results)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Student Id') ?></th>
                            <th><?= __('Faculty Id') ?></th>
                            <th><?= __('Department Id') ?></th>
                            <th><?= __('Subject Id') ?></th>
                            <th><?= __('Semester Id') ?></th>
                            <th><?= __('Session Id') ?></th>
                            <th><?= __('Score') ?></th>
                            <th><?= __('Grade') ?></th>
                            <th><?= __('Remark') ?></th>
                            <th><?= __('Uploaddate') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Regno') ?></th>
                            <th><?= __('Creditload') ?></th>
                            <th><?= __('Level Id') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($department->results as $results) : ?>
                        <tr>
                            <td><?= h($results->id) ?></td>
                            <td><?= h($results->student_id) ?></td>
                            <td><?= h($results->faculty_id) ?></td>
                            <td><?= h($results->department_id) ?></td>
                            <td><?= h($results->subject_id) ?></td>
                            <td><?= h($results->semester_id) ?></td>
                            <td><?= h($results->session_id) ?></td>
                            <td><?= h($results->score) ?></td>
                            <td><?= h($results->grade) ?></td>
                            <td><?= h($results->remark) ?></td>
                            <td><?= h($results->uploaddate) ?></td>
                            <td><?= h($results->user_id) ?></td>
                            <td><?= h($results->regno) ?></td>
                            <td><?= h($results->creditload) ?></td>
                            <td><?= h($results->level_id) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Results', 'action' => 'view', $results->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Results', 'action' => 'edit', $results->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Results', 'action' => 'delete', $results->id], ['confirm' => __('Are you sure you want to delete # {0}?', $results->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Students') ?></h4>
                <?php if (!empty($department->students)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Fname') ?></th>
                            <th><?= __('Lname') ?></th>
                            <th><?= __('Mname') ?></th>
                            <th><?= __('Dob') ?></th>
                            <th><?= __('Joindate') ?></th>
                            <th><?= __('Department Id') ?></th>
                            <th><?= __('Olevelresulturl') ?></th>
                            <th><?= __('Jamb') ?></th>
                            <th><?= __('Birthcerturl') ?></th>
                            <th><?= __('Othercerts') ?></th>
                            <th><?= __('Email') ?></th>
                            <th><?= __('State Id') ?></th>
                            <th><?= __('Country Id') ?></th>
                            <th><?= __('Address') ?></th>
                            <th><?= __('Phone') ?></th>
                            <th><?= __('Fathersname') ?></th>
                            <th><?= __('Mothersname') ?></th>
                            <th><?= __('Fatherphone') ?></th>
                            <th><?= __('Motherphone') ?></th>
                            <th><?= __('Lga Id') ?></th>
                            <th><?= __('Community') ?></th>
                            <th><?= __('Passporturl') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Regno') ?></th>
                            <th><?= __('Status') ?></th>
                            <th><?= __('Admissiondate') ?></th>
                            <th><?= __('Gender') ?></th>
                            <th><?= __('Application No') ?></th>
                            <th><?= __('Level Id') ?></th>
                            <th><?= __('Faculty Id') ?></th>
                            <th><?= __('Jambregno') ?></th>
                            <th><?= __('Previousschool') ?></th>
                            <th><?= __('Programe Id') ?></th>
                            <th><?= __('Fathersjob') ?></th>
                            <th><?= __('Mothersjob') ?></th>
                            <th><?= __('Studentstatus') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($department->students as $students) : ?>
                        <tr>
                            <td><?= h($students->id) ?></td>
                            <td><?= h($students->fname) ?></td>
                            <td><?= h($students->lname) ?></td>
                            <td><?= h($students->mname) ?></td>
                            <td><?= h($students->dob) ?></td>
                            <td><?= h($students->joindate) ?></td>
                            <td><?= h($students->department_id) ?></td>
                            <td><?= h($students->olevelresulturl) ?></td>
                            <td><?= h($students->jamb) ?></td>
                            <td><?= h($students->birthcerturl) ?></td>
                            <td><?= h($students->othercerts) ?></td>
                            <td><?= h($students->email) ?></td>
                            <td><?= h($students->state_id) ?></td>
                            <td><?= h($students->country_id) ?></td>
                            <td><?= h($students->address) ?></td>
                            <td><?= h($students->phone) ?></td>
                            <td><?= h($students->fathersname) ?></td>
                            <td><?= h($students->mothersname) ?></td>
                            <td><?= h($students->fatherphone) ?></td>
                            <td><?= h($students->motherphone) ?></td>
                            <td><?= h($students->lga_id) ?></td>
                            <td><?= h($students->community) ?></td>
                            <td><?= h($students->passporturl) ?></td>
                            <td><?= h($students->user_id) ?></td>
                            <td><?= h($students->regno) ?></td>
                            <td><?= h($students->status) ?></td>
                            <td><?= h($students->admissiondate) ?></td>
                            <td><?= h($students->gender) ?></td>
                            <td><?= h($students->application_no) ?></td>
                            <td><?= h($students->level_id) ?></td>
                            <td><?= h($students->faculty_id) ?></td>
                            <td><?= h($students->jambregno) ?></td>
                            <td><?= h($students->previousschool) ?></td>
                            <td><?= h($students->programe_id) ?></td>
                            <td><?= h($students->fathersjob) ?></td>
                            <td><?= h($students->mothersjob) ?></td>
                            <td><?= h($students->studentstatus) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Students', 'action' => 'view', $students->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Students', 'action' => 'edit', $students->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Students', 'action' => 'delete', $students->id], ['confirm' => __('Are you sure you want to delete # {0}?', $students->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Teachers') ?></h4>
                <?php if (!empty($department->teachers)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Gender') ?></th>
                            <th><?= __('Address') ?></th>
                            <th><?= __('Country Id') ?></th>
                            <th><?= __('State Id') ?></th>
                            <th><?= __('Phone') ?></th>
                            <th><?= __('Profile') ?></th>
                            <th><?= __('Cv') ?></th>
                            <th><?= __('Qualification') ?></th>
                            <th><?= __('Date Created') ?></th>
                            <th><?= __('Passport') ?></th>
                            <th><?= __('Firstname') ?></th>
                            <th><?= __('Lastname') ?></th>
                            <th><?= __('Middlename') ?></th>
                            <th><?= __('Department Id') ?></th>
                            <th><?= __('Staffgrade Id') ?></th>
                            <th><?= __('Staffdepartment Id') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($department->teachers as $teachers) : ?>
                        <tr>
                            <td><?= h($teachers->id) ?></td>
                            <td><?= h($teachers->user_id) ?></td>
                            <td><?= h($teachers->gender) ?></td>
                            <td><?= h($teachers->address) ?></td>
                            <td><?= h($teachers->country_id) ?></td>
                            <td><?= h($teachers->state_id) ?></td>
                            <td><?= h($teachers->phone) ?></td>
                            <td><?= h($teachers->profile) ?></td>
                            <td><?= h($teachers->cv) ?></td>
                            <td><?= h($teachers->qualification) ?></td>
                            <td><?= h($teachers->date_created) ?></td>
                            <td><?= h($teachers->passport) ?></td>
                            <td><?= h($teachers->firstname) ?></td>
                            <td><?= h($teachers->lastname) ?></td>
                            <td><?= h($teachers->middlename) ?></td>
                            <td><?= h($teachers->department_id) ?></td>
                            <td><?= h($teachers->staffgrade_id) ?></td>
                            <td><?= h($teachers->staffdepartment_id) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Teachers', 'action' => 'view', $teachers->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Teachers', 'action' => 'edit', $teachers->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Teachers', 'action' => 'delete', $teachers->id], ['confirm' => __('Are you sure you want to delete # {0}?', $teachers->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Users') ?></h4>
                <?php if (!empty($department->users)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Username') ?></th>
                            <th><?= __('Password') ?></th>
                            <th><?= __('Role Id') ?></th>
                            <th><?= __('Fname') ?></th>
                            <th><?= __('Lname') ?></th>
                            <th><?= __('Mname') ?></th>
                            <th><?= __('Gender') ?></th>
                            <th><?= __('Address') ?></th>
                            <th><?= __('Country Id') ?></th>
                            <th><?= __('State Id') ?></th>
                            <th><?= __('Phone') ?></th>
                            <th><?= __('Department Id') ?></th>
                            <th><?= __('Profile') ?></th>
                            <th><?= __('Dob') ?></th>
                            <th><?= __('Created Date') ?></th>
                            <th><?= __('Created By') ?></th>
                            <th><?= __('Passport') ?></th>
                            <th><?= __('Useruniquid') ?></th>
                            <th><?= __('Userstatus') ?></th>
                            <th><?= __('Verification Key') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($department->users as $users) : ?>
                        <tr>
                            <td><?= h($users->id) ?></td>
                            <td><?= h($users->username) ?></td>
                            <td><?= h($users->password) ?></td>
                            <td><?= h($users->role_id) ?></td>
                            <td><?= h($users->fname) ?></td>
                            <td><?= h($users->lname) ?></td>
                            <td><?= h($users->mname) ?></td>
                            <td><?= h($users->gender) ?></td>
                            <td><?= h($users->address) ?></td>
                            <td><?= h($users->country_id) ?></td>
                            <td><?= h($users->state_id) ?></td>
                            <td><?= h($users->phone) ?></td>
                            <td><?= h($users->department_id) ?></td>
                            <td><?= h($users->profile) ?></td>
                            <td><?= h($users->dob) ?></td>
                            <td><?= h($users->created_date) ?></td>
                            <td><?= h($users->created_by) ?></td>
                            <td><?= h($users->passport) ?></td>
                            <td><?= h($users->useruniquid) ?></td>
                            <td><?= h($users->userstatus) ?></td>
                            <td><?= h($users->verification_key) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Users', 'action' => 'view', $users->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Users', 'action' => 'edit', $users->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Users', 'action' => 'delete', $users->id], ['confirm' => __('Are you sure you want to delete # {0}?', $users->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
