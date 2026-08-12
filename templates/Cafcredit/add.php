<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Cafcredit $cafcredit
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Cafcredit'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="cafcredit form content">
            <?= $this->Form->create($cafcredit) ?>
            <fieldset>
                <legend><?= __('Add Cafcredit') ?></legend>
                <?php
                    echo $this->Form->control('matricnum');
                    echo $this->Form->control('amount');
                    echo $this->Form->control('date1');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
