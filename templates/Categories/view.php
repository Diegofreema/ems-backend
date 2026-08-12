<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Category $category
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Category'), ['action' => 'edit', $category->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Category'), ['action' => 'delete', $category->id], ['confirm' => __('Are you sure you want to delete # {0}?', $category->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Categories'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Category'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="categories view content">
            <h3><?= h($category->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($category->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($category->id) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Subcategory') ?></h4>
                <?php if (!empty($category->subcategory)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('S C Id') ?></th>
                            <th><?= __('Category Id') ?></th>
                            <th><?= __('Subcategory Name') ?></th>
                            <th><?= __('Subcategory Status') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($category->subcategory as $subcategory) : ?>
                        <tr>
                            <td><?= h($subcategory->s_c_id) ?></td>
                            <td><?= h($subcategory->category_id) ?></td>
                            <td><?= h($subcategory->subcategory_name) ?></td>
                            <td><?= h($subcategory->subcategory_status) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Subcategory', 'action' => 'view', $subcategory->s_c_id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Subcategory', 'action' => 'edit', $subcategory->s_c_id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Subcategory', 'action' => 'delete', $subcategory->s_c_id], ['confirm' => __('Are you sure you want to delete # {0}?', $subcategory->s_c_id)]) ?>
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
