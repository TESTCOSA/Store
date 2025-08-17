<!DOCTYPE html>
<html>
<head>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        .red {
            color: #e30000;
        }
        .yellow {
            color: #f8f803;
        }
    </style>
</head>
<body>
<h1>Calibration Expiry Notification</h1>
<p>The following calibrations are nearing expiry:</p>
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Item Name</th>
        <th>Serial Number</th>
        <th>Due Date</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($calibrations as $calibration)
        @php
            $remainingDays = floor(now()->diffInDays($calibration->due_date, false));
        @endphp
        <tr>
            <td>{{ $calibration->id }}</td>
            <td>{{ $calibration->item->name }}</td>
            <td>{{ $calibration->item->serial_number }}</td>
            <td>{{ $calibration->due_date }}</td>
            <td class="{{ $remainingDays < 0 ? 'red' : 'yellow' }}">
                @if ($remainingDays < 0)
                    Expired since: {{ abs($remainingDays) }} days
                @else
                    Due in: {{ $remainingDays }} days
                @endif
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
<p>Thanks,<br>{{ config('app.name') }}</p>
</body>
</html>
