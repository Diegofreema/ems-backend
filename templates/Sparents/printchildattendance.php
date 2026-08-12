<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child Attendance Report - <?= h($student->fname . ' ' . $student->lname) ?> - <?= date('M j, Y', strtotime($startDate)) ?> to <?= date('M j, Y', strtotime($endDate)) ?></title>
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
        <h1>Child Attendance Report</h1>
        <h2><?= h($student->fname . ' ' . $student->lname) ?></h2>
        <p>Period: <?= date('M j, Y', strtotime($startDate)) ?> - <?= date('M j, Y', strtotime($endDate)) ?></p>
    </div>

    <div class="info">
        <p><strong>Student Name:</strong> <?= h($student->fname . ' ' . $student->lname) ?></p>
        <p><strong>Registration Number:</strong> <?= h($student->regno) ?></p>
        <p><strong>Class:</strong> <?= h($student->department->name . (!empty($student->class_arm) ? ' - ' . $student->class_arm->arm_name : '') ?? 'Not specified') ?></p>
        <p><strong>Parent:</strong> <?= h($parent->fname . ' ' . $parent->lname) ?></p>
        <p><strong>Report Period:</strong> <?= date('M j, Y', strtotime($startDate)) ?> - <?= date('M j, Y', strtotime($endDate)) ?></p>
        <p><strong>Generated:</strong> <?= date('M j, Y, g:i A') ?></p>
    </div>

    <?php if (empty($attendance)): ?>
        <div class="no-data">
            <h3>No Attendance Records</h3>
            <p>No attendance records found for the selected period.</p>
        </div>
    <?php else: ?>
        <!-- Attendance Summary -->
        <div class="summary">
            <h3>Attendance Summary</h3>
            <p><strong>Total Records:</strong> <?= $attendanceStats['total'] ?></p>
            <p><strong>Present:</strong> <?= $attendanceStats['present'] ?></p>
            <p><strong>Absent:</strong> <?= $attendanceStats['absent'] ?></p>
            <p><strong>Late:</strong> <?= $attendanceStats['late'] ?></p>
            <p><strong>Excused:</strong> <?= $attendanceStats['excused'] ?></p>
            <p><strong>Attendance Rate:</strong> <?= $attendanceStats['rate'] ?>%</p>
        </div>

        <!-- Detailed Attendance Table -->
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Day</th>
                    <th>Status</th>
                    <th>Notes</th>
                    <th>Recorded By</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendance as $record): ?>
                    <tr>
                        <td><?= date('M j, Y', strtotime($record->attendance_date)) ?></td>
                        <td><?= date('l', strtotime($record->attendance_date)) ?></td>
                        <td>
                            <span class="status-<?= $record->status ?>">
                                <?= ucfirst($record->status) ?>
                            </span>
                        </td>
                        <td>
                            <?php if (!empty($record->notes)): ?>
                                <?= h($record->notes) ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= h($record->teacher->firstname . ' ' . $record->teacher->lastname) ?>
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
