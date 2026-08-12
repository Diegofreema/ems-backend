<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\DepartmentsFee $departmentsFee
 * @var \Cake\Collection\CollectionInterface|string[] $fees
 * @var \Cake\Collection\CollectionInterface|string[] $departments
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Departments Fees'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="departmentsFees form content">
            <?= $this->Form->create($departmentsFee) ?>
            <fieldset>
                <legend><?= __('Add Departments Fee') ?></legend>
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
