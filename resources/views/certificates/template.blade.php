<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Course Completion Certificate</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            text-align: center;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            border: 10px solid #0056b3;
            padding: 50px;
            margin: 20px;
            height: 600px;
            position: relative;
        }
        .header {
            font-size: 40px;
            font-weight: bold;
            color: #0056b3;
            margin-bottom: 20px;
            text-transform: uppercase;
        }
        .subheader {
            font-size: 20px;
            margin-bottom: 40px;
        }
        .student-name {
            font-size: 35px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 30px;
        }
        .content {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 50px;
        }
        .course-name {
            font-size: 24px;
            font-weight: bold;
            color: #d9534f;
        }
        .footer {
            position: absolute;
            bottom: 50px;
            width: 90%;
            display: table;
        }
        .signature-block {
            display: table-cell;
            width: 50%;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 70%;
            margin: 0 auto 10px auto;
        }
        .cert-number {
            position: absolute;
            bottom: 10px;
            left: 50px;
            font-size: 12px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">Certificate of Completion</div>
        <div class="subheader">This is to certify that</div>
        
        <div class="student-name">{{ $student_name }}</div>
        
        <div class="content">
            has successfully fulfilled the requirements for the course<br>
            <span class="course-name">{{ $course_name }}</span><br>
            (Batch: {{ $batch_name }})<br>
            with exceptional attendance and performance.
        </div>
        
        <div class="footer">
            <div class="signature-block">
                <div class="signature-line"></div>
                <strong>{{ $faculty_name }}</strong><br>
                Course Faculty
            </div>
            <div class="signature-block">
                <div class="signature-line"></div>
                <strong>{{ $admin_name }}</strong><br>
                Administrator<br>
                Date: {{ $completion_date }}
            </div>
        </div>
        
        <div class="cert-number">Certificate No: {{ $certificate_number }}</div>
    </div>
</body>
</html>