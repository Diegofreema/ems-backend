<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Setting $setting
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Settings'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="settings form content">
            <?= $this->Form->create($setting) ?>
            <fieldset>
                <legend><?= __('Add Setting') ?></legend>
                <?php
                    echo $this->Form->control('semester_id', ['options' => $semesters]);
                    echo $this->Form->control('description');
                    echo $this->Form->control('regfee');
                    echo $this->Form->control('name');
                    echo $this->Form->control('address');
                    echo $this->Form->control('email');
                    echo $this->Form->control('phone');
                    echo $this->Form->control('invoiceprefix');
                    echo $this->Form->control('adminprefix');
                    echo $this->Form->control('logo');
                    echo $this->Form->control('staffprefix');
                    echo $this->Form->control('regnoformat');
                    echo $this->Form->control('session_id', ['options' => $sessions]);
                    echo $this->Form->control('application_no_prefix');
                    echo $this->Form->control('rector');
                    echo $this->Form->control('registrar');
                    echo $this->Form->control('rectorcerts');
                    echo $this->Form->control('registrarcerts');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
