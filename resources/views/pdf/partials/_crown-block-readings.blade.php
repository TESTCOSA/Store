<h2>GROOVE DEPTH MEASUREMENTS</h2>
@php
    $cluster_min = (float)$record->drill_line_dia * 1.33;
    $cluster_max = (float)$record->drill_line_dia * 1.75;
@endphp
<table>
    <tr>
        <td style="border: none;" width="40%"><b style="text-decoration: underline;">Cluster Sheaves Min. Groove Depth</b></td>
        <td class="bordered" width="10%">{{ number_format($cluster_min, 2) }} mm</td>
        <td style="border: none;" width="40%" class="text-right"><b style="text-decoration: underline;">Max. Groove Depth</b></td>
        <td class="bordered" width="10%">{{ number_format($cluster_max, 2) }} mm</td>
    </tr>
    <tr>
        <td style="border: none;" width="40%"><b style="text-decoration: underline;">Fast Line Sheave Min. Groove Depth</b></td>
        <td class="bordered" width="10%">{{ number_format($cluster_min, 2) }} mm</td>
        <td style="border: none;" width="40%" class="text-right"><b style="text-decoration: underline;">Max. Groove Depth</b></td>
        <td class="bordered" width="10%">{{ number_format($cluster_max, 2) }} mm</td>
    </tr>
</table>
<br>
<table class="bordered">
    <thead>
    <tr>
        <th>Cluster Sheaves<br> Serial No</th>
        <th>Nominal Wire<br>Rope Diameter</th>
        <th>Groove Depth<br> Point (A)</th>
        <th>Groove Depth<br> Point (B)</th>
        <th>Groove Depth<br> Point (C)</th>
        <th>Groove Depth<br> Point (D)</th>
        <th><span class="text-green">Pass</span>/<span class="text-red">Fail</span></th>
    </tr>
    </thead>
    <tbody>
    @forelse($record->clusterReadings as $reading)
        <tr>
            <td class="text-center">{{ $reading->cluster_sn }}</td>
            <td class="text-center">{{ $record->drill_line_dia }} mm</td>
            <td class="text-center">{{ $reading->groove_a }} mm</td>
            <td class="text-center">{{ $reading->groove_b }} mm</td>
            <td class="text-center">{{ $reading->groove_c }} mm</td>
            <td class="text-center">{{ $reading->groove_d }} mm</td>
            <td class="text-center @if($reading->pass_fail == 1) text-green @else text-red @endif">
                {{ $reading->pass_fail == 1 ? 'Pass' : 'Fail' }}
            </td>
        </tr>
    @empty
        <tr><td colspan="7" class="text-center">No cluster readings found.</td></tr>
    @endforelse
    </tbody>
</table>
<br>
@if($record->fastLineReading)
    <table class="bordered">
        <thead>
        <tr>
            <th>Fast Line Sheave<br> Serial No</th>
            <th>Nominal Wire<br>Rope Diameter</th>
            <th>Groove Depth<br> Point (A)</th>
            <th>Groove Depth<br> Point (B)</th>
            <th>Groove Depth<br> Point (C)</th>
            <th>Groove Depth<br> Point (D)</th>
            <th><span class="text-green">Pass</span>/<span class="text-red">Fail</span></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td class="text-center">{{ $record->fastLineReading->fast_line_sn }}</td>
            <td class="text-center">{{ $record->drill_line_dia }} mm</td>
            <td class="text-center">{{ $record->fastLineReading->groove_a }} mm</td>
            <td class="text-center">{{ $record->fastLineReading->groove_b }} mm</td>
            <td class="text-center">{{ $record->fastLineReading->groove_c }} mm</td>
            <td class="text-center">{{ $record->fastLineReading->groove_d }} mm</td>
            <td class="text-center @if($record->fastLineReading->pass_fail == 1) text-green @else text-red @endif">
                {{ $record->fastLineReading->pass_fail == 1 ? 'Pass' : 'Fail' }}
            </td>
        </tr>
        </tbody>
    </table>
@endif
<br>
<table>
    <tr>
        <td width="10%" class="text-center text-blue" rowspan="2" style="vertical-align: middle;">Cluster<br>Sheaves<br>Depth Check</td>
        <td width="40%" class="text-center">
            @if(file_exists(storage_path('app/public/images/cluster_sheaves.jpeg')))
                <img src="{{ storage_path('app/public/images/cluster_sheaves.jpeg') }}"  style="height:150px;"/>
            @endif
        </td>
        <td width="10%" class="text-center text-blue" rowspan="2" style="vertical-align: middle;">Fast Line<br>Sheave<br>Depth Check</td>
        <td width="40%" class="text-center">
            @if(file_exists(storage_path('app/public/images/cluster_sheaves.jpeg')))
                <img src="{{ storage_path('app/public/images/cluster_sheaves.jpeg') }}"  style="height:150px;"/>
            @endif
        </td>
    </tr>
    <tr>
        <td class="text-center">
            <b>Cluster Sheaves Groove Depth Check Photo</b><br>
            @if($record->cluster_photo && file_exists(storage_path('app/public/' . $record->cluster_photo)))
                <img src="{{ storage_path('app/public/' . $record->cluster_photo) }}" style="height:150px; border:1px solid #000;">
            @else
                <div style="height:150px; border:1px solid #000; padding-top: 60px; text-align:center;">Photo Not Available</div>
            @endif
        </td>
        <td class="text-center">
            <b>Fast Line Sheave Groove Depth Check Photo</b><br>
            @if($record->fast_line_photo && file_exists(storage_path('app/public/' . $record->fast_line_photo)))
                <img src="{{ storage_path('app/public/' . $record->fast_line_photo) }}" style="height:150px; border:1px solid #000;">
            @else
                <div style="height:150px; border:1px solid #000; padding-top: 60px; text-align:center;">Photo Not Available</div>
            @endif
        </td>
    </tr>
</table>
