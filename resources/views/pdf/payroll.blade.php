<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payroll Report</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f5f5f5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .department-section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .department-header {
            background-color: #e6e6e6;
            padding: 5px 10px;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="header">
    <h1>Payroll Report</h1>
    <p>Period: {{ $period }}</p>
    <p>Generated on: {{ $generatedAt->format('Y-m-d H:i') }}</p>
</div>

@php
    $departmentGroups = $records->groupBy('department');
@endphp

@foreach($departmentGroups as $department => $departmentRecords)
    <div class="department-section">
        <div class="department-header">{{ $department }}</div>

        @foreach($departmentRecords as $record)
            <h4>Attendance Sheet ID: {{ $record->id }}</h4>
            <p>
                Period: {{ $record->period_start->format('Y-m-d') }} - {{ $record->period_end->format('Y-m-d') }}<br>
                Approval Status: Approved
            </p>

            <table>
                <thead>
                <tr>
                    <th>Employee</th>
                    <th>Present</th>
                    <th>Absent</th>
                    <th>Annual Leave</th>
                    <th>Sick Leave</th>
                    <th>Unpaid Leave</th>
                </tr>
                </thead>
                <tbody>
                @foreach($record->sheets as $sheet)
                    <tr>
                        <td>{{ $sheet->userDetails->full_name_en ?? 'N/A' }}</td>
                        <td>{{ $sheet->present_count }}</td>
                        <td>{{ $sheet->absent_count }}</td>
                        <td>{{ $sheet->annual_leave_count }}</td>
                        <td>{{ $sheet->sick_leave_count }}</td>
                        <td>{{ $sheet->unpaid_leave_count }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endforeach
    </div>
@endforeach

<div style="text-align: center; margin-top: 30px; font-size: 10px;">
    <p>This is an automatically generated report.</p>
</div>
</body>
</html>
