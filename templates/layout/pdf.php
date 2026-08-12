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
            font-size: 11px;
            line-height: 1.3;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 15px;
        }
        
        .result-container {
            max-width: 100%;
            margin: 0 auto;
            background: #fff;
        }
        
        /* Header Section */
        .header-section {
            margin-bottom: 20px;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
            position: relative;
        }
        
        .header-content {
            display: table;
            width: 100%;
        }
        
        .header-left {
            display: table-cell;
            width: 30%;
            vertical-align: top;
            text-align: left;
        }
        
        .header-center {
            display: table-cell;
            width: 40%;
            vertical-align: top;
            text-align: center;
        }
        
        .header-right {
            display: table-cell;
            width: 30%;
            vertical-align: top;
            text-align: right;
        }
        
        .school-logo {
            width: 80px;
            height: 80px;
            display: block;
        }
        
        .student-photo {
            width: 80px;
            height: 80px;
            border: 2px solid #000;
            display: block;
            margin-left: auto;
            background-color: #f5f5f5;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .photo-placeholder {
            font-size: 8px;
            color: #666;
            text-align: center;
        }
        
        .school-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
            letter-spacing: 1px;
        }
        
        .school-details {
            font-size: 10px;
            margin-bottom: 5px;
        }
        
        /* Student Info Section */
        .student-info-section {
            margin-bottom: 20px;
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
            margin-bottom: 3px;
            font-size: 10px;
        }
        
        .info-value {
            font-size: 11px;
        }
        
        /* Results Table */
        .results-section {
            margin-bottom: 20px;
        }
        
        .section-title {
            background-color: #f0f0f0;
            border: 2px solid #000;
            border-bottom: none;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            font-size: 12px;
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
            padding: 6px 4px;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
        }
        
        .results-table td {
            border: 1px solid #000;
            padding: 5px 4px;
            text-align: center;
            font-size: 10px;
        }
        
        .subject-name {
            text-align: left;
            font-weight: bold;
        }
        
        .total-row {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        
        /* Grade Badges */
        .grade-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
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
            margin: 15px 0;
            border: 2px solid #000;
        }
        
        .grading-key-title {
            background-color: #f0f0f0;
            border-bottom: 1px solid #000;
            padding: 6px;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }
        
        .grading-key-content {
            padding: 8px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }
        
        .grade-item {
            text-align: center;
            margin: 3px;
            font-size: 9px;
        }
        
        /* Comments and Stamp Section */
        .comments-stamp-section {
            margin-top: 20px;
            border: 2px solid #000;
        }
        
        .comments-stamp-title {
            background-color: #f0f0f0;
            border-bottom: 1px solid #000;
            padding: 6px;
            text-align: center;
            font-weight: bold;
            font-size: 10px;
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
            padding: 10px;
            vertical-align: top;
            border-right: 1px solid #000;
        }
        
        .stamp-cell {
            display: table-cell;
            width: 40%;
            padding: 10px;
            text-align: center;
            vertical-align: top;
        }
        
        .comment-line {
            margin-bottom: 8px;
            font-size: 10px;
        }
        
        .comment-label {
            font-weight: bold;
            margin-right: 5px;
        }
        
        .comment-underline {
            border-bottom: 1px solid #000;
            display: inline-block;
            width: 200px;
            height: 15px;
        }
        
        .stamp-box {
            border: 2px solid #000;
            padding: 10px;
            display: inline-block;
            text-align: center;
            background-color: #f9f9f9;
        }
        
        .stamp-title {
            font-weight: bold;
            font-size: 10px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .stamp-area {
            border: 1px solid #000;
            width: 60px;
            height: 50px;
            margin: 0 auto 5px;
            background-color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .stamp-placeholder {
            font-size: 8px;
            color: #666;
        }
        
        .stamp-date {
            font-size: 8px;
            color: #666;
        }
        
        /* Average Section */
        .average-section {
            text-align: center;
            margin: 15px 0;
            font-size: 11px;
            font-weight: bold;
        }
        
        /* Watermark */
        .watermark {
            position: fixed;
            bottom: 20px;
            right: 20px;
            opacity: 0.1;
            color: #000;
            font-size: 60px;
            font-weight: bold;
            transform: rotate(-45deg);
            pointer-events: none;
            z-index: -1;
        }
        
        /* Print Styles */
        @media print {
            body { 
                margin: 0; 
                padding: 10px;
            }
            .watermark { 
                opacity: 0.1;
            }
        }
        
        /* Utility Classes */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .font-bold { font-weight: bold; }
        .mb-10 { margin-bottom: 10px; }
        .mt-10 { margin-top: 10px; }
    </style>
</head>
<body>
    <div class="result-container">
        <?= $this->fetch('content') ?>
    </div>
    
    <div class="watermark">
        STUDENT COPY
    </div>
</body>
</html>
