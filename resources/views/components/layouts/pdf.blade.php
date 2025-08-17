<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Inspection Report' }}</title>
    <style>
        @page {

            margin: 30mm 7mm 40mm 7mm; /* Set margins: top, right, bottom, left */

            header: page-header;

            footer: page-footer;

        }

        body { font-family: 'dejavusans', 'sans-serif'; font-size: 7pt; color: #333; }

        table { border-collapse: collapse; width: 100%; }

        .bordered, .bordered td, .bordered th { border: 1px solid #333; }

        td, th { padding: 3px; vertical-align: middle; word-wrap: break-word; }

        .bg-grey { background-color: #eeeeee; font-weight: bold; }

        .text-center { text-align: center; }

        .text-left { text-align: left; }

        .text-right { text-align: right; }

        .text-green { color: green; }

        .text-red { color: red; }

        .text-blue { color: blue; }

        h2 { font-size: 13pt; background-color:#ca182c; color:#fff; text-align:center; padding: 5px; margin-bottom: 5px; }

        h3 { text-decoration: underline; font-size: 11pt; text-align: center; margin-top: 15px; margin-bottom: 10px;}

        .page-break { page-break-after: always; }

        .signature { height: 30px; }

        .disclaimer { font-size: 8px; line-height: 1.4; text-align: justify; padding: 5px 0; }

        .footer-bar { background-color:#ca182c; color:#fff; font-size:9px; font-weight:bold; padding: 4px; text-align: center; }


        /* --- Blade Icon Styles --- */

        .icon-check { width: 10px; height: 10px; }

        .icon-sign { width: 10px; height: 10px; }

        .icon-pass { width: 12px; height: 12px; color: green; }

        .icon-fail { width: 12px; height: 12px; color: red; }

        .icon-na { width: 12px; height: 12px; color: #555; }


        .photo-cell {

            width: 33.33%;

            height: 200px; /* <-- Force a fixed height for every photo container */

            border: 1px solid #333;

            padding: 5px;

            text-align: center;

            vertical-align: middle;

        }

        .photo-img {

            max-width: 100%; /*<-- Ensure image doesn't overflow width */

            max-height: 100%; /*<-- Ensure image doesn't overflow height */

            height: auto;

            width: auto;

        }
    </style>
</head>
<body>

{{-- Define the header and footer for mPDF --}}
<htmlpageheader name="page-header">
    <x-pdf.header />
</htmlpageheader>

<htmlpagefooter name="page-footer">
    <x-pdf.signature-block :record="$record" :isDraft="$isDraft" />
    <x-pdf.footer :record="$record" />
</htmlpagefooter>

{{-- This is where the main content of each specific report will go --}}
<main>
    {{ $slot }}
</main>

</body>
</html>
