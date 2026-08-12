<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Invoice $invoice
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Invoices'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="invoices form content">
            <?= $this->Form->create($invoice) ?>
            <fieldset>
                <legend><?= __('Add Invoice') ?></legend>
                <?php
                    echo $this->Form->control('fee_id', ['options' => $fees]);
                    echo $this->Form->control('student_id', ['options' => $students]);
                    echo $this->Form->control('createdate');
                    echo $this->Form->control('amount');
                    echo $this->Form->control('paystatus');
                    echo $this->Form->control('invoiceid');
                    echo $this->Form->control('session_id', ['options' => $sessions]);
                    echo $this->Form->control('payday');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
