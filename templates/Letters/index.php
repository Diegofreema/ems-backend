<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Letter[]|\Cake\Collection\CollectionInterface $letters
 */
?>
<div class="letters index content">
    <?= $this->Html->link(__('New Letter'), ['action' => 'addletter'], ['class' => 'btn btn-primary float-right']) ?>
    <h3><?= __('Letters') ?></h3>
    <div class="table-responsive">
         <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                  <thead>
                <tr>
                    
                    <th><?= $this->Paginator->sort('Mode') ?></th>
                    <th><?= $this->Paginator->sort('Title') ?></th>
                   
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($letters as $letter): ?>
                <tr>
                  
                    <td><?= $letter->has('mode') ? $letter->mode->name : '' ?></td>
                    <td><?= h($letter->title) ?></td>
                  
                    <td class="actions">
                       
                        <?= $this->Html->link(__(' Edit'), ['action' => 'editletter', $letter->id,'admission-letters'],['class'=>'btn btn-success']) ?>
                        <?= $this->Form->postLink(__(' Delete'), ['action' => 'delete', $letter->id],
                                ['confirm' => __('Are you sure you want to delete # {0}?', $letter->id),'class'=>'btn btn-danger','title'=>'delete letter']) ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div>
