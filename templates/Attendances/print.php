<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report - <?= h($teacher->department->name ?? 'Department') ?> - <?= date('M j, Y', strtotime($date)) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .header h2 {
            margin: 5px 0;
            color: #666;
        }
        .info {
            margin-bottom: 20px;
        }
        .info p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .status-present { color: #28a745; font-weight: bold; }
        .status-absent { color: #dc3545; font-weight: bold; }
        .status-late { color: #ffc107; font-weight: bold; }
        .status-excused { color: #17a2b8; font-weight: bold; }
        .summary {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
        }
        .summary h3 {
            margin-top: 0;
        }
        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            margin: 40px 0;
        }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Attendance Report</h1>
        <h2><?= h($teacher->department->name ?? 'Department') ?></h2>
        <p>Date: <?= date('l, F j, Y', strtotime($date)) ?></p>
    </div>

    <div class="info">
        <p><strong>Teacher:</strong> <?= h($teacher->firstname . ' ' . $teacher->lastname) ?></p>
        <p><strong>Department:</strong> <?= h($teacher->department->name ?? 'Not specified') ?></p>
        <p><strong>Report Date:</strong> <?= date('M j, Y', strtotime($date)) ?></p>
        <p><strong>Generated:</strong> <?= date('M j, Y, g:i A') ?></p>
    </div>

    <?php if (empty($attendance)): ?>
        <div class="no-data">
            <h3>No Attendance Recorded</h3>
            <p>No attendance has been recorded for this date.</p>
        </div>
    <?php else: ?>
        <!-- Attendance Summary -->
        <?php
        $presentCount = 0;
        $absentCount = 0;
        $lateCount = 0;
        $excusedCount = 0;
        
        foreach ($attendance as $record) {
            switch ($record->status) {
                case 'present':
                    $presentCount++;
                    break;
                case 'absent':
                    $absentCount++;
                    break;
                case 'late':
                    $lateCount++;
                    break;
                case 'excused':
                    $excusedCount++;
                    break;
            }
        }
        
        $totalStudents = count($students);
        $attendanceRate = $totalStudents > 0 ? round((($presentCount + $lateCount) / $totalStudents) * 100, 1) : 0;
        ?>

        <div class="summary">
            <h3>Attendance Summary</h3>
            <p><strong>Total Students:</strong> <?= $totalStudents ?></p>
            <p><strong>Present:</strong> <?= $presentCount ?></p>
            <p><strong>Absent:</strong> <?= $absentCount ?></p>
            <p><strong>Late:</strong> <?= $lateCount ?></p>
            <p><strong>Excused:</strong> <?= $excusedCount ?></p>
            <p><strong>Attendance Rate:</strong> <?= $attendanceRate ?>%</p>
        </div>

        <!-- Detailed Attendance Table -->
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Registration Number</th>
                    <th>Student Name</th>
                    <th>Status</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $attendanceLookup = [];
                foreach ($attendance as $record) {
                    $attendanceLookup[$record->student_id] = $record;
                }
                
                foreach ($students as $index => $student): 
                    $attendanceRecord = $attendanceLookup[$student->id] ?? null;
                ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= h($student->regno) ?></td>
                        <td><?= h($student->fname . ' ' . $student->lname) ?></td>
                        <td>
                            <?php if ($attendanceRecord): ?>
                                <span class="status-<?= $attendanceRecord->status ?>">
                                    <?= ucfirst($attendanceRecord->status) ?>
                                </span>
                            <?php else: ?>
                                <span class="status-absent">Not Marked</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($attendanceRecord && !empty($attendanceRecord->notes)): ?>
                                <?= h($attendanceRecord->notes) ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="no-print" style="margin-top: 30px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Print Report</button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 16px; background-color: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">Close</button>
    </div>
</body>
</html>
