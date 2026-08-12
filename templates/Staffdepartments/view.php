<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Staffdepartment $staffdepartment
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Staffdepartment'), ['action' => 'edit', $staffdepartment->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Staffdepartment'), ['action' => 'delete', $staffdepartment->id], ['confirm' => __('Are you sure you want to delete # {0}?', $staffdepartment->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Staffdepartments'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Staffdepartment'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="staffdepartments view content">
            <h3><?= h($staffdepartment->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($staffdepartment->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($staffdepartment->id) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Teachers') ?></h4>
                <?php if (!empty($staffdepartment->teachers)) : ?>
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
                        <?php foreach ($staffdepartment->teachers as $teachers) : ?>
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
