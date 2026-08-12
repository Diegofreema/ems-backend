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
            <?= $this->Html->link(__('Edit Sponsorships Student'), ['action' => 'edit', $sponsorshipsStudent->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Sponsorships Student'), ['action' => 'delete', $sponsorshipsStudent->id], ['confirm' => __('Are you sure you want to delete # {0}?', $sponsorshipsStudent->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Sponsorships Students'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Sponsorships Student'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="sponsorshipsStudents view content">
            <h3><?= h($sponsorshipsStudent->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Student') ?></th>
                    <td><?= $sponsorshipsStudent->has('student') ? $this->Html->link($sponsorshipsStudent->student->fname, ['controller' => 'Students', 'action' => 'view', $sponsorshipsStudent->student->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Sponsorship') ?></th>
                    <td><?= $sponsorshipsStudent->has('sponsorship') ? $this->Html->link($sponsorshipsStudent->sponsorship->id, ['controller' => 'Sponsorships', 'action' => 'view', $sponsorshipsStudent->sponsorship->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($sponsorshipsStudent->id) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
