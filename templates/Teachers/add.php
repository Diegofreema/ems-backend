<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Teacher $teacher
 */
?>
<div class="row">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <?= $this->Html->link(__('List Teachers'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="teachers form content">
            <?= $this->Form->create($teacher) ?>
            <fieldset>
                <legend><?= __('Add Teacher') ?></legend>
                <?php
                    echo $this->Form->control('user_id', ['options' => $users]);
                    echo $this->Form->control('gender');
                    echo $this->Form->control('address');
                    echo $this->Form->control('country_id', ['options' => $countries]);
                    echo $this->Form->control('state_id', ['options' => $states]);
                    echo $this->Form->control('phone');
                    echo $this->Form->control('profile');
                    echo $this->Form->control('cv');
                    echo $this->Form->control('qualification');
                    echo $this->Form->control('date_created');
                    echo $this->Form->control('passport');
                    echo $this->Form->control('firstname');
                    echo $this->Form->control('lastname');
                    echo $this->Form->control('middlename');
                    echo $this->Form->control('department_id', ['options' => $departments]);
                    echo $this->Form->control('staffgrade_id', ['options' => $staffgrades]);
                    echo $this->Form->control('staffdepartment_id', ['options' => $staffdepartments]);
                    echo $this->Form->control('class_arm_id', [
                        'label' => 'Class Arm Assignment (Optional)',
                        'options' => ['' => 'No Class Arm Assignment'] + $classArmsList,
                        'help' => 'Select a class arm to assign this teacher as a form teacher. Leave empty if this teacher is only a subject teacher.'
                    ]);
                    echo $this->Form->control('subjects._ids', ['options' => $subjects]);
                ?>
            </fieldset>
            <?= $this->Form->button(__('Submit')) ?>
            <?= $this->Form->end() ?>
        </div>
    </div>
</div>
