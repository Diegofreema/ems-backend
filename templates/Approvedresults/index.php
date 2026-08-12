<!-- Begin Page Content -->
        <div class="container-fluid">
            <div style="padding-bottom: 10px; margin-bottom: 20px;">
          <!-- Page Heading -->
          <h1 class="h3 mb-2 text-gray-800"> Results Approval</h1></div>
            <div class="card shadow mb-4 PrintDis" id="printableArea">  <br /><br /><br />
    <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                  <thead>
            <tr>
                 
                    <th><?= $this->Paginator->sort('Session') ?></th>
                    <th><?= $this->Paginator->sort('Semester') ?></th>
                    <th><?= $this->Paginator->sort('status') ?></th>
                    <th><?= $this->Paginator->sort('Admin') ?></th>
                    <th class="actions"><?= __('Actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($approvedresults as $approvedresult): ?>
                <tr>
              
                    <td><?= $approvedresult->has('session') ? $approvedresult->session->name : '' ?></td>
                    <td><?= $approvedresult->has('semester') ? $approvedresult->semester->name : '' ?></td>
                    <td><?= h($approvedresult->status) ?></td>
                    <td><?= $approvedresult->has('admin') ? $approvedresult->admin->surname : '' ?></td>
                    <td class="actions">
                    
                        <?= $this->Html->link(__(' Edit'), ['action' => 'edit', $approvedresult->id]) ?>
                        <?= $this->Form->postLink(__('Delete'), ['action' => 'delete', $approvedresult->id], ['confirm' => __('Are you sure you want to delete # {0}?', $approvedresult->id)]) ?>
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
