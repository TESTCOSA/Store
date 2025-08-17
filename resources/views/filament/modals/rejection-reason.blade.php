<div>
    <p><strong>Rejected By:</strong> {{ $record->approver?->full_name_en ?? 'N/A' }}</p>
    <p><strong>Date:</strong> {{ $record->approved_date }}</p>
    <p><strong>Reason:</strong> {{ $record->reject_reason }}</p>
</div>
