<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Staffgrade $staffgrade
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Staffgrade'), ['action' => 'edit', $staffgrade->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Staffgrade'), ['action' => 'delete', $staffgrade->id], ['confirm' => __('Are you sure you want to delete # {0}?', $staffgrade->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Staffgrades'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Staffgrade'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="staffgrades view content">
            <h3><?= h($staffgrade->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($staffgrade->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($staffgrade->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Basicsalary') ?></th>
                    <td><?= $this->Number->format($staffgrade->basicsalary) ?></td>
                </tr>
                <tr>
                    <th><?= __('Tax') ?></th>
                    <td><?= $this->Number->format($staffgrade->tax) ?></td>
                </tr>
                <tr>
                    <th><?= __('Deduction') ?></th>
                    <td><?= $this->Number->format($staffgrade->deduction) ?></td>
                </tr>
                <tr>
                    <th><?= __('Allowance') ?></th>
                    <td><?= $this->Number->format($staffgrade->allowance) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Teachers') ?></h4>
                <?php if (!empty($staffgrade->teachers)) : ?>
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
                        <?php foreach ($staffgrade->teachers as $teachers) : ?>
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
        </div>
    </div>
</div>
