<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Candidate $candidate
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Candidate'), ['action' => 'edit', $candidate->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Candidate'), ['action' => 'delete', $candidate->id], ['confirm' => __('Are you sure you want to delete # {0}?', $candidate->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Candidates'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Candidate'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="candidates view content">
            <h3><?= h($candidate->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Student') ?></th>
                    <td><?= $candidate->has('student') ? $this->Html->link($candidate->student->regno, ['controller' => 'Students', 'action' => 'view', $candidate->student->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Position') ?></th>
                    <td><?= $candidate->has('position') ? $this->Html->link($candidate->position->name, ['controller' => 'Positions', 'action' => 'view', $candidate->position->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Session') ?></th>
                    <td><?= $candidate->has('session') ? $this->Html->link($candidate->session->name, ['controller' => 'Sessions', 'action' => 'view', $candidate->session->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($candidate->id) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Votes') ?></h4>
                <?php if (!empty($candidate->votes)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Candidate Id') ?></th>
                            <th><?= __('Student Id') ?></th>
                            <th><?= __('Vote') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($candidate->votes as $votes) : ?>
                        <tr>
                            <td><?= h($votes->id) ?></td>
                            <td><?= h($votes->candidate_id) ?></td>
                            <td><?= h($votes->student_id) ?></td>
                            <td><?= h($votes->vote) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Votes', 'action' => 'view', $votes->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Votes', 'action' => 'edit', $votes->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Votes', 'action' => 'delete', $votes->id], ['confirm' => __('Are you sure you want to delete # {0}?', $votes->id)]) ?>
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
