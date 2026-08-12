<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Studentmessage $studentmessage
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Studentmessages'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="studentmessages form content">
            <?= $this->Form->create($studentmessage) ?>
            <fieldset>
                <legend><?= __('Add Studentmessage') ?></legend>
                <?php
                    echo $this->Form->control('title');
                    echo $this->Form->control('messages');
                    echo $this->Form->control('student_id', ['options' => $students]);
                    echo $this->Form->control('user_id', ['options' => $users]);
                    echo $this->Form->control('date_created');
                    echo $this->Form->control('status');
                    echo $this->Form->control('mfor');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
