<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Userlogin $userlogin
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Userlogin'), ['action' => 'edit', $userlogin->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Userlogin'), ['action' => 'delete', $userlogin->id], ['confirm' => __('Are you sure you want to delete # {0}?', $userlogin->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Userlogins'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Userlogin'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="userlogins view content">
            <h3><?= h($userlogin->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('User') ?></th>
                    <td><?= $userlogin->has('user') ? $this->Html->link($userlogin->user->id, ['controller' => 'Users', 'action' => 'view', $userlogin->user->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($userlogin->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Logintime') ?></th>
                    <td><?= h($userlogin->logintime) ?></td>
                </tr>
                <tr>
                    <th><?= __('Logouttime') ?></th>
                    <td><?= h($userlogin->logouttime) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
