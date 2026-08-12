<?php if(!empty($results)){ ?>
    <!-- Header Section -->
    <div class="header-section">
        <div class="header-content">
            <div class="header-left">
                <?php 
                $logoPath = WWW_ROOT . 'img' . DS . $settings->logo;
                if (file_exists($logoPath)): ?>
                    <img src="<?= 'file:///' . str_replace('\\', '/', $logoPath) ?>" alt="School Logo" class="school-logo" />
                <?php else: ?>
                    <div class="school-logo" style="background-color: #f0f0f0; display: flex; align-items: center; justify-content: center; font-size: 8px; color: #666;">
                        SCHOOL<br>LOGO
                    </div>
                <?php endif; ?>
            </div>
            <div class="header-center">
                <div class="school-name"><?= h($settings->name) ?></div>
                <div class="school-details"><?= h($settings->address) ?></div>
                <div class="school-details">Tel: <?= h($settings->phone) ?> | Email: <?= h($settings->email) ?></div>
            </div>
            <div class="header-right">
                <div class="student-photo">
                    <?php 
                    $photoPath = WWW_ROOT . 'student_files' . DS . $student->passporturl;
                    if (!empty($student->passporturl) && file_exists($photoPath)): ?>
                        <img src="<?= 'file:///' . str_replace('\\', '/', $photoPath) ?>" alt="Student Photo" style="width: 100%; height: 100%; object-fit: cover;" />
                    <?php else: ?>
                        <div class="photo-placeholder">
                            STUDENT<br>PHOTO
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Information Section -->
    <div class="student-info-section">
        <div class="info-grid">
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Student Name:</div>
                    <div class="info-value"><?= h($student->fname . ' ' . $student->lname) ?></div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Registration Number:</div>
                    <div class="info-value"><?= h($student->regno) ?></div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Class:</div>
                    <div class="info-value"><?= $student->has('department') ? h($student->department->name) : '' ?><?= !empty($student->class_arm) ? ' - ' . h($student->class_arm->arm_name) : '' ?></div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Term:</div>
                    <div class="info-value"><?= !empty($results) ? h($results->first()->semester->name) : '' ?></div>
                </div>
            </div>
            <div class="info-row">
                <div class="info-cell">
                    <div class="info-label">Session:</div>
                    <div class="info-value"><?= !empty($results) ? h($results->first()->session->name) : '' ?></div>
                </div>
                <div class="info-cell">
                    <div class="info-label">Attendance:</div>
                    <div class="info-value">
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
                    </div>
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

    <!-- Grading Key Section -->
    <div class="grading-key">
        <div class="grading-key-title">Grading Key</div>
        <div class="grading-key-content">
            <div class="grade-item">
                <span class="grade-badge grade-A">A</span>
                <div>Excellent (100 - 75)</div>
            </div>
            <div class="grade-item">
                <span class="grade-badge grade-B">B</span>
                <div>Very Good (74 - 70)</div>
            </div>
            <div class="grade-item">
                <span class="grade-badge grade-C">C</span>
                <div>Good (69 - 50)</div>
            </div>
            <div class="grade-item">
                <span class="grade-badge grade-D">D</span>
                <div>Average (49 - 45)</div>
            </div>
            <div class="grade-item">
                <span class="grade-badge grade-E">E</span>
                <div>Pass (44 - 40)</div>
            </div>
            <div class="grade-item">
                <span class="grade-badge grade-NI">NI</span>
                <div>Needs Improvement (39 - 0)</div>
            </div>
        </div>
    </div>

    <!-- Comments and Stamp Section -->
    <div class="comments-stamp-section">
        <div class="comments-stamp-title">Comments & Official Stamp</div>
        <div class="comments-stamp-content">
            <div class="comments-stamp-row">
                <div class="comments-cell">
                    <div class="comment-line">
                        <span class="comment-label">Form Master's Comment:</span>
                        <span class="comment-underline"></span>
                    </div>
                    <div class="comment-line">
                        <span class="comment-label">House Master's Comment:</span>
                        <span class="comment-underline"></span>
                    </div>
                    <div class="comment-line">
                        <span class="comment-label">Guidance Counsellor's Comment:</span>
                        <span class="comment-underline"></span>
                    </div>
                    <div class="comment-line">
                        <span class="comment-label">Principal's Comment:</span>
                        <span class="comment-underline"></span>
                    </div>
                    <div class="comment-line">
                        <span class="comment-label">Name of Principal:</span>
                        <span class="comment-underline"></span>
                    </div>
                </div>
                <div class="stamp-cell">
                    <div class="stamp-box">
                        <div class="stamp-area">
                            <?php 
                            $stampPath = WWW_ROOT . 'img' . DS . 'school_stamp.png';
                            if (file_exists($stampPath)): ?>
                                <img src="<?= 'file:///' . str_replace('\\', '/', $stampPath) ?>" alt="School Stamp" style="width: 100%; height: 100%; object-fit: contain;" />
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
