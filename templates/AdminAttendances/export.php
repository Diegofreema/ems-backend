<?php
/**
 * CSV Export for Attendance Records
 * This template generates CSV output for attendance data
 */

// Set CSV headers
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="attendance_report_' . date('Y-m-d') . '.csv"');

// Create output stream
$output = fopen('php://output', 'w');

// Write CSV headers
fputcsv($output, [
    'Date',
    'Student Name',
    'Registration Number',
    'Class',
    'Status',
    'Teacher',
    'Notes'
]);

// Write data rows
if (!empty($attendanceRecords)) {
    foreach ($attendanceRecords as $record) {
        fputcsv($output, [
            $record->attendance_date->format('Y-m-d'),
            $record->student->fname . ' ' . $record->student->lname,
            $record->student->regno,
            $record->student->department->name . (!empty($record->student->class_arm) ? ' - ' . $record->student->class_arm->arm_name : ''),
            ucfirst($record->status),
            $record->teacher->firstname . ' ' . $record->teacher->lastname,
            $record->notes
        ]);
    }
}

// Close output stream
fclose($output);
?>
