<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\SemestersSubject $semestersSubject
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $semestersSubject->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $semestersSubject->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Semesters Subjects'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="semestersSubjects form content">
            <?= $this->Form->create($semestersSubject) ?>
            <fieldset>
                <legend><?= __('Edit Semesters Subject') ?></legend>
                <?php
                    echo $this->Form->control('semester_id', ['options' => $semesters]);
                    echo $this->Form->control('subject_id', ['options' => $subjects]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
