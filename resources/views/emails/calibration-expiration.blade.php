<!DOCTYPE html>
<html>
<head>
    <title>Calibration Expiration Notice</title>
</head>
<body>
<h2>Upcoming Calibration Expirations</h2>

<p>The following items are due for calibration within the next 2 weeks:</p>

<table style="width: 100%; border-collapse: collapse;">
    <thead>
    <tr>
        <th style="border: 1px solid #ddd; padding: 8px;">Item</th>
        <th style="border: 1px solid #ddd; padding: 8px;">Calibration Date</th>
        <th style="border: 1px solid #ddd; padding: 8px;">Due Date</th>
    </tr>
    </thead>
    <tbody>
    @foreach($calibrations as $calibration)
        <tr>
            <td style="border: 1px solid #ddd; padding: 8px;">{{ $calibration->item->name }}</td>
            <td style="border: 1px solid #ddd; padding: 8px;">{{ $calibration->date->format('Y-m-d') }}</td>
            <td style="border: 1px solid #ddd; padding: 8px;">{{ $calibration->due_date->format('Y-m-d') }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

<p>Please schedule recalibration for these items as soon as possible.</p>
</body>
</html>
