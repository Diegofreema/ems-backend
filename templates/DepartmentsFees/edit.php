<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\DepartmentsFee $departmentsFee
 * @var string[]|\Cake\Collection\CollectionInterface $fees
 * @var string[]|\Cake\Collection\CollectionInterface $departments
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $departmentsFee->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $departmentsFee->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Departments Fees'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="departmentsFees form content">
            <?= $this->Form->create($departmentsFee) ?>
            <fieldset>
                <legend><?= __('Edit Departments Fee') ?></legend>
                <?php
                    echo $this->Form->control('fee_id', ['options' => $fees]);
                    echo $this->Form->control('department_id', ['options' => $departments]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
