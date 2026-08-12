<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Book $book
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Book'), ['action' => 'edit', $book->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Book'), ['action' => 'delete', $book->id], ['confirm' => __('Are you sure you want to delete # {0}?', $book->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Books'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Book'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="books view content">
            <h3><?= h($book->title) ?></h3>
            <table>
                <tr>
                    <th><?= __('Title') ?></th>
                    <td><?= h($book->title) ?></td>
                </tr>
                <tr>
                    <th><?= __('Author') ?></th>
                    <td><?= h($book->author) ?></td>
                </tr>
                <tr>
                    <th><?= __('Pubdate') ?></th>
                    <td><?= h($book->pubdate) ?></td>
                </tr>
                <tr>
                    <th><?= __('Isavailable') ?></th>
                    <td><?= h($book->isavailable) ?></td>
                </tr>
                <tr>
                    <th><?= __('User') ?></th>
                    <td><?= $book->has('user') ? $this->Html->link($book->user->username, ['controller' => 'Users', 'action' => 'view', $book->user->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Isbn') ?></th>
                    <td><?= h($book->isbn) ?></td>
                </tr>
                <tr>
                    <th><?= __('Coverphoto') ?></th>
                    <td><?= h($book->coverphoto) ?></td>
                </tr>
                <tr>
                    <th><?= __('Section') ?></th>
                    <td><?= h($book->section) ?></td>
                </tr>
                <tr>
                    <th><?= __('Callno') ?></th>
                    <td><?= h($book->callno) ?></td>
                </tr>
                <tr>
                    <th><?= __('Department') ?></th>
                    <td><?= $book->has('department') ? $this->Html->link($book->department->name, ['controller' => 'Departments', 'action' => 'view', $book->department->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($book->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Copies') ?></th>
                    <td><?= $book->copies === null ? '' : $this->Number->format($book->copies) ?></td>
                </tr>
                <tr>
                    <th><?= __('Date Created') ?></th>
                    <td><?= h($book->date_created) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Borrowedbooks') ?></h4>
                <?php if (!empty($book->borrowedbooks)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Student Id') ?></th>
                            <th><?= __('Book Id') ?></th>
                            <th><?= __('Date') ?></th>
                            <th><?= __('Datetoreturn') ?></th>
                            <th><?= __('Status') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($book->borrowedbooks as $borrowedbooks) : ?>
                        <tr>
                            <td><?= h($borrowedbooks->id) ?></td>
                            <td><?= h($borrowedbooks->student_id) ?></td>
                            <td><?= h($borrowedbooks->book_id) ?></td>
                            <td><?= h($borrowedbooks->date) ?></td>
                            <td><?= h($borrowedbooks->datetoreturn) ?></td>
                            <td><?= h($borrowedbooks->status) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Borrowedbooks', 'action' => 'view', $borrowedbooks->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Borrowedbooks', 'action' => 'edit', $borrowedbooks->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Borrowedbooks', 'action' => 'delete', $borrowedbooks->id], ['confirm' => __('Are you sure you want to delete # {0}?', $borrowedbooks->id)]) ?>
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
