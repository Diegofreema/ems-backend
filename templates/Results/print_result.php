<?php if(!empty($results)){ ?>
    <!-- Header Section -->
    <div class="header-section">
        <div class="header-content">
            <div class="header-left">
                <?= $this->Html->image($settings->logo, [
                    'alt' => 'School Logo', 
                    'class' => 'school-logo'
                ]) ?>
            </div>
            <div class="header-center">
                <div class="school-name"><?= h($settings->name) ?></div>
                <div class="school-details"><?= h($settings->address) ?></div>
                <div class="school-details">Tel: <?= h($settings->phone) ?> | Email: <?= h($settings->email) ?></div>
                <div class="report-title">Terminal Report Sheet</div>
            </div>
            <div class="header-right">
                <div class="student-photo">
                    <?php if (!empty($student->passporturl) && file_exists(WWW_ROOT . 'student_files' . DS . $student->passporturl)): ?>
                        <?= $this->Html->image('../student_files/' . $student->passporturl, [
                            'alt' => 'Student Photo',
                            'style' => 'width: 100%; height: 100%; object-fit: cover;'
                        ]) ?>
                    <?php else: ?>
                        <div class="photo-placeholder">
                            STUDENT<br>PHOTO
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Grading Key -->
    <div style="text-align: center; font-size: 8px; color: #666; margin-top: 2px; margin-bottom: 10px;">
        Key: A = Excellent (100 - 75) | B = Very Good (74 - 70) | C = Good (69 - 50) | D = Average (49 - 45) | E = Pass (44 - 40) | NI = Needs Improvement (39 - 0)
    </div>

    <!-- Student Information Section -->
    <div class="student-info-section">
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell">
                    <span class="info-label">Student Name:</span>
                    <span class="info-value"><?= h($student->fname . ' ' . $student->lname . ' ' . $student->mname) ?></span>
                </div>
                <div class="info-cell">
                    <span class="info-label">Registration Number:</span>
                    <span class="info-value"><?= h($student->regno) ?></span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <span class="info-label">Class:</span>
                    <span class="info-value"><?= $student->has('department') ? h($student->department->name) : '' ?><?= !empty($student->class_arm) ? ' - ' . h($student->class_arm->arm_name) : '' ?></span>
                </div>
                <div class="info-cell">
                    <span class="info-label">Term:</span>
                    <span class="info-value"><?= !empty($results) ? h($results->first()->semester->name) : '' ?></span>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <span class="info-label">Session:</span>
                    <span class="info-value"><?= !empty($results) ? h($results->first()->session->name) : '' ?></span>
                </div>
                <div class="info-cell">
                    <span class="info-label">Attendance:</span>
                    <span class="info-value">
                        <?php
                        // Get attendance data for current month
                        $attendanceTable = \Cake\ORM\TableRegistry::getTableLocator()->get('Attendances');
                        $currentMonth = date('Y-m');
                        $attendanceRecords = $attendanceTable->find()
                            ->where([
                                'student_id' => $student->id,
                                'attendance_date LIKE' => $currentMonth . '%'
                            ])
                            ->all();
                        
                        $totalDays = 0;
                        $presentDays = 0;
                        foreach ($attendanceRecords as $record) {
                            $totalDays++;
                            if ($record->status == 'Present') {
                                $presentDays++;
                            }
                        }
                        
                        $attendancePercentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0;
                        echo $attendancePercentage . '% (' . $presentDays . '/' . $totalDays . ' days)';
                        ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div class="results-section">
        <div class="section-title">Academic Performance Report</div>
        <table class="results-table">
            <thead>
                <tr>
                    <th>Subject</th>
                    <th>Homework/Project<br><small>(10%)</small></th>
                    <th>1st CA</th>
                    <th>2nd CA</th>
                    <th>Exam</th>
                    <th>Total</th>
                    <th>Grade</th>
                    <th>Remark</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $total_score = 0;
                $subjects = 0;
                $total_marks = 0;
                foreach ($results as $result):
                    $total_score += $result->total;
                    $total_marks += $result->total;
                    $subjects++;
                ?>
                    <tr>
                        <td class="subject-name"><?= $result->has('subject') ? h($result->subject->name) : '' ?></td>
                        <td><?= $this->Number->format($result->homework_project ?? 0) ?></td>
                        <td><?= $this->Number->format($result->first_ca ?? 0) ?></td>
                        <td><?= $this->Number->format($result->second_ca ?? 0) ?></td>
                        <td><?= $this->Number->format($result->score) ?></td>
                        <td class="font-bold"><?= $this->Number->format($result->total) ?></td>
                        <td>
                            <span class="grade-badge grade-<?= $result->grade ?>">
                                <?= h($result->grade) ?>
                            </span>
                        </td>
                        <td><?= h($result->remark) ?></td>
                    </tr>
                <?php endforeach; ?>
                
                <!-- Summary Row -->
                <tr class="total-row">
                    <td class="font-bold">TOTAL</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td class="font-bold"><?= $this->Number->format($total_marks) ?></td>
                    <td>
                        <?php 
                        $average = $subjects > 0 ? $total_marks / $subjects : 0;
                        $overall_grade = '';
                        if ($average >= 75) $overall_grade = 'A';
                        elseif ($average >= 70) $overall_grade = 'B';
                        elseif ($average >= 50) $overall_grade = 'C';
                        elseif ($average >= 45) $overall_grade = 'D';
                        elseif ($average >= 40) $overall_grade = 'E';
                        else $overall_grade = 'NI';
                        ?>
                        <span class="grade-badge grade-<?= $overall_grade ?>">
                            <?= $overall_grade ?>
                        </span>
                    </td>
                    <td>
                        <?php
                        if ($average >= 75) echo 'Excellent';
                        elseif ($average >= 70) echo 'Very Good';
                        elseif ($average >= 50) echo 'Good';
                        elseif ($average >= 45) echo 'Average';
                        elseif ($average >= 40) echo 'Pass';
                        else echo 'Needs Improvement';
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Average Section -->
    <div class="average-section">
        Student Term Average: <?= $subjects > 0 ? $this->Number->format($total_marks / $subjects, ['places' => 1]) : '0' ?>%
    </div>


    <!-- Comments and Stamp Section -->
    <div class="comments-stamp-section">
        <div class="comments-stamp-title">Comments & Official Stamp</div>
        <div class="comments-stamp-content">
            <div class="comments-stamp-row">
                <div class="comments-cell">
                    <div class="comment-line">
                        <span class="comment-label">Form Master's Comment:</span>
                        <span style="margin-left: 10px;">Student has shown good progress this term. Keep up the excellent work!</span>
                    </div>
                    <div class="comment-line">
                        <span class="comment-label">Principal's Comment:</span>
                        <span style="margin-left: 10px;">Outstanding performance. Continue to maintain this standard of excellence.</span>
                    </div>
                    <div class="comment-line">
                        <span class="comment-label">Name of Principal:</span>
                        <span style="margin-left: 10px;">Dr. Sarah Johnson</span>
                    </div>
                </div>
                <div class="stamp-cell">
                    <div class="stamp-box">
                        <div class="stamp-area">
                            <?php 
                            $stampPath = WWW_ROOT . 'img' . DS . 'school_stamp.png';
                            if (file_exists($stampPath)): ?>
                                <img src="<?= $this->Url->build('/img/school_stamp.png') ?>" alt="School Stamp" style="width: 100%; height: 100%; object-fit: contain;" />
                            <?php else: ?>
                                <span class="stamp-placeholder">STAMP</span>
                            <?php endif; ?>
                        </div>
                        <div class="stamp-date">Date: <?= date('d-m-Y') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
