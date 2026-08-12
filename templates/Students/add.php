<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Student $student
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Students'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="students form content">
            <?= $this->Form->create($student) ?>
            <fieldset>
                <legend><?= __('Add Student') ?></legend>
                <?php
                    echo $this->Form->control('fname');
                    echo $this->Form->control('lname');
                    echo $this->Form->control('mname');
                    echo $this->Form->control('dob');
                    echo $this->Form->control('joindate');
                    echo $this->Form->control('department_id', ['options' => $departments]);
                    echo $this->Form->control('olevelresulturl');
                    echo $this->Form->control('jamb');
                    echo $this->Form->control('birthcerturl');
                    echo $this->Form->control('othercerts');
                    echo $this->Form->control('email');
                    echo $this->Form->control('state_id', ['options' => $states]);
                    echo $this->Form->control('country_id', ['options' => $countries]);
                    echo $this->Form->control('address');
                    echo $this->Form->control('phone');
                    echo $this->Form->control('fathersname');
                    echo $this->Form->control('mothersname');
                    echo $this->Form->control('fatherphone');
                    echo $this->Form->control('motherphone');
                    echo $this->Form->control('lga_id', ['options' => $lgas, 'empty' => true]);
                    echo $this->Form->control('community');
                    echo $this->Form->control('passporturl');
                    echo $this->Form->control('user_id', ['options' => $users]);
                    echo $this->Form->control('regno');
                    echo $this->Form->control('status');
                    echo $this->Form->control('admissiondate');
                    echo $this->Form->control('gender');
                    echo $this->Form->control('application_no');
                    echo $this->Form->control('level_id', ['options' => $levels]);
                    echo $this->Form->control('faculty_id', ['options' => $faculties]);
                    echo $this->Form->control('jambregno');
                    echo $this->Form->control('previousschool');
                    echo $this->Form->control('programe_id', ['options' => $programes]);
                    echo $this->Form->control('fathersjob');
                    echo $this->Form->control('mothersjob');
                    echo $this->Form->control('studentstatus');
                    echo $this->Form->control('fees._ids', ['options' => $fees]);
                    echo $this->Form->control('hostelrooms._ids', ['options' => $hostelrooms]);
                    echo $this->Form->control('sparents._ids', ['options' => $sparents]);
                    echo $this->Form->control('subjects._ids', ['options' => $subjects]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
