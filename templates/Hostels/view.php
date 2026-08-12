<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Hostel $hostel
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Hostel'), ['action' => 'edit', $hostel->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Hostel'), ['action' => 'delete', $hostel->id], ['confirm' => __('Are you sure you want to delete # {0}?', $hostel->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Hostels'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Hostel'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="hostels view content">
            <h3><?= h($hostel->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($hostel->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Type') ?></th>
                    <td><?= h($hostel->type) ?></td>
                </tr>
                <tr>
                    <th><?= __('Address') ?></th>
                    <td><?= h($hostel->address) ?></td>
                </tr>
                <tr>
                    <th><?= __('Phone') ?></th>
                    <td><?= h($hostel->phone) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($hostel->id) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Hostelrooms') ?></h4>
                <?php if (!empty($hostel->hostelrooms)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Hostel Id') ?></th>
                            <th><?= __('Floor') ?></th>
                            <th><?= __('Room Number') ?></th>
                            <th><?= __('Available Beds') ?></th>
                            <th><?= __('Occupiedbeds') ?></th>
                            <th><?= __('Description') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($hostel->hostelrooms as $hostelrooms) : ?>
                        <tr>
                            <td><?= h($hostelrooms->id) ?></td>
                            <td><?= h($hostelrooms->hostel_id) ?></td>
                            <td><?= h($hostelrooms->floor) ?></td>
                            <td><?= h($hostelrooms->room_number) ?></td>
                            <td><?= h($hostelrooms->available_beds) ?></td>
                            <td><?= h($hostelrooms->occupiedbeds) ?></td>
                            <td><?= h($hostelrooms->description) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Hostelrooms', 'action' => 'view', $hostelrooms->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Hostelrooms', 'action' => 'edit', $hostelrooms->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Hostelrooms', 'action' => 'delete', $hostelrooms->id], ['confirm' => __('Are you sure you want to delete # {0}?', $hostelrooms->id)]) ?>
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
