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
            <?= $this->Html->link(__('Edit Postcategory'), ['action' => 'edit', $postcategory->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Postcategory'), ['action' => 'delete', $postcategory->id], ['confirm' => __('Are you sure you want to delete # {0}?', $postcategory->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Postcategories'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Postcategory'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="postcategories view content">
            <h3><?= h($postcategory->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($postcategory->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($postcategory->id) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Posts') ?></h4>
                <?php if (!empty($postcategory->posts)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Postcategory Id') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Allowcomments') ?></th>
                            <th><?= __('Title') ?></th>
                            <th><?= __('Postbody') ?></th>
                            <th><?= __('Status') ?></th>
                            <th><?= __('Dateadded') ?></th>
                            <th><?= __('Lastedited') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($postcategory->posts as $posts) : ?>
                        <tr>
                            <td><?= h($posts->id) ?></td>
                            <td><?= h($posts->postcategory_id) ?></td>
                            <td><?= h($posts->user_id) ?></td>
                            <td><?= h($posts->allowcomments) ?></td>
                            <td><?= h($posts->title) ?></td>
                            <td><?= h($posts->postbody) ?></td>
                            <td><?= h($posts->status) ?></td>
                            <td><?= h($posts->dateadded) ?></td>
                            <td><?= h($posts->lastedited) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Posts', 'action' => 'view', $posts->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Posts', 'action' => 'edit', $posts->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Posts', 'action' => 'delete', $posts->id], ['confirm' => __('Are you sure you want to delete # {0}?', $posts->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
