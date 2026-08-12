<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Feeallocation $feeallocation
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $feeallocation->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $feeallocation->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Feeallocations'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="feeallocations form content">
            <?= $this->Form->create($feeallocation) ?>
            <fieldset>
                <legend><?= __('Edit Feeallocation') ?></legend>
                <?php
                    echo $this->Form->control('fee_id', ['options' => $fees]);
                    echo $this->Form->control('department_id', ['options' => $departments]);
                    echo $this->Form->control('startdate');
                    echo $this->Form->control('enddate');
                    echo $this->Form->control('user_id', ['options' => $users]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
