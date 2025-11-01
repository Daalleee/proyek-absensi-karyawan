<!DOCTYPE html>
<html>
<head>
    <title>Attendance Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .info {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Attendance Report</h1>
        <p>Report Period: {{ $fromDate }} to {{ $toDate }}</p>
    </div>

    <div class="info">
        <p>Total Records: {{ count($attendances) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Employee</th>
                <th>Location</th>
                <th>Check In</th>
                <th>Check Out</th>
                <th>Location Valid</th>
                <th>Face Recognized</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $attendance)
            <tr>
                <td>{{ $attendance->id }}</td>
                <td>{{ $attendance->employee->first_name }} {{ $attendance->employee->last_name }}</td>
                <td>{{ $attendance->workLocation->name }}</td>
                <td>{{ $attendance->check_in_time ? $attendance->check_in_time->format('d/m/Y H:i') : '-' }}</td>
                <td>{{ $attendance->check_out_time ? $attendance->check_out_time->format('d/m/Y H:i') : '-' }}</td>
                <td>{{ $attendance->is_check_in_valid ? 'Yes' : 'No' }}</td>
                <td>{{ $attendance->is_face_recognized ? 'Yes' : 'No' }}</td>
                <td>{{ ucfirst($attendance->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on: {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>