<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Courier $courier
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Courier'), ['action' => 'edit', $courier->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Courier'), ['action' => 'delete', $courier->id], ['confirm' => __('Are you sure you want to delete # {0}?', $courier->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Couriers'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Courier'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="couriers view content">
            <h3><?= h($courier->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($courier->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($courier->id) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Trequests') ?></h4>
                <?php if (!empty($courier->trequests)) : ?>
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
                        <?php foreach ($courier->trequests as $trequests) : ?>
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
