<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Fee $fee
 * @var string[]|\Cake\Collection\CollectionInterface $users
 * @var string[]|\Cake\Collection\CollectionInterface $departments
 * @var string[]|\Cake\Collection\CollectionInterface $levels
 * @var string[]|\Cake\Collection\CollectionInterface $students
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $fee->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $fee->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Fees'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="fees form content">
            <?= $this->Form->create($fee) ?>
            <fieldset>
                <legend><?= __('Edit Fee') ?></legend>
                <?php
                    echo $this->Form->control('name');
                    echo $this->Form->control('amount');
                    echo $this->Form->control('user_id', ['options' => $users]);
                    echo $this->Form->control('status');
                    echo $this->Form->control('startdate');
                    echo $this->Form->control('enddate');
                    echo $this->Form->control('feetype');
                    echo $this->Form->control('itemcode');
                    echo $this->Form->control('departments._ids', ['options' => $departments]);
                    echo $this->Form->control('levels._ids', ['options' => $levels]);
                    echo $this->Form->control('students._ids', ['options' => $students]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
