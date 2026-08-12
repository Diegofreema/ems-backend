<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Trequest $trequest
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Trequest'), ['action' => 'edit', $trequest->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Trequest'), ['action' => 'delete', $trequest->id], ['confirm' => __('Are you sure you want to delete # {0}?', $trequest->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Trequests'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Trequest'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="trequests view content">
            <h3><?= h($trequest->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Student') ?></th>
                    <td><?= $trequest->has('student') ? $this->Html->link($trequest->student->id, ['controller' => 'Students', 'action' => 'view', $trequest->student->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Institution') ?></th>
                    <td><?= h($trequest->institution) ?></td>
                </tr>
                <tr>
                    <th><?= __('Status') ?></th>
                    <td><?= h($trequest->status) ?></td>
                </tr>
                <tr>
                    <th><?= __('Continent') ?></th>
                    <td><?= $trequest->has('continent') ? $this->Html->link($trequest->continent->name, ['controller' => 'Continents', 'action' => 'view', $trequest->continent->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Country') ?></th>
                    <td><?= $trequest->has('country') ? $this->Html->link($trequest->country->name, ['controller' => 'Countries', 'action' => 'view', $trequest->country->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('State') ?></th>
                    <td><?= $trequest->has('state') ? $this->Html->link($trequest->state->name, ['controller' => 'States', 'action' => 'view', $trequest->state->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Address') ?></th>
                    <td><?= h($trequest->address) ?></td>
                </tr>
                <tr>
                    <th><?= __('Courier') ?></th>
                    <td><?= $trequest->has('courier') ? $this->Html->link($trequest->courier->name, ['controller' => 'Couriers', 'action' => 'view', $trequest->courier->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Amount') ?></th>
                    <td><?= h($trequest->amount) ?></td>
                </tr>
                <tr>
                    <th><?= __('Deliverystatus') ?></th>
                    <td><?= h($trequest->deliverystatus) ?></td>
                </tr>
                <tr>
                    <th><?= __('Fee') ?></th>
                    <td><?= $trequest->has('fee') ? $this->Html->link($trequest->fee->name, ['controller' => 'Fees', 'action' => 'view', $trequest->fee->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($trequest->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Orderdate') ?></th>
                    <td><?= h($trequest->orderdate) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
