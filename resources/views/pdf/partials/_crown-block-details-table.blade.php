<table class="bordered" style="font-size: 6.5pt;">
    <tbody>
    <tr>
        <td width="12%" class="bg-grey">Certificate No:</td>
        <td width="20%">{{ $cert_name }}</td>
        <td width="15%" class="bg-grey">Client Name:</td>
        <td width="22%">{{ $record->customer->name ?? 'N/A' }}</td>
        <td width="15%" class="bg-grey">Work Location:</td>
        <td width="16%">{{ $record->work_location }}</td>
    </tr>
    <tr>
        <td class="bg-grey">Work Order No:</td>
        <td>{{ $record->workOrder->first()->wo_name ?? $record->wo_id }}</td>
        <td class="bg-grey">Inspection Date:</td>
        <td>{{ \Carbon\Carbon::parse($record->test_date)->format('F j, Y') }}</td>
        <td class="bg-grey">Next Inspection:</td>
        <td>{{ $record->status == '1' && $record->next_test_date ? \Carbon\Carbon::parse($record->next_test_date)->format('F j, Y') : 'N/A' }}</td>
    </tr>
    <tr>
        <td class="bg-grey">Specification:</td>
        <td>
            @if($record->standard_type_api == 1) @svg('heroicon-o-check', 'icon-check') API: {{ $record->standard_name_api }} @else @svg('heroicon-o-x-mark', 'icon-sign') API: @endif <br>
            @if($record->standard_type_astm == 1) @svg('heroicon-o-check', 'icon-check') ASTM: {{ $record->standard_name_astm }} @else @svg('heroicon-o-x-mark', 'icon-sign') ASTM: @endif
        </td>
        <td class="bg-grey">Insp. Method:</td>
        <td>
            @if($record->inspection_method == 1) @svg('heroicon-o-check', 'icon-check') Visible @else @svg('heroicon-o-x-mark', 'icon-sign') Visible @endif
            @if($record->inspection_method == 2) @svg('heroicon-o-check', 'icon-check') Flourescent @else @svg('heroicon-o-x-mark', 'icon-sign') Flourescent @endif
        </td>
        <td class="bg-grey">MPI Type:</td>
        <td>
            @if($record->mpi_type == 1) @svg('heroicon-o-check', 'icon-check') Wet @else @svg('heroicon-o-x-mark', 'icon-sign') Wet @endif
            @if($record->mpi_type == 2) @svg('heroicon-o-check', 'icon-check') Dry @else @svg('heroicon-o-x-mark', 'icon-sign') Dry @endif
        </td>
    </tr>
    <tr>
        <td class="bg-grey">Magnetization Eq:</td>
        <td>
            @if($record->mg_eq_used == 1) @svg('heroicon-o-check', 'icon-check') AC @else @svg('heroicon-o-x-mark', 'icon-sign') AC @endif
            @if($record->mg_eq_used == 2) @svg('heroicon-o-check', 'icon-check') DC @else @svg('heroicon-o-x-mark', 'icon-sign') DC @endif
            @if($record->mg_eq_used == 3) @svg('heroicon-o-check', 'icon-check') Permanent @else @svg('heroicon-o-x-mark', 'icon-sign') Permanent @endif
        </td>
        <td class="bg-grey">Manufacturer:</td>
        <td>{{ $record->mg_eq_manuf }}</td>
        <td class="bg-grey">Magnet No:</td>
        <td>{{ $record->magnet_no }}</td>
    </tr>
    <tr>
        <td class="bg-grey">Cal. Test Weight:</td>
        <td>
            @if($record->cal_test_weight == 1) @svg('heroicon-o-check', 'icon-check') 40 LBS @else @svg('heroicon-o-x-mark', 'icon-sign') 40 LBS @endif
            @if($record->cal_test_weight == 2) @svg('heroicon-o-check', 'icon-check') 10 LBS @else @svg('heroicon-o-x-mark', 'icon-sign') 10 LBS @endif
        </td>
        <td class="bg-grey">Pole Spacing:</td>
        <td>{{ $record->pole_spacing }}-150mm</td>
        <td class="bg-grey">Light Intensity:</td>
        <td>{{ $record->light_intensity_value }}</td>
    </tr>
    <tr>
        <td class="bg-grey">Surface Condition:</td>
        <td>{{ $record->surface_condition }}</td>
        <td class="bg-grey">Temperature:</td>
        <td>{{ $record->temprature }} C</td>
        <td class="bg-grey">Light Meter No:</td>
        <td>{{ $record->light_meter_no }}</td>
    </tr>
    <tr>
        <td class="bg-grey">Contrast Media:</td>
        <td>{{ $record->contrast_media }}</td>
        <td class="bg-grey">Batch No:</td>
        <td>{{ $record->contrast_media_batch }}</td>
        <td class="bg-grey">Manufacturer:</td>
        <td colspan="1">{{ $record->contrast_media_manuf }}</td>
    </tr>
    <tr>
        <td class="bg-grey">Indicator:</td>
        <td>{{ $record->indicator }}</td>
        <td class="bg-grey">Batch No:</td>
        <td>{{ $record->indicator_batch }}</td>
        <td class="bg-grey">Manufacturer:</td>
        <td colspan="1">{{ $record->indicator_manuf }}</td>
    </tr>
    <tr>
        <td class="bg-grey">Description:</td>
        <td colspan="5">{{ $record->description }}</td>
    </tr>
    <tr>
        <td class="bg-grey">Manufacturer:</td>
        <td>{{ $record->manufacturer }}</td>
        <td class="bg-grey">Manuf. Date:</td>
        <td>{{ \Carbon\Carbon::parse($record->manuf_date)->format('F j, Y') }}</td>
        <td class="bg-grey">Model:</td>
        <td>{{ $record->model }}</td>
    </tr>
    <tr>
        <td class="bg-grey">Sheaves OD:</td>
        <td>{{ $record->sheaves_od }} mm</td>
        <td class="bg-grey">Drill Line Dia.:</td>
        <td>{{ $record->drill_line_dia }} mm</td>
        <td class="bg-grey">Rated Loading:</td>
        <td>{{ $record->rated_loading }} KN</td>
    </tr>
    <tr>
        <td class="bg-grey">Equipment No:</td>
        <td>{{ $record->equipment_no }}</td>
        <td class="bg-grey">F/L Sheave SN:</td>
        <td>{{ $record->fl_sheave_sn }}</td>
        <td class="bg-grey">Sand-Line Sheave SN:</td>
        <td>{{ $record->sand_line_sheave_sn ?? 'N/A' }}</td>
    </tr>
    <tr>
        <td class="bg-grey">Cluster Sheaves SN:</td>
        <td colspan="5">{{ $record->cluster_sheaves_sn }}</td>
    </tr>
    </tbody>
</table>
