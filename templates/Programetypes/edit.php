<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Programetype $programetype
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $programetype->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $programetype->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Programetypes'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="programetypes form content">
            <?= $this->Form->create($programetype) ?>
            <fieldset>
                <legend><?= __('Edit Programetype') ?></legend>
                <?php
                    echo $this->Form->control('name');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
