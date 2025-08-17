<x-layouts.pdf :record="$record">
    <x-slot:title>{{ $cert_name }}</x-slot:title>

    <h2>TRAVELING BLOCK INSPECTION REPORT</h2>

    @include('pdf.partials._traveling-block-details-table')

    <table>
        <tr>
            <td style="border:none; width: 50%; text-align:center;">
                <img src="{{ storage_path('app/public/images/traveling.jpeg') }}" style="height:200px; border:1px solid #000;">
            </td>
            <td style="border:none; width: 50%; text-align:center;">
                @if($record->traveling_photo && file_exists(storage_path('app/public/' . $record->traveling_photo)))
                    <img src="{{ storage_path('app/public/' . $record->traveling_photo) }}" style="height:200px; border:1px solid #000;">
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

    <x-pdf.signature-block :record="$record" :isDraft="$isDraft" />

    <div class="page-break"></div>
    @include('pdf.partials._traveling-block-checklist')

    <div class="page-break"></div>
    @include('pdf.partials._traveling-block-readings')

    @if($record->photos->isNotEmpty())
        @foreach($record->photos->chunk(12) as $photosOnPage)
            <div class="page-break"></div>
            <h2>TRAVELING BLOCK INSPECTION REPORT</h2>
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

            @if($loop->last)
                <x-pdf.signature-block :record="$record" :isDraft="$isDraft" />
            @endif
        @endforeach
    @endif
</x-layouts.pdf>
