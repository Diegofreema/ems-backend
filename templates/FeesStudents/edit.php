<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\FeesStudent $feesStudent
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $feesStudent->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $feesStudent->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Fees Students'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="feesStudents form content">
            <?= $this->Form->create($feesStudent) ?>
            <fieldset>
                <legend><?= __('Edit Fees Student') ?></legend>
                <?php
                    echo $this->Form->control('fee_id', ['options' => $fees]);
                    echo $this->Form->control('student_id', ['options' => $students]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
