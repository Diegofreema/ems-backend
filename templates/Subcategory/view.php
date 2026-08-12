<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Subcategory $subcategory
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Subcategory'), ['action' => 'edit', $subcategory->s_c_id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Subcategory'), ['action' => 'delete', $subcategory->s_c_id], ['confirm' => __('Are you sure you want to delete # {0}?', $subcategory->s_c_id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Subcategory'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Subcategory'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="subcategory view content">
            <h3><?= h($subcategory->s_c_id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Subcategory Name') ?></th>
                    <td><?= h($subcategory->subcategory_name) ?></td>
                </tr>
                <tr>
                    <th><?= __('S C Id') ?></th>
                    <td><?= $this->Number->format($subcategory->s_c_id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Category Id') ?></th>
                    <td><?= $this->Number->format($subcategory->category_id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Subcategory Status') ?></th>
                    <td><?= $this->Number->format($subcategory->subcategory_status) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
