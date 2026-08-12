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
            <?= $this->Html->link(__('Edit Semesters Subject'), ['action' => 'edit', $semestersSubject->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Semesters Subject'), ['action' => 'delete', $semestersSubject->id], ['confirm' => __('Are you sure you want to delete # {0}?', $semestersSubject->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Semesters Subjects'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Semesters Subject'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="semestersSubjects view content">
            <h3><?= h($semestersSubject->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Semester') ?></th>
                    <td><?= $semestersSubject->has('semester') ? $this->Html->link($semestersSubject->semester->name, ['controller' => 'Semesters', 'action' => 'view', $semestersSubject->semester->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Subject') ?></th>
                    <td><?= $semestersSubject->has('subject') ? $this->Html->link($semestersSubject->subject->name, ['controller' => 'Subjects', 'action' => 'view', $semestersSubject->subject->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($semestersSubject->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
