<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Country $country
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Country'), ['action' => 'edit', $country->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Country'), ['action' => 'delete', $country->id], ['confirm' => __('Are you sure you want to delete # {0}?', $country->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Countries'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Country'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="countries view content">
            <h3><?= h($country->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Sortname') ?></th>
                    <td><?= h($country->sortname) ?></td>
                </tr>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($country->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($country->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Phonecode') ?></th>
                    <td><?= $this->Number->format($country->phonecode) ?></td>
                </tr>
                <tr>
                    <th><?= __('Cost') ?></th>
                    <td><?= $this->Number->format($country->cost) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Dstudents') ?></h4>
                <?php if (!empty($country->dstudents)) : ?>
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
                        <?php foreach ($country->dstudents as $dstudents) : ?>
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
                <h4><?= __('Related States') ?></h4>
                <?php if (!empty($country->states)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Name') ?></th>
                            <th><?= __('Country Id') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($country->states as $states) : ?>
                        <tr>
                            <td><?= h($states->id) ?></td>
                            <td><?= h($states->name) ?></td>
                            <td><?= h($states->country_id) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'States', 'action' => 'view', $states->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'States', 'action' => 'edit', $states->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'States', 'action' => 'delete', $states->id], ['confirm' => __('Are you sure you want to delete # {0}?', $states->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Students') ?></h4>
                <?php if (!empty($country->students)) : ?>
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
                        <?php foreach ($country->students as $students) : ?>
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
                <?php if (!empty($country->teachers)) : ?>
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
                        <?php foreach ($country->teachers as $teachers) : ?>
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
                <h4><?= __('Related Trequests') ?></h4>
                <?php if (!empty($country->trequests)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Student Id') ?></th>
                            <th><?= __('Orderdate') ?></th>
                            <th><?= __('Institution') ?></th>
                            <th><?= __('Status') ?></th>
                            <th><?= __('Continent Id') ?></th>
                            <th><?= __('Country Id') ?></th>
                            <th><?= __('State Id') ?></th>
                            <th><?= __('Address') ?></th>
                            <th><?= __('Courier Id') ?></th>
                            <th><?= __('Amount') ?></th>
                            <th><?= __('Deliverystatus') ?></th>
                            <th><?= __('Fee Id') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($country->trequests as $trequests) : ?>
                        <tr>
                            <td><?= h($trequests->id) ?></td>
                            <td><?= h($trequests->student_id) ?></td>
                            <td><?= h($trequests->orderdate) ?></td>
                            <td><?= h($trequests->institution) ?></td>
                            <td><?= h($trequests->status) ?></td>
                            <td><?= h($trequests->continent_id) ?></td>
                            <td><?= h($trequests->country_id) ?></td>
                            <td><?= h($trequests->state_id) ?></td>
                            <td><?= h($trequests->address) ?></td>
                            <td><?= h($trequests->courier_id) ?></td>
                            <td><?= h($trequests->amount) ?></td>
                            <td><?= h($trequests->deliverystatus) ?></td>
                            <td><?= h($trequests->fee_id) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Trequests', 'action' => 'view', $trequests->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Trequests', 'action' => 'edit', $trequests->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Trequests', 'action' => 'delete', $trequests->id], ['confirm' => __('Are you sure you want to delete # {0}?', $trequests->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Users') ?></h4>
                <?php if (!empty($country->users)) : ?>
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
                        <?php foreach ($country->users as $users) : ?>
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
