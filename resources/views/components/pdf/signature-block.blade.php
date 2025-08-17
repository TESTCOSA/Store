@props(['record', 'isDraft'])

<table class="bordered page-footer" style="font-size: 8pt; margin-top: 15px; ">
    <tr>
        <td class="bg-grey" width="15%"><b>Inspected By:</b></td>
        <td width="35%">{{ $record->inspector->full_name_en ?? 'N/A' }}</td>
        <td class="bg-grey" width="20%"><b>Approved By:</b></td>
        <td width="30%">{{ $record->approver->full_name_en ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td class="bg-grey" style="height: 35px;"><b>Signature:</b></td>
        <td class="text-center">
            @if(!$isDraft && $record->inspector?->digital_sig_b && file_exists(storage_path('app/public/users/' . $record->inspector->digital_sig_b)))
                <img src="{{ storage_path('app/public/users/' . $record->inspector->digital_sig_b) }}" class="signature">
            @endif
        </td>
        <td class="bg-grey"><b>Signature:</b></td>
        <td class="text-center">
            @if(!$isDraft && $record->approver?->digital_sig_b && file_exists(storage_path('app/public/users/' . $record->approver->digital_sig_b)))
                <img src="{{ storage_path('app/public/users/' . $record->approver->digital_sig_b) }}" class="signature">
            @endif
        </td>
    </tr>
</table>
