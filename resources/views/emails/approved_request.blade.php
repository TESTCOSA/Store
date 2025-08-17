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
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .red{
            color: #e30000;
        }
        .yellow {
            color: #f8f803;
        }
    </style>
</head>
<body>
<p>The following calibrations are nearing expiry:</p>

<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Item Name</th>
        <th>
            Calibration
        </th>
    </tr>
    </thead>
    <tbody>
    @foreach($details as $detail)
    <tr>
        <td>{{ $detail->outItems->name }}</td>
        <td>{{ $detail->outItems->serial_number}}</td>
        @if($detail->outItems->category->type->is_calibrated)
        <td><a href="{{asset('storage/'.$detail->outItems->calibrations->sortByDesc('due_date')->first()->file)}}" style="">Download</a></td>
        @endif
    </tr>
    @endforeach
    </tbody>
</table>

<p>Thank you for using our application!</p>
</body>
</html>
