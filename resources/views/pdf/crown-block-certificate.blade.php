<x-layouts.pdf :record="$record" :isDraft="$isDraft">
    <x-slot:title>{{ $cert_name }}</x-slot:title>

    {{-- PAGE 1: Main Report --}}
    <h2>CROWN BLOCK INSPECTION REPORT</h2>

    {{-- Main Details Table --}}
    @include('pdf.partials._crown-block-details-table')

    {{-- Main Photo and Results --}}
    <table>
        <tr>
            <td style="border:none; width: 50%; text-align:center;">
                <img src="{{ storage_path('app/public/images/crown.jpeg') }}" style="height:200px; border:1px solid #000;">
            </td>
            <td style="border:none; width: 50%; text-align:center;">
                @if($record->crown_photo && file_exists(storage_path('app/public/' . $record->crown_photo)))
                    <img src="{{ storage_path('app/public/' . $record->crown_photo) }}" style="height:200px; border:1px solid #000;">
                @else
                    <div style="height:200px; border:1px solid #000; padding-top: 90px; text-align:center;">Photo Not Available</div>
                @endif
            </td>
        </tr>
    </table>
    <br>
    <table class="bordered">
        <tr>
            <td width="50%">
                <b style="color: blue;">Dimensional Result:</b>
                <p>{!! $record->status == '1' ? str_replace('(ACCEPTED)', '<span style="color:green;">(ACCEPTED)</span>', $record->dim_results) : str_replace('(REJECTED)', '<span style="color:red;">(REJECTED)</span>', $record->dim_results) !!}</p>
            </td>
            <td width="50%">
                <b style="color: blue;">MPI Result:</b>
                <p>{!! $record->status == '1' ? str_replace('(ACCEPTED)', '<span style="color:green;">(ACCEPTED)</span>', $record->mpi_results) : str_replace('(REJECTED)', '<span style="color:red;">(REJECTED)</span>', $record->mpi_results) !!}</p>
            </td>
        </tr>
    </table>


    {{-- PAGE 2 & 3: Checklist and Readings (as partials) --}}
    <div class="page-break"></div>
    @include('pdf.partials._crown-block-checklist')

    <div class="page-break"></div>
    @include('pdf.partials._crown-block-readings')

    {{-- PAGE 4+: Photo Annex --}}
    @if($record->photos->isNotEmpty())
        @foreach($record->photos->chunk(12) as $photosOnPage)
            <div class="page-break"></div>
            <h2>CROWN BLOCK INSPECTION REPORT</h2>
            <h3>ANNEX TO REPORT NO: {{ $cert_name }}</h3>
            <br>
            <table style="width: 100%; border-collapse: separate; border-spacing: 10px;">
                @foreach($photosOnPage->chunk(3) as $row)
                    <tr>
                        @foreach($row as $photo)
                            <td class="photo-cell">
                                <img src="{{ storage_path('app/public/' . $photo->file_name) }}" class="photo-img">
                            </td>
                        @endforeach
                        @for($i = $row->count(); $i < 3; $i++)
                            <td style="border: none;"></td>
                        @endfor
                    </tr>
                @endforeach
            </table>


        @endforeach
    @endif

</x-layouts.pdf>
