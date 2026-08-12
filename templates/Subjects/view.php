<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Subject $subject
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Subject'), ['action' => 'edit', $subject->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Subject'), ['action' => 'delete', $subject->id], ['confirm' => __('Are you sure you want to delete # {0}?', $subject->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Subjects'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Subject'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="subjects view content">
            <h3><?= h($subject->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($subject->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Subjectcode') ?></th>
                    <td><?= h($subject->subjectcode) ?></td>
                </tr>
                <tr>
                    <th><?= __('User') ?></th>
                    <td><?= $subject->has('user') ? $this->Html->link($subject->user->username, ['controller' => 'Users', 'action' => 'view', $subject->user->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($subject->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Department Id') ?></th>
                    <td><?= $this->Number->format($subject->department_id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Creditload') ?></th>
                    <td><?= $this->Number->format($subject->creditload) ?></td>
                </tr>
                <tr>
                    <th><?= __('Status') ?></th>
                    <td><?= $this->Number->format($subject->status) ?></td>
                </tr>
                <tr>
                    <th><?= __('Semester Id') ?></th>
                    <td><?= $this->Number->format($subject->semester_id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Level Id') ?></th>
                    <td><?= $this->Number->format($subject->level_id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created Date') ?></th>
                    <td><?= h($subject->created_date) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Departments') ?></h4>
                <?php if (!empty($subject->departments)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Faculty Id') ?></th>
                            <th><?= __('Name') ?></th>
                            <th><?= __('Deptcode') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($subject->departments as $departments) : ?>
                        <tr>
                            <td><?= h($departments->id) ?></td>
                            <td><?= h($departments->faculty_id) ?></td>
                            <td><?= h($departments->name) ?></td>
                            <td><?= h($departments->deptcode) ?></td>
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
                <h4><?= __('Related Semesters') ?></h4>
                <?php if (!empty($subject->semesters)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Name') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($subject->semesters as $semesters) : ?>
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
                <h4><?= __('Related Levels') ?></h4>
                <?php if (!empty($subject->levels)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Name') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($subject->levels as $levels) : ?>
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
                <h4><?= __('Related Courseassignments') ?></h4>
                <?php if (!empty($subject->courseassignments)) : ?>
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
                        <?php foreach ($subject->courseassignments as $courseassignments) : ?>
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
                <?php if (!empty($subject->courseregistrations)) : ?>
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
                        <?php foreach ($subject->courseregistrations as $courseregistrations) : ?>
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
                <h4><?= __('Related Students') ?></h4>
                <?php if (!empty($subject->students)) : ?>
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
                        <?php foreach ($subject->students as $students) : ?>
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
                <?php if (!empty($subject->teachers)) : ?>
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
                        <?php foreach ($subject->teachers as $teachers) : ?>
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
                <h4><?= __('Related Coursematerials') ?></h4>
                <?php if (!empty($subject->coursematerials)) : ?>
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
                        <?php foreach ($subject->coursematerials as $coursematerials) : ?>
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
                <h4><?= __('Related Results') ?></h4>
                <?php if (!empty($subject->results)) : ?>
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
                        <?php foreach ($subject->results as $results) : ?>
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
                <h4><?= __('Related Topics') ?></h4>
                <?php if (!empty($subject->topics)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Subject Id') ?></th>
                            <th><?= __('Contents') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Uploaddate') ?></th>
                            <th><?= __('Updatedon') ?></th>
                            <th><?= __('Title') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($subject->topics as $topics) : ?>
                        <tr>
                            <td><?= h($topics->id) ?></td>
                            <td><?= h($topics->subject_id) ?></td>
                            <td><?= h($topics->contents) ?></td>
                            <td><?= h($topics->user_id) ?></td>
                            <td><?= h($topics->uploaddate) ?></td>
                            <td><?= h($topics->updatedon) ?></td>
                            <td><?= h($topics->title) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Topics', 'action' => 'view', $topics->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Topics', 'action' => 'edit', $topics->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Topics', 'action' => 'delete', $topics->id], ['confirm' => __('Are you sure you want to delete # {0}?', $topics->id)]) ?>
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
