<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Level $level
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Level'), ['action' => 'edit', $level->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Level'), ['action' => 'delete', $level->id], ['confirm' => __('Are you sure you want to delete # {0}?', $level->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Levels'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Level'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="levels view content">
            <h3><?= h($level->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($level->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($level->id) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Departments') ?></h4>
                <?php if (!empty($level->departments)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Faculty Id') ?></th>
                            <th><?= __('Name') ?></th>
                            <th><?= __('Deptcode') ?></th>
                            <th><?= __('Iscdl') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($level->departments as $departments) : ?>
                        <tr>
                            <td><?= h($departments->id) ?></td>
                            <td><?= h($departments->faculty_id) ?></td>
                            <td><?= h($departments->name) ?></td>
                            <td><?= h($departments->deptcode) ?></td>
                            <td><?= h($departments->iscdl) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Departments', 'action' => 'view', $departments->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Departments', 'action' => 'edit', $departments->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Departments', 'action' => 'delete', $departments->id], ['confirm' => __('Are you sure you want to delete # {0}?', $departments->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Fees') ?></h4>
                <?php if (!empty($level->fees)) : ?>
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
                            <th><?= __('Itemcode') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($level->fees as $fees) : ?>
                        <tr>
                            <td><?= h($fees->id) ?></td>
                            <td><?= h($fees->name) ?></td>
                            <td><?= h($fees->amount) ?></td>
                            <td><?= h($fees->user_id) ?></td>
                            <td><?= h($fees->status) ?></td>
                            <td><?= h($fees->startdate) ?></td>
                            <td><?= h($fees->enddate) ?></td>
                            <td><?= h($fees->feetype) ?></td>
                            <td><?= h($fees->itemcode) ?></td>
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
                <h4><?= __('Related Courseassignments') ?></h4>
                <?php if (!empty($level->courseassignments)) : ?>
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
                        <?php foreach ($level->courseassignments as $courseassignments) : ?>
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
                <h4><?= __('Related Courseregistrations') ?></h4>
                <?php if (!empty($level->courseregistrations)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Student Id') ?></th>
                            <th><?= __('Session Id') ?></th>
                            <th><?= __('Semester Id') ?></th>
                            <th><?= __('Level Id') ?></th>
                            <th><?= __('Date Created') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($level->courseregistrations as $courseregistrations) : ?>
                        <tr>
                            <td><?= h($courseregistrations->id) ?></td>
                            <td><?= h($courseregistrations->student_id) ?></td>
                            <td><?= h($courseregistrations->session_id) ?></td>
                            <td><?= h($courseregistrations->semester_id) ?></td>
                            <td><?= h($courseregistrations->level_id) ?></td>
                            <td><?= h($courseregistrations->date_created) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Courseregistrations', 'action' => 'view', $courseregistrations->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Courseregistrations', 'action' => 'edit', $courseregistrations->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Courseregistrations', 'action' => 'delete', $courseregistrations->id], ['confirm' => __('Are you sure you want to delete # {0}?', $courseregistrations->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Results') ?></h4>
                <?php if (!empty($level->results)) : ?>
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
                            <th><?= __('Ca') ?></th>
                            <th><?= __('Total') ?></th>
                            <th><?= __('Iscarryover') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($level->results as $results) : ?>
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
                            <td><?= h($results->ca) ?></td>
                            <td><?= h($results->total) ?></td>
                            <td><?= h($results->iscarryover) ?></td>
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
                <?php if (!empty($level->students)) : ?>
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
                            <th><?= __('Jamb Notification') ?></th>
                            <th><?= __('Jambresult') ?></th>
                            <th><?= __('Jamb Admin Letter') ?></th>
                            <th><?= __('Status') ?></th>
                            <th><?= __('Admissiondate') ?></th>
                            <th><?= __('Gender') ?></th>
                            <th><?= __('Application No') ?></th>
                            <th><?= __('Level Id') ?></th>
                            <th><?= __('Faculty Id') ?></th>
                            <th><?= __('Jambregno') ?></th>
                            <th><?= __('Previousschool') ?></th>
                            <th><?= __('Programme Id') ?></th>
                            <th><?= __('Fathersjob') ?></th>
                            <th><?= __('Mothersjob') ?></th>
                            <th><?= __('Studentstatus') ?></th>
                            <th><?= __('Mode Id') ?></th>
                            <th><?= __('Universitymail') ?></th>
                            <th><?= __('Category Id') ?></th>
                            <th><?= __('Programetype Id') ?></th>
                            <th><?= __('Duration Id') ?></th>
                            <th><?= __('Landlocation') ?></th>
                            <th><?= __('Landsize') ?></th>
                            <th><?= __('Landowner') ?></th>
                            <th><?= __('Landaccessurl') ?></th>
                            <th><?= __('Session Id') ?></th>
                            <th><?= __('Isclaretian') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($level->students as $students) : ?>
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
                            <td><?= h($students->jamb_notification) ?></td>
                            <td><?= h($students->jambresult) ?></td>
                            <td><?= h($students->jamb_admin_letter) ?></td>
                            <td><?= h($students->status) ?></td>
                            <td><?= h($students->admissiondate) ?></td>
                            <td><?= h($students->gender) ?></td>
                            <td><?= h($students->application_no) ?></td>
                            <td><?= h($students->level_id) ?></td>
                            <td><?= h($students->faculty_id) ?></td>
                            <td><?= h($students->jambregno) ?></td>
                            <td><?= h($students->previousschool) ?></td>
                            <td><?= h($students->programme_id) ?></td>
                            <td><?= h($students->fathersjob) ?></td>
                            <td><?= h($students->mothersjob) ?></td>
                            <td><?= h($students->studentstatus) ?></td>
                            <td><?= h($students->mode_id) ?></td>
                            <td><?= h($students->universitymail) ?></td>
                            <td><?= h($students->category_id) ?></td>
                            <td><?= h($students->programetype_id) ?></td>
                            <td><?= h($students->duration_id) ?></td>
                            <td><?= h($students->landlocation) ?></td>
                            <td><?= h($students->landsize) ?></td>
                            <td><?= h($students->landowner) ?></td>
                            <td><?= h($students->landaccessurl) ?></td>
                            <td><?= h($students->session_id) ?></td>
                            <td><?= h($students->isclaretian) ?></td>
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
                <h4><?= __('Related Subjects') ?></h4>
                <?php if (!empty($level->subjects)) : ?>
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
                        <?php foreach ($level->subjects as $subjects) : ?>
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
                <h4><?= __('Related Timetables') ?></h4>
                <?php if (!empty($level->timetables)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Semester Id') ?></th>
                            <th><?= __('Level Id') ?></th>
                            <th><?= __('Department Id') ?></th>
                            <th><?= __('Session Id') ?></th>
                            <th><?= __('Timetable') ?></th>
                            <th><?= __('Dateadded') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($level->timetables as $timetables) : ?>
                        <tr>
                            <td><?= h($timetables->id) ?></td>
                            <td><?= h($timetables->semester_id) ?></td>
                            <td><?= h($timetables->level_id) ?></td>
                            <td><?= h($timetables->department_id) ?></td>
                            <td><?= h($timetables->session_id) ?></td>
                            <td><?= h($timetables->timetable) ?></td>
                            <td><?= h($timetables->dateadded) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Timetables', 'action' => 'view', $timetables->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Timetables', 'action' => 'edit', $timetables->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Timetables', 'action' => 'delete', $timetables->id], ['confirm' => __('Are you sure you want to delete # {0}?', $timetables->id)]) ?>
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
