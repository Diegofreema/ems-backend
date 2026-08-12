<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\Sponsorship> $sponsorships
 */
?>
   <!-- DataTales Example -->
          <div class="card shadow mb-4">
            <div class="card-header py-3">
              <h6 class="m-0 font-weight-bold text-primary">Sponsorship Manager</h6>
           <?= $this->Html->link(__(' '), ['action' => 'addnew'],
                            ['class'=>'btn-circle btn-lg fa fa-plus float-right','title'=>'add new sponsored student']) ?>
 
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table id="myTable" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%"
                       style="margin-top: 23px;">
                  <thead>
            <tr>
                <tr>
              
                    <th><?= $this->Paginator->sort('Sponsor') ?></th>
                    <th><?= $this->Paginator->sort('Session') ?></th>
                    <th><?= $this->Paginator->sort('Student') ?></th> 
                    <th><?= $this->Paginator->sort('Regno') ?></th>
                    <th><?= $this->Paginator->sort('Admin') ?></th>
                    <th><?= $this->Paginator->sort('Date') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sponsorships as $sponsorship): ?>
                <tr>
                 
                    <td><?= $sponsorship->has('sponsor') ? $sponsorship->sponsor->name : '' ?></td>
                    <td><?= $sponsorship->has('session') ? $sponsorship->session->name : '' ?></td>
                   <td><?= $sponsorship->student->fname.' '.$sponsorship->student->lname ?></td>
                    <td><?= $sponsorship->student->regno ?></td>
                    <td><?= $sponsorship->has('admin') ? $sponsorship->admin->surname : '' ?></td>
                    <td><?= h(date('D, d M Y', strtotime($sponsorship->datecreated))) ?>
                       </td>
                    <td class="actions">
                        <?= $this->Html->link(__('View'), ['action' => 'view', $sponsorship->id,$sponsorship->student_id,'viewsp']) ?>
                        <?= $this->Html->link(__('Edit'), ['action' => 'edit', $sponsorship->id,$sponsorship->student_id,'spupdate']) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $sponsorship->id], ['confirm' => __('Are you sure you want to delete # {0}?', $sponsorship->id)]) ?>
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
</div>