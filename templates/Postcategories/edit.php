<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Postcategory $postcategory
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Form->postLink(
                __('Delete'),
                ['action' => 'delete', $postcategory->id],
                ['confirm' => __('Are you sure you want to delete # {0}?', $postcategory->id), 'class' => 'side-nav-item']
            ) ?>
            <?= $this->Html->link(__('List Postcategories'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="postcategories form content">
            <?= $this->Form->create($postcategory) ?>
            <fieldset>
                <legend><?= __('Edit Postcategory') ?></legend>
                <?php
                    echo $this->Form->control('name');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
