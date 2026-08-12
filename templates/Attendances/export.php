<?php
// Set CSV headers
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="attendance_report_' . $startDate . '_to_' . $endDate . '.csv"');

// Create output stream
$output = fopen('php://output', 'w');

// Write CSV header
fputcsv($output, [
    'Student ID',
    'Registration Number', 
    'Student Name',
    'Date',
    'Status',
    'Notes',
    'Recorded By'
]);

// Write attendance data
foreach ($students as $student) {
    if (isset($studentAttendance[$student->id])) {
        foreach ($studentAttendance[$student->id] as $record) {
            fputcsv($output, [
                $student->id,
                $student->regno,
                $student->fname . ' ' . $student->lname,
                $record->attendance_date,
                ucfirst($record->status),
                $record->notes ?? '',
                $teacher->firstname . ' ' . $teacher->lastname
            ]);
        }
    } else {
        // If no attendance records, still include the student
        fputcsv($output, [
            $student->id,
            $student->regno,
            $student->fname . ' ' . $student->lname,
            'No records',
            'Not marked',
            '',
            $teacher->firstname . ' ' . $teacher->lastname
        ]);
    }
}

// Close output stream
fclose($output);
exit;
?>
