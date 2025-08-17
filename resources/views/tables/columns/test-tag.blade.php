<div>

    @php

            $record = $getRecord();
            $companyNameAbbreviation = env('SLUG', 'TEST');
            $slug = $record->category->slug ?? 'N/A';
            $sequence = str_pad($record->sequence ?? 0, 2, '0', STR_PAD_LEFT);
            $test_tag = $companyNameAbbreviation . '-' . strtoupper($slug) . '-' . $sequence;
    @endphp

    <span>{{ $test_tag }}</span>

</div>
