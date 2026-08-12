<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Setting $setting
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('Edit Setting'), ['action' => 'edit', $setting->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Setting'), ['action' => 'delete', $setting->id], ['confirm' => __('Are you sure you want to delete # {0}?', $setting->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Settings'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Setting'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="settings view content">
            <h3><?= h($setting->name) ?></h3>
            <table>
                <tr>
                    <th><?= __('Semester') ?></th>
                    <td><?= $setting->has('semester') ? $this->Html->link($setting->semester->name, ['controller' => 'Semesters', 'action' => 'view', $setting->semester->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Description') ?></th>
                    <td><?= h($setting->description) ?></td>
                </tr>
                <tr>
                    <th><?= __('Name') ?></th>
                    <td><?= h($setting->name) ?></td>
                </tr>
                <tr>
                    <th><?= __('Address') ?></th>
                    <td><?= h($setting->address) ?></td>
                </tr>
                <tr>
                    <th><?= __('Email') ?></th>
                    <td><?= h($setting->email) ?></td>
                </tr>
                <tr>
                    <th><?= __('Phone') ?></th>
                    <td><?= h($setting->phone) ?></td>
                </tr>
                <tr>
                    <th><?= __('Invoiceprefix') ?></th>
                    <td><?= h($setting->invoiceprefix) ?></td>
                </tr>
                <tr>
                    <th><?= __('Adminprefix') ?></th>
                    <td><?= h($setting->adminprefix) ?></td>
                </tr>
                <tr>
                    <th><?= __('Logo') ?></th>
                    <td><?= h($setting->logo) ?></td>
                </tr>
                <tr>
                    <th><?= __('Staffprefix') ?></th>
                    <td><?= h($setting->staffprefix) ?></td>
                </tr>
                <tr>
                    <th><?= __('Regnoformat') ?></th>
                    <td><?= h($setting->regnoformat) ?></td>
                </tr>
                <tr>
                    <th><?= __('Session') ?></th>
                    <td><?= $setting->has('session') ? $this->Html->link($setting->session->name, ['controller' => 'Sessions', 'action' => 'view', $setting->session->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Application No Prefix') ?></th>
                    <td><?= h($setting->application_no_prefix) ?></td>
                </tr>
                <tr>
                    <th><?= __('Rector') ?></th>
                    <td><?= h($setting->rector) ?></td>
                </tr>
                <tr>
                    <th><?= __('Registrar') ?></th>
                    <td><?= h($setting->registrar) ?></td>
                </tr>
                <tr>
                    <th><?= __('Rectorcerts') ?></th>
                    <td><?= h($setting->rectorcerts) ?></td>
                </tr>
                <tr>
                    <th><?= __('Registrarcerts') ?></th>
                    <td><?= h($setting->registrarcerts) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($setting->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Regfee') ?></th>
                    <td><?= $this->Number->format($setting->regfee) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
