<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Subcategory $subcategory
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Subcategory'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="subcategory form content">
            <?= $this->Form->create($subcategory) ?>
            <fieldset>
                <legend><?= __('Add Subcategory') ?></legend>
                <?php
                    echo $this->Form->control('category_id');
                    echo $this->Form->control('subcategory_name');
                    echo $this->Form->control('subcategory_status');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
