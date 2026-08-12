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
            <?= $this->Html->link(__('Edit Student'), ['action' => 'edit', $student->id], ['class' => 'side-nav-item']) ?>
            <?= $this->Form->postLink(__('Delete Student'), ['action' => 'delete', $student->id], ['confirm' => __('Are you sure you want to delete # {0}?', $student->id), 'class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('List Students'), ['action' => 'index'], ['class' => 'side-nav-item']) ?>
            <?= $this->Html->link(__('New Student'), ['action' => 'add'], ['class' => 'side-nav-item']) ?>
        </div>
    </aside>
    <div class="column-responsive column-80">
        <div class="students view content">
            <h3><?= h($student->id) ?></h3>
            <table>
                <tr>
                    <th><?= __('Fname') ?></th>
                    <td><?= h($student->fname) ?></td>
                </tr>
                <tr>
                    <th><?= __('Lname') ?></th>
                    <td><?= h($student->lname) ?></td>
                </tr>
                <tr>
                    <th><?= __('Mname') ?></th>
                    <td><?= h($student->mname) ?></td>
                </tr>
                <tr>
                    <th><?= __('Dob') ?></th>
                    <td><?= h($student->dob) ?></td>
                </tr>
                <tr>
                    <th><?= __('Department') ?></th>
                    <td><?= $student->has('department') ? $this->Html->link($student->department->name, ['controller' => 'Departments', 'action' => 'view', $student->department->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Olevelresulturl') ?></th>
                    <td><?= h($student->olevelresulturl) ?></td>
                </tr>
                <tr>
                    <th><?= __('Birthcerturl') ?></th>
                    <td><?= h($student->birthcerturl) ?></td>
                </tr>
                <tr>
                    <th><?= __('Othercerts') ?></th>
                    <td><?= h($student->othercerts) ?></td>
                </tr>
                <tr>
                    <th><?= __('Email') ?></th>
                    <td><?= h($student->email) ?></td>
                </tr>
                <tr>
                    <th><?= __('State') ?></th>
                    <td><?= $student->has('state') ? $this->Html->link($student->state->name, ['controller' => 'States', 'action' => 'view', $student->state->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Country') ?></th>
                    <td><?= $student->has('country') ? $this->Html->link($student->country->name, ['controller' => 'Countries', 'action' => 'view', $student->country->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Address') ?></th>
                    <td><?= h($student->address) ?></td>
                </tr>
                <tr>
                    <th><?= __('Phone') ?></th>
                    <td><?= h($student->phone) ?></td>
                </tr>
                <tr>
                    <th><?= __('Fathersname') ?></th>
                    <td><?= h($student->fathersname) ?></td>
                </tr>
                <tr>
                    <th><?= __('Mothersname') ?></th>
                    <td><?= h($student->mothersname) ?></td>
                </tr>
                <tr>
                    <th><?= __('Fatherphone') ?></th>
                    <td><?= h($student->fatherphone) ?></td>
                </tr>
                <tr>
                    <th><?= __('Motherphone') ?></th>
                    <td><?= h($student->motherphone) ?></td>
                </tr>
                <tr>
                    <th><?= __('Lga') ?></th>
                    <td><?= $student->has('lga') ? $this->Html->link($student->lga->name, ['controller' => 'Lgas', 'action' => 'view', $student->lga->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Community') ?></th>
                    <td><?= h($student->community) ?></td>
                </tr>
                <tr>
                    <th><?= __('Passporturl') ?></th>
                    <td><?= h($student->passporturl) ?></td>
                </tr>
                <tr>
                    <th><?= __('User') ?></th>
                    <td><?= $student->has('user') ? $this->Html->link($student->user->id, ['controller' => 'Users', 'action' => 'view', $student->user->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Regno') ?></th>
                    <td><?= h($student->regno) ?></td>
                </tr>
                <tr>
                    <th><?= __('Status') ?></th>
                    <td><?= h($student->status) ?></td>
                </tr>
                <tr>
                    <th><?= __('Admissiondate') ?></th>
                    <td><?= h($student->admissiondate) ?></td>
                </tr>
                <tr>
                    <th><?= __('Gender') ?></th>
                    <td><?= h($student->gender) ?></td>
                </tr>
                <tr>
                    <th><?= __('Application No') ?></th>
                    <td><?= h($student->application_no) ?></td>
                </tr>
                <tr>
                    <th><?= __('Level') ?></th>
                    <td><?= $student->has('level') ? $this->Html->link($student->level->name, ['controller' => 'Levels', 'action' => 'view', $student->level->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Faculty') ?></th>
                    <td><?= $student->has('faculty') ? $this->Html->link($student->faculty->name, ['controller' => 'Faculties', 'action' => 'view', $student->faculty->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Jambregno') ?></th>
                    <td><?= h($student->jambregno) ?></td>
                </tr>
                <tr>
                    <th><?= __('Previousschool') ?></th>
                    <td><?= h($student->previousschool) ?></td>
                </tr>
                <tr>
                    <th><?= __('Programe') ?></th>
                    <td><?= $student->has('programe') ? $this->Html->link($student->programe->name, ['controller' => 'Programes', 'action' => 'view', $student->programe->id]) : '' ?></td>
                </tr>
                <tr>
                    <th><?= __('Fathersjob') ?></th>
                    <td><?= h($student->fathersjob) ?></td>
                </tr>
                <tr>
                    <th><?= __('Mothersjob') ?></th>
                    <td><?= h($student->mothersjob) ?></td>
                </tr>
                <tr>
                    <th><?= __('Studentstatus') ?></th>
                    <td><?= h($student->studentstatus) ?></td>
                </tr>
                <tr>
                    <th><?= __('Id') ?></th>
                    <td><?= $this->Number->format($student->id) ?></td>
                </tr>
                <tr>
                    <th><?= __('Jamb') ?></th>
                    <td><?= $this->Number->format($student->jamb) ?></td>
                </tr>
                <tr>
                    <th><?= __('Joindate') ?></th>
                    <td><?= h($student->joindate) ?></td>
                </tr>
            </table>
            <div class="related">
                <h4><?= __('Related Fees') ?></h4>
                <?php if (!empty($student->fees)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Name') ?></th>
                            <th><?= __('Amount') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Status') ?></th>
                            <th><?= __('Startdate') ?></th>
                            <th><?= __('Enddate') ?></th>
                            <th><?= __('Feetype') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($student->fees as $fees) : ?>
                        <tr>
                            <td><?= h($fees->id) ?></td>
                            <td><?= h($fees->name) ?></td>
                            <td><?= h($fees->amount) ?></td>
                            <td><?= h($fees->user_id) ?></td>
                            <td><?= h($fees->status) ?></td>
                            <td><?= h($fees->startdate) ?></td>
                            <td><?= h($fees->enddate) ?></td>
                            <td><?= h($fees->feetype) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Fees', 'action' => 'view', $fees->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Fees', 'action' => 'edit', $fees->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Fees', 'action' => 'delete', $fees->id], ['confirm' => __('Are you sure you want to delete # {0}?', $fees->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Hostelrooms') ?></h4>
                <?php if (!empty($student->hostelrooms)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Hostel Id') ?></th>
                            <th><?= __('Floor') ?></th>
                            <th><?= __('Room Number') ?></th>
                            <th><?= __('Available Beds') ?></th>
                            <th><?= __('Occupiedbeds') ?></th>
                            <th><?= __('Description') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($student->hostelrooms as $hostelrooms) : ?>
                        <tr>
                            <td><?= h($hostelrooms->id) ?></td>
                            <td><?= h($hostelrooms->hostel_id) ?></td>
                            <td><?= h($hostelrooms->floor) ?></td>
                            <td><?= h($hostelrooms->room_number) ?></td>
                            <td><?= h($hostelrooms->available_beds) ?></td>
                            <td><?= h($hostelrooms->occupiedbeds) ?></td>
                            <td><?= h($hostelrooms->description) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Hostelrooms', 'action' => 'view', $hostelrooms->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Hostelrooms', 'action' => 'edit', $hostelrooms->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Hostelrooms', 'action' => 'delete', $hostelrooms->id], ['confirm' => __('Are you sure you want to delete # {0}?', $hostelrooms->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Sparents') ?></h4>
                <?php if (!empty($student->sparents)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Fathersname') ?></th>
                            <th><?= __('Mothersname') ?></th>
                            <th><?= __('Fatherphone') ?></th>
                            <th><?= __('Motherphone') ?></th>
                            <th><?= __('Fathersjob') ?></th>
                            <th><?= __('Mothersjob') ?></th>
                            <th><?= __('Pemailaddress') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Address') ?></th>
                            <th><?= __('Status') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($student->sparents as $sparents) : ?>
                        <tr>
                            <td><?= h($sparents->id) ?></td>
                            <td><?= h($sparents->fathersname) ?></td>
                            <td><?= h($sparents->mothersname) ?></td>
                            <td><?= h($sparents->fatherphone) ?></td>
                            <td><?= h($sparents->motherphone) ?></td>
                            <td><?= h($sparents->fathersjob) ?></td>
                            <td><?= h($sparents->mothersjob) ?></td>
                            <td><?= h($sparents->pemailaddress) ?></td>
                            <td><?= h($sparents->user_id) ?></td>
                            <td><?= h($sparents->address) ?></td>
                            <td><?= h($sparents->status) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Sparents', 'action' => 'view', $sparents->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Sparents', 'action' => 'edit', $sparents->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Sparents', 'action' => 'delete', $sparents->id], ['confirm' => __('Are you sure you want to delete # {0}?', $sparents->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Subjects') ?></h4>
                <?php if (!empty($student->subjects)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Name') ?></th>
                            <th><?= __('Subjectcode') ?></th>
                            <th><?= __('Department Id') ?></th>
                            <th><?= __('Creditload') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Created Date') ?></th>
                            <th><?= __('Status') ?></th>
                            <th><?= __('Semester Id') ?></th>
                            <th><?= __('Level Id') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($student->subjects as $subjects) : ?>
                        <tr>
                            <td><?= h($subjects->id) ?></td>
                            <td><?= h($subjects->name) ?></td>
                            <td><?= h($subjects->subjectcode) ?></td>
                            <td><?= h($subjects->department_id) ?></td>
                            <td><?= h($subjects->creditload) ?></td>
                            <td><?= h($subjects->user_id) ?></td>
                            <td><?= h($subjects->created_date) ?></td>
                            <td><?= h($subjects->status) ?></td>
                            <td><?= h($subjects->semester_id) ?></td>
                            <td><?= h($subjects->level_id) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Subjects', 'action' => 'view', $subjects->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Subjects', 'action' => 'edit', $subjects->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Subjects', 'action' => 'delete', $subjects->id], ['confirm' => __('Are you sure you want to delete # {0}?', $subjects->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Borrowedbooks') ?></h4>
                <?php if (!empty($student->borrowedbooks)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Student Id') ?></th>
                            <th><?= __('Book Id') ?></th>
                            <th><?= __('Date') ?></th>
                            <th><?= __('Datetoreturn') ?></th>
                            <th><?= __('Status') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($student->borrowedbooks as $borrowedbooks) : ?>
                        <tr>
                            <td><?= h($borrowedbooks->id) ?></td>
                            <td><?= h($borrowedbooks->student_id) ?></td>
                            <td><?= h($borrowedbooks->book_id) ?></td>
                            <td><?= h($borrowedbooks->date) ?></td>
                            <td><?= h($borrowedbooks->datetoreturn) ?></td>
                            <td><?= h($borrowedbooks->status) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Borrowedbooks', 'action' => 'view', $borrowedbooks->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Borrowedbooks', 'action' => 'edit', $borrowedbooks->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Borrowedbooks', 'action' => 'delete', $borrowedbooks->id], ['confirm' => __('Are you sure you want to delete # {0}?', $borrowedbooks->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Courseregistrations') ?></h4>
                <?php if (!empty($student->courseregistrations)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Student Id') ?></th>
                            <th><?= __('Session Id') ?></th>
                            <th><?= __('Semester Id') ?></th>
                            <th><?= __('Level Id') ?></th>
                            <th><?= __('Date Created') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($student->courseregistrations as $courseregistrations) : ?>
                        <tr>
                            <td><?= h($courseregistrations->id) ?></td>
                            <td><?= h($courseregistrations->student_id) ?></td>
                            <td><?= h($courseregistrations->session_id) ?></td>
                            <td><?= h($courseregistrations->semester_id) ?></td>
                            <td><?= h($courseregistrations->level_id) ?></td>
                            <td><?= h($courseregistrations->date_created) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Courseregistrations', 'action' => 'view', $courseregistrations->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Courseregistrations', 'action' => 'edit', $courseregistrations->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Courseregistrations', 'action' => 'delete', $courseregistrations->id], ['confirm' => __('Are you sure you want to delete # {0}?', $courseregistrations->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Invoices') ?></h4>
                <?php if (!empty($student->invoices)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Fee Id') ?></th>
                            <th><?= __('Student Id') ?></th>
                            <th><?= __('Createdate') ?></th>
                            <th><?= __('Amount') ?></th>
                            <th><?= __('Paystatus') ?></th>
                            <th><?= __('Invoiceid') ?></th>
                            <th><?= __('Session Id') ?></th>
                            <th><?= __('Payday') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($student->invoices as $invoices) : ?>
                        <tr>
                            <td><?= h($invoices->id) ?></td>
                            <td><?= h($invoices->fee_id) ?></td>
                            <td><?= h($invoices->student_id) ?></td>
                            <td><?= h($invoices->createdate) ?></td>
                            <td><?= h($invoices->amount) ?></td>
                            <td><?= h($invoices->paystatus) ?></td>
                            <td><?= h($invoices->invoiceid) ?></td>
                            <td><?= h($invoices->session_id) ?></td>
                            <td><?= h($invoices->payday) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Invoices', 'action' => 'view', $invoices->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Invoices', 'action' => 'edit', $invoices->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Invoices', 'action' => 'delete', $invoices->id], ['confirm' => __('Are you sure you want to delete # {0}?', $invoices->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Results') ?></h4>
                <?php if (!empty($student->results)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Student Id') ?></th>
                            <th><?= __('Faculty Id') ?></th>
                            <th><?= __('Department Id') ?></th>
                            <th><?= __('Subject Id') ?></th>
                            <th><?= __('Semester Id') ?></th>
                            <th><?= __('Session Id') ?></th>
                            <th><?= __('Score') ?></th>
                            <th><?= __('Grade') ?></th>
                            <th><?= __('Remark') ?></th>
                            <th><?= __('Uploaddate') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Regno') ?></th>
                            <th><?= __('Creditload') ?></th>
                            <th><?= __('Level Id') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($student->results as $results) : ?>
                        <tr>
                            <td><?= h($results->id) ?></td>
                            <td><?= h($results->student_id) ?></td>
                            <td><?= h($results->faculty_id) ?></td>
                            <td><?= h($results->department_id) ?></td>
                            <td><?= h($results->subject_id) ?></td>
                            <td><?= h($results->semester_id) ?></td>
                            <td><?= h($results->session_id) ?></td>
                            <td><?= h($results->score) ?></td>
                            <td><?= h($results->grade) ?></td>
                            <td><?= h($results->remark) ?></td>
                            <td><?= h($results->uploaddate) ?></td>
                            <td><?= h($results->user_id) ?></td>
                            <td><?= h($results->regno) ?></td>
                            <td><?= h($results->creditload) ?></td>
                            <td><?= h($results->level_id) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Results', 'action' => 'view', $results->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Results', 'action' => 'edit', $results->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Results', 'action' => 'delete', $results->id], ['confirm' => __('Are you sure you want to delete # {0}?', $results->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Studentmessages') ?></h4>
                <?php if (!empty($student->studentmessages)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Title') ?></th>
                            <th><?= __('Messages') ?></th>
                            <th><?= __('Student Id') ?></th>
                            <th><?= __('User Id') ?></th>
                            <th><?= __('Date Created') ?></th>
                            <th><?= __('Status') ?></th>
                            <th><?= __('Mfor') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($student->studentmessages as $studentmessages) : ?>
                        <tr>
                            <td><?= h($studentmessages->id) ?></td>
                            <td><?= h($studentmessages->title) ?></td>
                            <td><?= h($studentmessages->messages) ?></td>
                            <td><?= h($studentmessages->student_id) ?></td>
                            <td><?= h($studentmessages->user_id) ?></td>
                            <td><?= h($studentmessages->date_created) ?></td>
                            <td><?= h($studentmessages->status) ?></td>
                            <td><?= h($studentmessages->mfor) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Studentmessages', 'action' => 'view', $studentmessages->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Studentmessages', 'action' => 'edit', $studentmessages->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Studentmessages', 'action' => 'delete', $studentmessages->id], ['confirm' => __('Are you sure you want to delete # {0}?', $studentmessages->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Transactions') ?></h4>
                <?php if (!empty($student->transactions)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Student Id') ?></th>
                            <th><?= __('Transdate') ?></th>
                            <th><?= __('Amount') ?></th>
                            <th><?= __('Paystatus') ?></th>
                            <th><?= __('Payref') ?></th>
                            <th><?= __('Gresponse') ?></th>
                            <th><?= __('Session Id') ?></th>
                            <th><?= __('Fee Id') ?></th>
                            <th><?= __('Invoice Id') ?></th>
                            <th><?= __('Pgateway') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($student->transactions as $transactions) : ?>
                        <tr>
                            <td><?= h($transactions->id) ?></td>
                            <td><?= h($transactions->student_id) ?></td>
                            <td><?= h($transactions->transdate) ?></td>
                            <td><?= h($transactions->amount) ?></td>
                            <td><?= h($transactions->paystatus) ?></td>
                            <td><?= h($transactions->payref) ?></td>
                            <td><?= h($transactions->gresponse) ?></td>
                            <td><?= h($transactions->session_id) ?></td>
                            <td><?= h($transactions->fee_id) ?></td>
                            <td><?= h($transactions->invoice_id) ?></td>
                            <td><?= h($transactions->pgateway) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Transactions', 'action' => 'view', $transactions->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Transactions', 'action' => 'edit', $transactions->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Transactions', 'action' => 'delete', $transactions->id], ['confirm' => __('Are you sure you want to delete # {0}?', $transactions->id)]) ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                </div>
                <?php endif; ?>
            </div>
            <div class="related">
                <h4><?= __('Related Trequests') ?></h4>
                <?php if (!empty($student->trequests)) : ?>
                <div class="table-responsive">
                    <table>
                        <tr>
                            <th><?= __('Id') ?></th>
                            <th><?= __('Student Id') ?></th>
                            <th><?= __('Orderdate') ?></th>
                            <th><?= __('Institution') ?></th>
                            <th><?= __('Status') ?></th>
                            <th><?= __('Continent Id') ?></th>
                            <th><?= __('Country Id') ?></th>
                            <th><?= __('State Id') ?></th>
                            <th><?= __('Address') ?></th>
                            <th><?= __('Courier Id') ?></th>
                            <th><?= __('Amount') ?></th>
                            <th><?= __('Deliverystatus') ?></th>
                            <th><?= __('Fee Id') ?></th>
                            <th class="actions"><?= __('Actions') ?></th>
                        </tr>
                        <?php foreach ($student->trequests as $trequests) : ?>
                        <tr>
                            <td><?= h($trequests->id) ?></td>
                            <td><?= h($trequests->student_id) ?></td>
                            <td><?= h($trequests->orderdate) ?></td>
                            <td><?= h($trequests->institution) ?></td>
                            <td><?= h($trequests->status) ?></td>
                            <td><?= h($trequests->continent_id) ?></td>
                            <td><?= h($trequests->country_id) ?></td>
                            <td><?= h($trequests->state_id) ?></td>
                            <td><?= h($trequests->address) ?></td>
                            <td><?= h($trequests->courier_id) ?></td>
                            <td><?= h($trequests->amount) ?></td>
                            <td><?= h($trequests->deliverystatus) ?></td>
                            <td><?= h($trequests->fee_id) ?></td>
                            <td class="actions">
                                <?= $this->Html->link(__('View'), ['controller' => 'Trequests', 'action' => 'view', $trequests->id]) ?>
                                <?= $this->Html->link(__('Edit'), ['controller' => 'Trequests', 'action' => 'edit', $trequests->id]) ?>
                                <?= $this->Form->postLink(__('Delete'), ['controller' => 'Trequests', 'action' => 'delete', $trequests->id], ['confirm' => __('Are you sure you want to delete # {0}?', $trequests->id)]) ?>
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
