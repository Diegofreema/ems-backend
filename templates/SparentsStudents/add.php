<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\SparentsStudent $sparentsStudent
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Sparents Students'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="sparentsStudents form content">
            <?= $this->Form->create($sparentsStudent) ?>
            <fieldset>
                <legend><?= __('Add Sparents Student') ?></legend>
                <?php
                    echo $this->Form->control('student_id', ['options' => $students]);
                    echo $this->Form->control('parent_id', ['options' => $parentSparentsStudents]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
