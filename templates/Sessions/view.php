<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Session $session
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Session'), ['action' => 'edit', $session->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Session'), ['action' => 'delete', $session->id], ['confirm' => __('Are you sure you want to delete # {0}?', $session->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Sessions'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Session'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="sessions view content">
            <h3><?= h($session->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($session->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('User') ?></th>
                    <td><?= $session->has('user') ? $this->Html->link($session->user->id, ['controller' => 'Users', 'action' => 'view', $session->user->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($session->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Createdate') ?></th>
                    <td><?= h($session->createdate) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Courseregistrations') ?></h4>
                <?php if (!empty($session->courseregistrations)) : ?>
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
                        <?php foreach ($session->courseregistrations as $courseregistrations) : ?>
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
                <h4><?= __('Related Invoices') ?></h4>
                <?php if (!empty($session->invoices)) : ?>
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
                        <?php foreach ($session->invoices as $invoices) : ?>
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
                <h4><?= __('Related Results') ?></h4>
                <?php if (!empty($session->results)) : ?>
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
                        <?php foreach ($session->results as $results) : ?>
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
                <?php if (!empty($session->settings)) : ?>
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
                        <?php foreach ($session->settings as $settings) : ?>
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
            <div class="related">
                <h4><?= __('Related Transactions') ?></h4>
                <?php if (!empty($session->transactions)) : ?>
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
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($session->transactions as $transactions) : ?>
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
        </div>
    </div>
</div>
