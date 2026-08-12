<?php
$user = $this->request->getSession()->read('usersinfo');
?>

<!-- Page Content -->
<div class="content container-fluid">

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-sm-12">
                <h3 class="page-title">Sponsorship Manager</h3>
                <ul class="breadcrumb">
                   
                    <li class="breadcrumb-item active">Student Sponsorship Profile Details</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="column-responsive column-80">
        <div class="sponsorships view content">
          
            <table>
                <tr>
                    <th><?= __('Sponsor') ?></th>
                    <td><?= $sponsorship->has('sponsor') ? $sponsorship->sponsor->name: '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Session') ?></th>
                    <td><?= $sponsorship->has('session') ? $sponsorship->session->name : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Admin') ?></th>
                    <td><?= $sponsorship->has('admin') ? $sponsorship->admin->surname: '' ?></td>
                </tr>
                
                <tr>
                    <th><?= __('Student ReNo') ?></th>
                    <td><?= $sponsorship->student->regno ?></td>
                </tr>
                <tr>
                    <th><?= __('Datecreated') ?></th>
                    <td><?= h($sponsorship->datecreated) ?></td>
                </tr>
            </table>
           
            <div class="related">
                <h4><?= __('Related Fees') ?></h4>
                <?php if (!empty($sponsorship->fees)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                           
                            <th><?= __('Name') ?></th>
                            <th><?= __('Amount') ?></th>
                         
                       
                      
                      
                        </tr>
                        <?php foreach ($sponsorship->fees as $fees) : ?>
                        <tr>
                       
                            <td><?= h($fees->name) ?></td>
                            <td><?= h($fees->amount) ?></td>
                      
                           
                         
                            <td class="actions">
                           <!--     <?= $this->Html->link(__(' Get Receipt'), ['controller' => 'Sponsorshippayments', 'action' => 'viewreceipt', $fees->id,$sponsorship->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Sponsorshippayments', 'action' => 'edit', $fees->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Fees', 'action' => 'delete', $fees->id], ['confirm' => __('Are you sure you want to delete # {0}?', $fees->id)]) ?>
                            </td>-->
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        
        </div>
    </div>
    
</div>
