<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\User $user
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit User'), ['action' => 'edit', $user->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete User'), ['action' => 'delete', $user->id], ['confirm' => __('Are you sure you want to delete # {0}?', $user->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Users'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New User'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="users view content">
            <h3><?= h($user->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Username') ?></th>
                    <td><?= h($user->username) ?></td>
                </tr>
                <tr>
                    <th><?= __('Password') ?></th>
                    <td><?= h($user->password) ?></td>
                </tr>
                <tr>
                    <th><?= __('Role') ?></th>
                    <td><?= $user->has('role') ? $this->Html->link($user->role->id, ['controller' => 'Roles', 'action' => 'view', $user->role->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Fname') ?></th>
                    <td><?= h($user->fname) ?></td>
                </tr>
                <tr>
                    <th><?= __('Lname') ?></th>
                    <td><?= h($user->lname) ?></td>
                </tr>
                <tr>
                    <th><?= __('Mname') ?></th>
                    <td><?= h($user->mname) ?></td>
                </tr>
                <tr>
                    <th><?= __('Gender') ?></th>
                    <td><?= h($user->gender) ?></td>
                </tr>
                <tr>
                    <th><?= __('Address') ?></th>
                    <td><?= h($user->address) ?></td>
                </tr>
                <tr>
                    <th><?= __('Country') ?></th>
                    <td><?= $user->has('country') ? $this->Html->link($user->country->name, ['controller' => 'Countries', 'action' => 'view', $user->country->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('State') ?></th>
                    <td><?= $user->has('state') ? $this->Html->link($user->state->name, ['controller' => 'States', 'action' => 'view', $user->state->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Phone') ?></th>
                    <td><?= h($user->phone) ?></td>
                </tr>
                <tr>
                    <th><?= __('Department') ?></th>
                    <td><?= $user->has('department') ? $this->Html->link($user->department->name, ['controller' => 'Departments', 'action' => 'view', $user->department->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Dob') ?></th>
                    <td><?= h($user->dob) ?></td>
                </tr>
                <tr>
                    <th><?= __('Passport') ?></th>
                    <td><?= h($user->passport) ?></td>
                </tr>
                <tr>
                    <th><?= __('Useruniquid') ?></th>
                    <td><?= h($user->useruniquid) ?></td>
                </tr>
                <tr>
                    <th><?= __('Userstatus') ?></th>
                    <td><?= h($user->userstatus) ?></td>
                </tr>
                <tr>
                    <th><?= __('Verification Key') ?></th>
                    <td><?= h($user->verification_key) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($user->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created By') ?></th>
                    <td><?= $this->Number->format($user->created_by) ?></td>
                </tr>
                <tr>
                    <th><?= __('Created Date') ?></th>
                    <td><?= h($user->created_date) ?></td>
                </tr>
            </table>
            <div class="text">
                <strong><?= __('Profile') ?></strong>
                <blockquote>
                    <?= $this->Text->autoParagraph(h($user->profile)); ?>
                </blockquote>
            </div>
            <div class="related">
                <h4><?= __('Related Admins') ?></h4>
                <?php if (!empty($user->admins)) : ?>
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
                        <?php foreach ($user->admins as $admins) : ?>
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
                <h4><?= __('Related Admisionconditions') ?></h4>
                <?php if (!empty($user->admisionconditions)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Conditiond') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Lastupdate') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($user->admisionconditions as $admisionconditions) : ?>
                        <tr>
                            <td><?= h($admisionconditions->id) ?></td>
                            <td><?= h($admisionconditions->conditiond) ?></td>
                            <td><?= h($admisionconditions->user_id) ?></td>
                            <td><?= h($admisionconditions->lastupdate) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Admisionconditions', 'action' => 'view', $admisionconditions->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Admisionconditions', 'action' => 'edit', $admisionconditions->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Admisionconditions', 'action' => 'delete', $admisionconditions->id], ['confirm' => __('Are you sure you want to delete # {0}?', $admisionconditions->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Books') ?></h4>
                <?php if (!empty($user->books)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Title') ?></th>
                            <th><?= __('Author') ?></th>
                            <th><?= __('Pubdate') ?></th>
                            <th><?= __('Isavailable') ?></th>
                            <th><?= __('Date Created') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Isbn') ?></th>
                            <th><?= __('Coverphoto') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($user->books as $books) : ?>
                        <tr>
                            <td><?= h($books->id) ?></td>
                            <td><?= h($books->title) ?></td>
                            <td><?= h($books->author) ?></td>
                            <td><?= h($books->pubdate) ?></td>
                            <td><?= h($books->isavailable) ?></td>
                            <td><?= h($books->date_created) ?></td>
                            <td><?= h($books->user_id) ?></td>
                            <td><?= h($books->isbn) ?></td>
                            <td><?= h($books->coverphoto) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Books', 'action' => 'view', $books->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Books', 'action' => 'edit', $books->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Books', 'action' => 'delete', $books->id], ['confirm' => __('Are you sure you want to delete # {0}?', $books->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Courseassignments') ?></h4>
                <?php if (!empty($user->courseassignments)) : ?>
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
                        <?php foreach ($user->courseassignments as $courseassignments) : ?>
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
                <h4><?= __('Related Dstudents') ?></h4>
                <?php if (!empty($user->dstudents)) : ?>
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
                        <?php foreach ($user->dstudents as $dstudents) : ?>
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
                <h4><?= __('Related Feeallocations') ?></h4>
                <?php if (!empty($user->feeallocations)) : ?>
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
                        <?php foreach ($user->feeallocations as $feeallocations) : ?>
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
                <h4><?= __('Related Fees') ?></h4>
                <?php if (!empty($user->fees)) : ?>
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
                        <?php foreach ($user->fees as $fees) : ?>
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
                <h4><?= __('Related Logs') ?></h4>
                <?php if (!empty($user->logs)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Title') ?></th>
                            <th><?= __('Timestamp') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Description') ?></th>
                            <th><?= __('Ip') ?></th>
                            <th><?= __('Type') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($user->logs as $logs) : ?>
                        <tr>
                            <td><?= h($logs->id) ?></td>
                            <td><?= h($logs->title) ?></td>
                            <td><?= h($logs->timestamp) ?></td>
                            <td><?= h($logs->user_id) ?></td>
                            <td><?= h($logs->description) ?></td>
                            <td><?= h($logs->ip) ?></td>
                            <td><?= h($logs->type) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Logs', 'action' => 'view', $logs->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Logs', 'action' => 'edit', $logs->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Logs', 'action' => 'delete', $logs->id], ['confirm' => __('Are you sure you want to delete # {0}?', $logs->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related News') ?></h4>
                <?php if (!empty($user->news)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Title') ?></th>
                            <th><?= __('Details') ?></th>
                            <th><?= __('Dateposted') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Viewcount') ?></th>
                            <th><?= __('Status') ?></th>
                            <th><?= __('Newsimage') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($user->news as $news) : ?>
                        <tr>
                            <td><?= h($news->id) ?></td>
                            <td><?= h($news->title) ?></td>
                            <td><?= h($news->details) ?></td>
                            <td><?= h($news->dateposted) ?></td>
                            <td><?= h($news->user_id) ?></td>
                            <td><?= h($news->viewcount) ?></td>
                            <td><?= h($news->status) ?></td>
                            <td><?= h($news->newsimage) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'News', 'action' => 'view', $news->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'News', 'action' => 'edit', $news->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'News', 'action' => 'delete', $news->id], ['confirm' => __('Are you sure you want to delete # {0}?', $news->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Notifications') ?></h4>
                <?php if (!empty($user->notifications)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Title') ?></th>
                            <th><?= __('Message') ?></th>
                            <th><?= __('Datecreated') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Recipients') ?></th>
                            <th><?= __('Status') ?></th>
                            <th><?= __('Viewcount') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($user->notifications as $notifications) : ?>
                        <tr>
                            <td><?= h($notifications->id) ?></td>
                            <td><?= h($notifications->title) ?></td>
                            <td><?= h($notifications->message) ?></td>
                            <td><?= h($notifications->datecreated) ?></td>
                            <td><?= h($notifications->user_id) ?></td>
                            <td><?= h($notifications->recipients) ?></td>
                            <td><?= h($notifications->status) ?></td>
                            <td><?= h($notifications->viewcount) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Notifications', 'action' => 'view', $notifications->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Notifications', 'action' => 'edit', $notifications->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Notifications', 'action' => 'delete', $notifications->id], ['confirm' => __('Are you sure you want to delete # {0}?', $notifications->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Results') ?></h4>
                <?php if (!empty($user->results)) : ?>
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
                        <?php foreach ($user->results as $results) : ?>
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
                <h4><?= __('Related Sessions') ?></h4>
                <?php if (!empty($user->sessions)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Name') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Createdate') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($user->sessions as $sessions) : ?>
                        <tr>
                            <td><?= h($sessions->id) ?></td>
                            <td><?= h($sessions->name) ?></td>
                            <td><?= h($sessions->user_id) ?></td>
                            <td><?= h($sessions->createdate) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Sessions', 'action' => 'view', $sessions->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Sessions', 'action' => 'edit', $sessions->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Sessions', 'action' => 'delete', $sessions->id], ['confirm' => __('Are you sure you want to delete # {0}?', $sessions->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Sparents') ?></h4>
                <?php if (!empty($user->sparents)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Fathersname') ?></th>
                            <th><?= __('Mothersname') ?></th>
                            <th><?= __('Fatherphone') ?></th>
                            <th><?= __('Motherphone') ?></th>
                            <th><?= __('Fathersjob') ?></th>
                            <th><?= __('Mothersjob') ?></th>
                            <th><?= __('Pemailaddress') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Address') ?></th>
                            <th><?= __('Status') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($user->sparents as $sparents) : ?>
                        <tr>
                            <td><?= h($sparents->id) ?></td>
                            <td><?= h($sparents->fathersname) ?></td>
                            <td><?= h($sparents->mothersname) ?></td>
                            <td><?= h($sparents->fatherphone) ?></td>
                            <td><?= h($sparents->motherphone) ?></td>
                            <td><?= h($sparents->fathersjob) ?></td>
                            <td><?= h($sparents->mothersjob) ?></td>
                            <td><?= h($sparents->pemailaddress) ?></td>
                            <td><?= h($sparents->user_id) ?></td>
                            <td><?= h($sparents->address) ?></td>
                            <td><?= h($sparents->status) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Sparents', 'action' => 'view', $sparents->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Sparents', 'action' => 'edit', $sparents->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Sparents', 'action' => 'delete', $sparents->id], ['confirm' => __('Are you sure you want to delete # {0}?', $sparents->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Staffmessages') ?></h4>
                <?php if (!empty($user->staffmessages)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Title') ?></th>
                            <th><?= __('Message') ?></th>
                            <th><?= __('Datecreated') ?></th>
                            <th><?= __('Teacher Id') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Status') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($user->staffmessages as $staffmessages) : ?>
                        <tr>
                            <td><?= h($staffmessages->id) ?></td>
                            <td><?= h($staffmessages->title) ?></td>
                            <td><?= h($staffmessages->message) ?></td>
                            <td><?= h($staffmessages->datecreated) ?></td>
                            <td><?= h($staffmessages->teacher_id) ?></td>
                            <td><?= h($staffmessages->user_id) ?></td>
                            <td><?= h($staffmessages->status) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Staffmessages', 'action' => 'view', $staffmessages->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Staffmessages', 'action' => 'edit', $staffmessages->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Staffmessages', 'action' => 'delete', $staffmessages->id], ['confirm' => __('Are you sure you want to delete # {0}?', $staffmessages->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Studentmessages') ?></h4>
                <?php if (!empty($user->studentmessages)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Title') ?></th>
                            <th><?= __('Messages') ?></th>
                            <th><?= __('Student Id') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Date Created') ?></th>
                            <th><?= __('Status') ?></th>
                            <th><?= __('Mfor') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($user->studentmessages as $studentmessages) : ?>
                        <tr>
                            <td><?= h($studentmessages->id) ?></td>
                            <td><?= h($studentmessages->title) ?></td>
                            <td><?= h($studentmessages->messages) ?></td>
                            <td><?= h($studentmessages->student_id) ?></td>
                            <td><?= h($studentmessages->user_id) ?></td>
                            <td><?= h($studentmessages->date_created) ?></td>
                            <td><?= h($studentmessages->status) ?></td>
                            <td><?= h($studentmessages->mfor) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Studentmessages', 'action' => 'view', $studentmessages->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Studentmessages', 'action' => 'edit', $studentmessages->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Studentmessages', 'action' => 'delete', $studentmessages->id], ['confirm' => __('Are you sure you want to delete # {0}?', $studentmessages->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Students') ?></h4>
                <?php if (!empty($user->students)) : ?>
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
                        <?php foreach ($user->students as $students) : ?>
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
                <h4><?= __('Related Subjects') ?></h4>
                <?php if (!empty($user->subjects)) : ?>
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
                        <?php foreach ($user->subjects as $subjects) : ?>
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
                <h4><?= __('Related Teachers') ?></h4>
                <?php if (!empty($user->teachers)) : ?>
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
                        <?php foreach ($user->teachers as $teachers) : ?>
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
                <h4><?= __('Related Topics') ?></h4>
                <?php if (!empty($user->topics)) : ?>
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
                        <?php foreach ($user->topics as $topics) : ?>
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
            <div class="related">
                <h4><?= __('Related Userlogins') ?></h4>
                <?php if (!empty($user->userlogins)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Logintime') ?></th>
                            <th><?= __('Logouttime') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($user->userlogins as $userlogins) : ?>
                        <tr>
                            <td><?= h($userlogins->id) ?></td>
                            <td><?= h($userlogins->user_id) ?></td>
                            <td><?= h($userlogins->logintime) ?></td>
                            <td><?= h($userlogins->logouttime) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Userlogins', 'action' => 'view', $userlogins->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Userlogins', 'action' => 'edit', $userlogins->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Userlogins', 'action' => 'delete', $userlogins->id], ['confirm' => __('Are you sure you want to delete # {0}?', $userlogins->id)]) ?>
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
