<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance Report - Print</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
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
            font-size: 24px;
            color: #333;
        }
        .header h2 {
            margin: 5px 0 0 0;
            font-size: 18px;
            color: #666;
        }
        .report-info {
            margin-bottom: 20px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        .report-info h3 {
            margin: 0 0 10px 0;
            font-size: 16px;
            color: #333;
        }
        .report-info p {
            margin: 5px 0;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            font-size: 11px;
        }
        td {
            font-size: 10px;
        }
        .status-present {
            background-color: #d4edda;
            color: #155724;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        .status-absent {
            background-color: #f8d7da;
            color: #721c24;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        .status-late {
            background-color: #fff3cd;
            color: #856404;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        .status-excused {
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
        }
        .statistics {
            margin-top: 30px;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        .statistics h3 {
            margin: 0 0 15px 0;
            font-size: 16px;
            color: #333;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }
        .stat-item {
            text-align: center;
            padding: 10px;
            border-radius: 5px;
            color: white;
        }
        .stat-present { background-color: #28a745; }
        .stat-absent { background-color: #dc3545; }
        .stat-late { background-color: #ffc107; color: #333; }
        .stat-excused { background-color: #17a2b8; }
        .stat-item h4 {
            margin: 0 0 5px 0;
            font-size: 14px;
        }
        .stat-item .number {
            font-size: 24px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 15px;
        }
        @media print {
            body { margin: 0; padding: 15px; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Attendance Report</h1>
        <h2><?= SCHOOL ?></h2>
    </div>

    <div class="report-info">
        <h3>Report Details</h3>
        <p><strong>Generated:</strong> <?= date('F j, Y \a\t g:i A') ?></p>
        <?php if ($department): ?>
            <p><strong>Class:</strong> <?= h($department->name) ?></p>
        <?php else: ?>
            <p><strong>Class:</strong> All Classes</p>
        <?php endif; ?>
        <p><strong>Date Range:</strong> <?= date('M j, Y', strtotime($startDate)) ?> - <?= date('M j, Y', strtotime($endDate)) ?></p>
        <?php if ($status): ?>
            <p><strong>Status Filter:</strong> <?= ucfirst($status) ?></p>
        <?php endif; ?>
    </div>

    <?php if (!empty($attendanceRecords)): ?>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Student Name</th>
                    <th>Registration No.</th>
                    <th>Class</th>
                    <th>Status</th>
                    <th>Teacher</th>
                    <th>Notes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($attendanceRecords as $record): ?>
                    <tr>
                        <td><?= h($record->attendance_date->format('M d, Y')) ?></td>
                        <td><?= h($record->student->fname . ' ' . $record->student->lname) ?></td>
                        <td><?= h($record->student->regno) ?></td>
                        <td><?= h($record->student->department->name . (!empty($record->student->class_arm) ? ' - ' . $record->student->class_arm->arm_name : '')) ?></td>
                        <td>
                            <span class="status-<?= $record->status ?>"><?= ucfirst($record->status) ?></span>
                        </td>
                        <td><?= h($record->teacher->firstname . ' ' . $record->teacher->lastname) ?></td>
                        <td><?= h($record->notes) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="statistics">
            <h3>Attendance Statistics</h3>
            <div class="stats-grid">
                <div class="stat-item stat-present">
                    <h4>Present</h4>
                    <div class="number"><?= isset($attendanceStats->present) ? $attendanceStats->present : 0 ?></div>
                </div>
                <div class="stat-item stat-absent">
                    <h4>Absent</h4>
                    <div class="number"><?= isset($attendanceStats->absent) ? $attendanceStats->absent : 0 ?></div>
                </div>
                <div class="stat-item stat-late">
                    <h4>Late</h4>
                    <div class="number"><?= isset($attendanceStats->late) ? $attendanceStats->late : 0 ?></div>
                </div>
                <div class="stat-item stat-excused">
                    <h4>Excused</h4>
                    <div class="number"><?= isset($attendanceStats->excused) ? $attendanceStats->excused : 0 ?></div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div style="text-align: center; padding: 40px; color: #666;">
            <h3>No Records Found</h3>
            <p>No attendance records match the specified criteria.</p>
        </div>
    <?php endif; ?>

    <div class="footer">
        <p>This report was generated on <?= date('F j, Y \a\t g:i A') ?> by the School Management System</p>
    </div>
</body>
</html>
