<!DOCTYPE html>
<html>
<head>
    <title>New Stock Out Request</title>
</head>
<body>
    <h2>A new stock out request has been created</h2>
    <p><strong>Inspector:</strong> {{ $inspector }}</p>
    <p><strong>Work Order ID:</strong> {{ $wo_id }}</p>

    <h3>Requested Tools:</h3>
    <ul>
        @foreach($tools as $detail)
            <li>{{ $detail->outItems->name }} - Qty: {{ $detail->quantity }}</li>
        @endforeach
    </ul>

    <p>
        <a href="{{ $requestUrl }}" style="background-color: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;">
            View Requests
        </a>
    </p>
</body>
</html>
