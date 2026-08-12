<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->fetch('title') ?></title>
    <?= $this->Html->meta('icon') ?>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 20px;
        }
        
        .print-container {
            max-width: 100%;
            margin: 0 auto;
            background: #fff;
        }
        
        /* Header Section */
        .header-section {
            margin-bottom: 25px;
            border-bottom: 3px solid #000;
            padding-bottom: 20px;
        }
        
        .header-content {
            display: table;
            width: 100%;
        }
        
        .header-left {
            display: table-cell;
            width: 25%;
            vertical-align: top;
            text-align: left;
        }
        
        .header-center {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: center;
        }
        
        .header-right {
            display: table-cell;
            width: 25%;
            vertical-align: top;
            text-align: right;
        }
        
        .school-logo {
            width: 80px;
            height: 80px;
            border: 1px solid #000;
        }
        
        .student-photo {
            width: 80px;
            height: 80px;
            border: 2px solid #000;
            display: inline-block;
            background-color: #f5f5f5;
        }
        
        .photo-placeholder {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            font-size: 8px;
            color: #666;
            text-align: center;
        }
        
        .school-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 8px;
            letter-spacing: 1px;
        }
        
        .school-details {
            font-size: 11px;
            margin-bottom: 5px;
        }
        
        .report-title {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 10px;
            letter-spacing: 1px;
        }
        
        /* Student Info Section */
        .student-info-section {
            margin-bottom: 25px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            border: 2px solid #000;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-cell {
            display: table-cell;
            padding: 8px 12px;
            border: 1px solid #000;
            vertical-align: top;
            width: 50%;
        }
        
        .info-label {
            font-weight: bold;
            margin-bottom: 2px;
            font-size: 10px;
            display: inline-block;
            width: 45%;
        }
        
        .info-value {
            font-size: 11px;
            display: inline-block;
            width: 50%;
            margin-left: 5px;
        }
        
        /* Results Table */
        .results-section {
            margin-bottom: 25px;
        }
        
        .section-title {
            background-color: #f0f0f0;
            border: 2px solid #000;
            border-bottom: none;
            padding: 10px;
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .results-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
            margin-bottom: 0;
        }
        
        .results-table th {
            background-color: #e0e0e0;
            border: 1px solid #000;
            padding: 10px 8px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
        }
        
        .results-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-size: 13px;
        }
        
        .subject-name {
            text-align: left;
            font-weight: bold;
            font-size: 13px;
        }
        
        .total-row {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        
        /* Grade Badges */
        .grade-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            color: white;
        }
        
        .grade-A { background-color: #28a745; }
        .grade-B { background-color: #007bff; }
        .grade-C { background-color: #17a2b8; }
        .grade-D { background-color: #ffc107; color: #000; }
        .grade-E { background-color: #6c757d; }
        .grade-NI { background-color: #dc3545; }
        
        /* Grading Key */
        .grading-key {
            margin: 20px 0;
            border: 2px solid #000;
        }
        
        .grading-key-title {
            background-color: #f0f0f0;
            border-bottom: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
        }
        
        .grading-key-content {
            padding: 10px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }
        
        .grade-item {
            text-align: center;
            margin: 5px;
            font-size: 10px;
        }
        
        /* Comments and Stamp Section */
        .comments-stamp-section {
            margin-top: 25px;
        }
        
        .comments-stamp-title {
            background-color: #f0f0f0;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
            text-transform: uppercase;
        }
        
        .comments-stamp-content {
            display: table;
            width: 100%;
        }
        
        .comments-stamp-row {
            display: table-row;
        }
        
        .comments-cell {
            display: table-cell;
            width: 60%;
            padding: 15px;
            vertical-align: top;
        }
        
        .stamp-cell {
            display: table-cell;
            width: 40%;
            padding: 15px;
            text-align: center;
            vertical-align: top;
        }
        
        .comment-line {
            margin-bottom: 15px;
            font-size: 11px;
        }
        
        .comment-label {
            font-weight: bold;
            margin-right: 8px;
        }
        
        .comment-underline {
            display: inline-block;
            width: 250px;
            height: 18px;
            border-bottom: 1px solid #000;
        }
        
        .stamp-box {
            padding: 15px;
            display: inline-block;
            text-align: center;
            background-color: #f9f9f9;
        }
        
        .stamp-title {
            font-weight: bold;
            font-size: 12px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        
        .stamp-area {
            width: 120px;
            height: 90px;
            margin: 0 auto 8px;
            background-color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .stamp-placeholder {
            font-size: 9px;
            color: #666;
        }
        
        .stamp-date {
            font-size: 9px;
            color: #666;
        }
        
        /* Average Section */
        .average-section {
            text-align: center;
            margin: 20px 0;
            font-size: 15px;
            font-weight: bold;
            padding: 12px;
            background-color: #f8f9fa;
            border: 1px solid #000;
        }
        
        /* Print Styles */
        @media print {
            body { 
                margin: 0; 
                padding: 15px;
            }
            .print-container {
                max-width: none;
            }
        }
        
        /* Utility Classes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .mb-15 { margin-bottom: 15px; }
        .mt-15 { margin-top: 15px; }
    </style>
</head>
<body>
    <div class="print-container">
        <?= $this->fetch('content') ?>
    </div>
</body>
</html>
