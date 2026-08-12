<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Trequest $trequest
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Trequests'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="trequests form content">
            <?= $this->Form->create($trequest) ?>
            <fieldset>
                <legend><?= __('Add Trequest') ?></legend>
                <?php
                    echo $this->Form->control('student_id', ['options' => $students]);
                    echo $this->Form->control('orderdate');
                    echo $this->Form->control('institution');
                    echo $this->Form->control('status');
                    echo $this->Form->control('continent_id', ['options' => $continents]);
                    echo $this->Form->control('country_id', ['options' => $countries]);
                    echo $this->Form->control('state_id', ['options' => $states]);
                    echo $this->Form->control('address');
                    echo $this->Form->control('courier_id', ['options' => $couriers]);
                    echo $this->Form->control('amount');
                    echo $this->Form->control('deliverystatus');
                    echo $this->Form->control('fee_id', ['options' => $fees]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
