<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Fee $fee
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Fee'), ['action' => 'edit', $fee->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Fee'), ['action' => 'delete', $fee->id], ['confirm' => __('Are you sure you want to delete # {0}?', $fee->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Fees'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Fee'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="fees view content">
            <h3><?= h($fee->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($fee->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('User') ?></th>
                    <td><?= $fee->has('user') ? $this->Html->link($fee->user->username, ['controller' => 'Users', 'action' => 'view', $fee->user->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Startdate') ?></th>
                    <td><?= h($fee->startdate) ?></td>
                </tr>
                <tr>
                    <th><?= __('Enddate') ?></th>
                    <td><?= h($fee->enddate) ?></td>
                </tr>
                <tr>
                    <th><?= __('Feetype') ?></th>
                    <td><?= h($fee->feetype) ?></td>
                </tr>
                <tr>
                    <th><?= __('Itemcode') ?></th>
                    <td><?= h($fee->itemcode) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($fee->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Amount') ?></th>
                    <td><?= $this->Number->format($fee->amount) ?></td>
                </tr>
                <tr>
                    <th><?= __('Status') ?></th>
                    <td><?= $this->Number->format($fee->status) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Departments') ?></h4>
                <?php if (!empty($fee->departments)) : ?>
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
                        <?php foreach ($fee->departments as $departments) : ?>
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
                <h4><?= __('Related Levels') ?></h4>
                <?php if (!empty($fee->levels)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Name') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($fee->levels as $levels) : ?>
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
                <h4><?= __('Related Students') ?></h4>
                <?php if (!empty($fee->students)) : ?>
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
                        <?php foreach ($fee->students as $students) : ?>
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
                <h4><?= __('Related Feeallocations') ?></h4>
                <?php if (!empty($fee->feeallocations)) : ?>
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
                        <?php foreach ($fee->feeallocations as $feeallocations) : ?>
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
                <h4><?= __('Related Invoices') ?></h4>
                <?php if (!empty($fee->invoices)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Fee Id') ?></th>
                            <th><?= __('Student Id') ?></th>
                            <th><?= __('Createdate') ?></th>
                            <th><?= __('Amount') ?></th>
                            <th><?= __('Paystatus') ?></th>
                            <th><?= __('Invoiceid') ?></th>
                            <th><?= __('Session Id') ?></th>
                            <th><?= __('Payday') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($fee->invoices as $invoices) : ?>
                        <tr>
                            <td><?= h($invoices->id) ?></td>
                            <td><?= h($invoices->fee_id) ?></td>
                            <td><?= h($invoices->student_id) ?></td>
                            <td><?= h($invoices->createdate) ?></td>
                            <td><?= h($invoices->amount) ?></td>
                            <td><?= h($invoices->paystatus) ?></td>
                            <td><?= h($invoices->invoiceid) ?></td>
                            <td><?= h($invoices->session_id) ?></td>
                            <td><?= h($invoices->payday) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Invoices', 'action' => 'view', $invoices->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Invoices', 'action' => 'edit', $invoices->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Invoices', 'action' => 'delete', $invoices->id], ['confirm' => __('Are you sure you want to delete # {0}?', $invoices->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Transactions') ?></h4>
                <?php if (!empty($fee->transactions)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Student Id') ?></th>
                            <th><?= __('Transdate') ?></th>
                            <th><?= __('Amount') ?></th>
                            <th><?= __('Paystatus') ?></th>
                            <th><?= __('Payref') ?></th>
                            <th><?= __('Gresponse') ?></th>
                            <th><?= __('Session Id') ?></th>
                            <th><?= __('Fee Id') ?></th>
                            <th><?= __('Invoice Id') ?></th>
                            <th><?= __('Pgateway') ?></th>
                            <th><?= __('Paymentlogid') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($fee->transactions as $transactions) : ?>
                        <tr>
                            <td><?= h($transactions->id) ?></td>
                            <td><?= h($transactions->student_id) ?></td>
                            <td><?= h($transactions->transdate) ?></td>
                            <td><?= h($transactions->amount) ?></td>
                            <td><?= h($transactions->paystatus) ?></td>
                            <td><?= h($transactions->payref) ?></td>
                            <td><?= h($transactions->gresponse) ?></td>
                            <td><?= h($transactions->session_id) ?></td>
                            <td><?= h($transactions->fee_id) ?></td>
                            <td><?= h($transactions->invoice_id) ?></td>
                            <td><?= h($transactions->pgateway) ?></td>
                            <td><?= h($transactions->paymentlogid) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Transactions', 'action' => 'view', $transactions->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Transactions', 'action' => 'edit', $transactions->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Transactions', 'action' => 'delete', $transactions->id], ['confirm' => __('Are you sure you want to delete # {0}?', $transactions->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Trequests') ?></h4>
                <?php if (!empty($fee->trequests)) : ?>
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
                        <?php foreach ($fee->trequests as $trequests) : ?>
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
        </div>
    </div>
</div>
