<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\HostelroomsStudent $hostelroomsStudent
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Hostelrooms Students'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="hostelroomsStudents form content">
            <?= $this->Form->create($hostelroomsStudent) ?>
            <fieldset>
                <legend><?= __('Add Hostelrooms Student') ?></legend>
                <?php
                    echo $this->Form->control('hostelroom_id', ['options' => $hostelrooms]);
                    echo $this->Form->control('student_id', ['options' => $students]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
