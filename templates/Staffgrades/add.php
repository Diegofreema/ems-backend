<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Staffgrade $staffgrade
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Staffgrades'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="staffgrades form content">
            <?= $this->Form->create($staffgrade) ?>
            <fieldset>
                <legend><?= __('Add Staffgrade') ?></legend>
                <?php
                    echo $this->Form->control('name');
                    echo $this->Form->control('basicsalary');
                    echo $this->Form->control('tax');
                    echo $this->Form->control('deduction');
                    echo $this->Form->control('allowance');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
