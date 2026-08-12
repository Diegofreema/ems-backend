<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Semester $semester
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Semester'), ['action' => 'edit', $semester->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Semester'), ['action' => 'delete', $semester->id], ['confirm' => __('Are you sure you want to delete # {0}?', $semester->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Semesters'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Semester'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="semesters view content">
            <h3><?= h($semester->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($semester->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($semester->id) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Subjects') ?></h4>
                <?php if (!empty($semester->subjects)) : ?>
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
                        <?php foreach ($semester->subjects as $subjects) : ?>
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
                <h4><?= __('Related Departments') ?></h4>
                <?php if (!empty($semester->departments)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Faculty Id') ?></th>
                            <th><?= __('Name') ?></th>
                            <th><?= __('Deptcode') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($semester->departments as $departments) : ?>
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
                <h4><?= __('Related Courseassignments') ?></h4>
                <?php if (!empty($semester->courseassignments)) : ?>
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
                        <?php foreach ($semester->courseassignments as $courseassignments) : ?>
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
                <?php if (!empty($semester->courseregistrations)) : ?>
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
                        <?php foreach ($semester->courseregistrations as $courseregistrations) : ?>
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
                <?php if (!empty($semester->results)) : ?>
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
                        <?php foreach ($semester->results as $results) : ?>
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
                <h4><?= __('Related Settings') ?></h4>
                <?php if (!empty($semester->settings)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Semester Id') ?></th>
                            <th><?= __('Description') ?></th>
                            <th><?= __('Regfee') ?></th>
                            <th><?= __('Name') ?></th>
                            <th><?= __('Address') ?></th>
                            <th><?= __('Email') ?></th>
                            <th><?= __('Phone') ?></th>
                            <th><?= __('Invoiceprefix') ?></th>
                            <th><?= __('Adminprefix') ?></th>
                            <th><?= __('Logo') ?></th>
                            <th><?= __('Staffprefix') ?></th>
                            <th><?= __('Regnoformat') ?></th>
                            <th><?= __('Session Id') ?></th>
                            <th><?= __('Application No Prefix') ?></th>
                            <th><?= __('Rector') ?></th>
                            <th><?= __('Registrar') ?></th>
                            <th><?= __('Rectorcerts') ?></th>
                            <th><?= __('Registrarcerts') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($semester->settings as $settings) : ?>
                        <tr>
                            <td><?= h($settings->id) ?></td>
                            <td><?= h($settings->semester_id) ?></td>
                            <td><?= h($settings->description) ?></td>
                            <td><?= h($settings->regfee) ?></td>
                            <td><?= h($settings->name) ?></td>
                            <td><?= h($settings->address) ?></td>
                            <td><?= h($settings->email) ?></td>
                            <td><?= h($settings->phone) ?></td>
                            <td><?= h($settings->invoiceprefix) ?></td>
                            <td><?= h($settings->adminprefix) ?></td>
                            <td><?= h($settings->logo) ?></td>
                            <td><?= h($settings->staffprefix) ?></td>
                            <td><?= h($settings->regnoformat) ?></td>
                            <td><?= h($settings->session_id) ?></td>
                            <td><?= h($settings->application_no_prefix) ?></td>
                            <td><?= h($settings->rector) ?></td>
                            <td><?= h($settings->registrar) ?></td>
                            <td><?= h($settings->rectorcerts) ?></td>
                            <td><?= h($settings->registrarcerts) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Settings', 'action' => 'view', $settings->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Settings', 'action' => 'edit', $settings->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Settings', 'action' => 'delete', $settings->id], ['confirm' => __('Are you sure you want to delete # {0}?', $settings->id)]) ?>
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
