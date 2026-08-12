<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\SponsorshipsStudent $sponsorshipsStudent
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $sponsorshipsStudent->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $sponsorshipsStudent->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Sponsorships Students'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="sponsorshipsStudents form content">
            <?= $this->Form->create($sponsorshipsStudent) ?>
            <fieldset>
                <legend><?= __('Edit Sponsorships Student') ?></legend>
                <?php
                    echo $this->Form->control('student_id', ['options' => $students]);
                    echo $this->Form->control('sponsorship_id', ['options' => $sponsorships]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
