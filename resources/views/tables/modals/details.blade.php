<table class="w-full border-collapse border border-gray-300">
    <thead>
    <tr class="bg-gray-100">
        <th class="border border-gray-300 px-4 py-2">Item Name</th>
        <th class="border border-gray-300 px-4 py-2">Number</th>
        <th class="border border-gray-300 px-4 py-2">Date</th>
        <th class="border border-gray-300 px-4 py-2">Due Date</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($details as $detail)
        <tr>
            <td class="border border-gray-300 px-4 py-2">{{ $detail->stock->item->name ?? 'N/A' }}</td>
            <td class="border border-gray-300 px-4 py-2">{{ $detail->number ?? $detail->stock->item->serial_number }}</td>
            <td class="border border-gray-300 px-4 py-2">{{ $detail->date ?? 'N/A'}}</td>
            <td class="border border-gray-300 px-4 py-2">{{ $detail->due_date ?? 'N/A'}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
