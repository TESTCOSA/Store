<h2>INSPECTION CHECKLIST</h2>
<table class="bordered">
    <thead>
    <tr>
        <th width="70%">Item Description</th>
        <th width="10%" class="text-center text-green">PASS</th>
        <th width="10%" class="text-center text-red">FAIL</th>
        <th width="10%" class="text-center">N/A</th>
    </tr>
    </thead>
    <tbody>
    @if($record->checklist)
        @foreach($record->checklist->first()->details as $detail)
            <tr style="font-size: 8pt;">

                <td>{{ $loop->iteration }} - {{ $detail->item->item_title ?? 'Item not found' }}</td>

                <td class="text-center">@if($detail->pass_fail == 1) @svg('heroicon-s-check-circle', 'icon-pass') @endif</td>
                <td class="text-center">@if($detail->pass_fail == 2) @svg('heroicon-s-x-circle', 'icon-fail') @endif</td>
                <td class="text-center">@if($detail->pass_fail == 0) @svg('heroicon-s-minus-circle', 'icon-na') @endif</td>
            </tr>
        @endforeach
    @else
        <tr><td colspan="4" class="text-center">No checklist data found.</td></tr>
    @endif
    </tbody>
</table>
<br>
<table>
    <tr>
        <th width="15%" class="text-center text-green">Accept <br> Rope Dia.+2.5% <br> Max.</th>
        <th width="15%" class="text-center text-red">Reject<br> More than Rope Dia.+ 2.5%</th>
        <th width="35%" class="text-center">Cluster Sheaves Wear<br> Check Photograph</th>
        <th width="35%" class="text-center">Fast Line Sheave Wear<br>Check Photograph</th>
    </tr>
    <tr>
        <td class="text-center">
            @if(file_exists(storage_path('app/public/images/sheaves_groove.jpeg')))
                <img src="{{ storage_path('app/public/images/sheaves_groove.jpeg') }}"  style="height:200px;"/>
            @endif
        </td>
        <td class="text-center">
            @if(file_exists(storage_path('app/public/images/sheaves_groove.jpeg')))
                <img src="{{ storage_path('app/public/images/sheaves_groove.jpeg') }}"  style="height:200px;"/>
            @endif
        </td>
        <td class="text-center">
            @if($record->cluster_wear_photo && file_exists(storage_path('app/public/' . $record->cluster_wear_photo)))
                <img src="{{ storage_path('app/public/' . $record->cluster_wear_photo) }}" style="height:200px; border:1px solid #000;">
            @else
                <div style="height:200px; border:1px solid #000; padding-top: 90px; text-align:center;">Photo Not Available</div>
            @endif
        </td>
        <td class="text-center">
            @if($record->fast_line_wear_photo && file_exists(storage_path('app/public/' . $record->fast_line_wear_photo)))
                <img src="{{ storage_path('app/public/' . $record->fast_line_wear_photo) }}" style="height:200px; border:1px solid #000;">
            @else
                <div style="height:200px; border:1px solid #000; padding-top: 90px; text-align:center;">Photo Not Available</div>
            @endif
        </td>
    </tr>
</table>
