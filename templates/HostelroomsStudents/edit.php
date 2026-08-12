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
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $hostelroomsStudent->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $hostelroomsStudent->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Hostelrooms Students'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="hostelroomsStudents form content">
            <?= $this->Form->create($hostelroomsStudent) ?>
            <fieldset>
                <legend><?= __('Edit Hostelrooms Student') ?></legend>
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
