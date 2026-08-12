<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Borrowedbook $borrowedbook
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Borrowedbooks'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="borrowedbooks form content">
            <?= $this->Form->create($borrowedbook) ?>
            <fieldset>
                <legend><?= __('Add Borrowedbook') ?></legend>
                <?php
                    echo $this->Form->control('student_id', ['options' => $students]);
                    echo $this->Form->control('book_id', ['options' => $books]);
                    echo $this->Form->control('date');
                    echo $this->Form->control('datetoreturn');
                    echo $this->Form->control('status');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
