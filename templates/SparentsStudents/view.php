<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\SparentsStudent $sparentsStudent
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Sparents Student'), ['action' => 'edit', $sparentsStudent->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Sparents Student'), ['action' => 'delete', $sparentsStudent->id], ['confirm' => __('Are you sure you want to delete # {0}?', $sparentsStudent->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Sparents Students'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Sparents Student'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="sparentsStudents view content">
            <h3><?= h($sparentsStudent->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Student') ?></th>
                    <td><?= $sparentsStudent->has('student') ? $this->Html->link($sparentsStudent->student->id, ['controller' => 'Students', 'action' => 'view', $sparentsStudent->student->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Parent Sparents Student') ?></th>
                    <td><?= $sparentsStudent->has('parent_sparents_student') ? $this->Html->link($sparentsStudent->parent_sparents_student->id, ['controller' => 'SparentsStudents', 'action' => 'view', $sparentsStudent->parent_sparents_student->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($sparentsStudent->id) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Sparents Students') ?></h4>
                <?php if (!empty($sparentsStudent->child_sparents_students)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Student Id') ?></th>
                            <th><?= __('Parent Id') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($sparentsStudent->child_sparents_students as $childSparentsStudents) : ?>
                        <tr>
                            <td><?= h($childSparentsStudents->id) ?></td>
                            <td><?= h($childSparentsStudents->student_id) ?></td>
                            <td><?= h($childSparentsStudents->parent_id) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'SparentsStudents', 'action' => 'view', $childSparentsStudents->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'SparentsStudents', 'action' => 'edit', $childSparentsStudents->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'SparentsStudents', 'action' => 'delete', $childSparentsStudents->id], ['confirm' => __('Are you sure you want to delete # {0}?', $childSparentsStudents->id)]) ?>
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
