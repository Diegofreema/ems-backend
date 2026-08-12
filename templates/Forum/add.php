<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Forum $forum
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Forum'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="forum form content">
            <?= $this->Form->create($forum) ?>
            <fieldset>
                <legend><?= __('Add Forum') ?></legend>
                <?php
                    echo $this->Form->control('title');
                    echo $this->Form->control('details');
                    echo $this->Form->control('user_id', ['options' => $users]);
                    echo $this->Form->control('category_id', ['options' => $categories]);
                    echo $this->Form->control('dateadded');
                    echo $this->Form->control('status');
                    echo $this->Form->control('views');
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
