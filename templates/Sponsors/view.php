<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Sponsor $sponsor
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Sponsor'), ['action' => 'edit', $sponsor->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Sponsor'), ['action' => 'delete', $sponsor->id], ['confirm' => __('Are you sure you want to delete # {0}?', $sponsor->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Sponsors'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Sponsor'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="sponsors view content">
            <h3><?= h($sponsor->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($sponsor->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Phone') ?></th>
                    <td><?= h($sponsor->phone) ?></td>
                </tr>
                <tr>
                    <th><?= __('Address') ?></th>
                    <td><?= h($sponsor->address) ?></td>
                </tr>
                <tr>
                    <th><?= __('Emailaddress') ?></th>
                    <td><?= h($sponsor->emailaddress) ?></td>
                </tr>
                <tr>
                    <th><?= __('Admin') ?></th>
                    <td><?= $sponsor->has('admin') ? $this->Html->link($sponsor->admin->id, ['controller' => 'Admins', 'action' => 'view', $sponsor->admin->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($sponsor->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Dateadded') ?></th>
                    <td><?= h($sponsor->dateadded) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Sponsorships') ?></h4>
                <?php if (!empty($sponsor->sponsorships)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Sponsor Id') ?></th>
                            <th><?= __('Session Id') ?></th>
                            <th><?= __('Noofstudents') ?></th>
                            <th><?= __('Admin Id') ?></th>
                            <th><?= __('Datecreated') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($sponsor->sponsorships as $sponsorships) : ?>
                        <tr>
                            <td><?= h($sponsorships->id) ?></td>
                            <td><?= h($sponsorships->sponsor_id) ?></td>
                            <td><?= h($sponsorships->session_id) ?></td>
                            <td><?= h($sponsorships->noofstudents) ?></td>
                            <td><?= h($sponsorships->admin_id) ?></td>
                            <td><?= h($sponsorships->datecreated) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Sponsorships', 'action' => 'view', $sponsorships->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Sponsorships', 'action' => 'edit', $sponsorships->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Sponsorships', 'action' => 'delete', $sponsorships->id], ['confirm' => __('Are you sure you want to delete # {0}?', $sponsorships->id)]) ?>
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
