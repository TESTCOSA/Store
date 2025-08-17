@props(['record', 'isDraft'])

<table class="bordered" style="font-size: 8pt; margin-top: 15px;">
    <tr>
        <td class="bg-grey" width="15%"><b>Inspected By:</b></td>
        <td width="35%">{{ $record->inspector->full_name_en ?? 'N/A' }}</td>
        <td class="bg-grey" width="20%"><b>Approved By:</b></td>
        <td width="30%">{{ $record->approvedBy->full_name_en ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td class="bg-grey" style="height: 35px;"><b>Signature:</b></td>
        <td class="text-center">
            @if(!$isDraft && $record->inspector?->userDetails?->digital_sig && file_exists(public_path('users/' . $record->inspector->userDetails->digital_sig)))
                <img src="{{ public_path('users/' . $record->inspector->userDetails->digital_sig) }}" class="signature">
            @endif
        </td>
        <td class="bg-grey"><b>Signature:</b></td>
        <td class="text-center">
            @if(!$isDraft && $record->approvedBy?->userDetails?->digital_sig && file_exists(public_path('users/' . $record->approvedBy->userDetails->digital_sig)))
                <img src="{{ public_path('users/' . $record->approvedBy->userDetails->digital_sig) }}" class="signature">
            @endif
        </td>
    </tr>
</table>
