<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Paylog $paylog
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Paylogs'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="paylogs form content">
            <?= $this->Form->create($paylog) ?>
            <fieldset>
                <legend><?= __('Add Paylog') ?></legend>
                <?php
                    echo $this->Form->control('transdate');
                    echo $this->Form->control('student_id', ['options' => $students]);
                    echo $this->Form->control('tref');
                    echo $this->Form->control('responsecode');
                    echo $this->Form->control('amount');
                    echo $this->Form->control('paymethod');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
