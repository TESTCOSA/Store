@props(['record'])


<div class="disclaimer bordered" style="font-size: 8px; line-height: 1.4; text-align: justify; padding: 5px 0;">

    No liability will be considered against TEST company for failure, damage or other occurrence to Client owned or operated equipment due to misuse, improper installations, poor maintenance, weather/environmental conditions & equipment operator/user mistakes or uncontrolled operation & maintenance. This Inspection Certificate/Report shall not be reproduced except in full without the approval of TEST Company and the Client.

</div>
<table style="margin-top: 5px;">
    <tbody>
    <tr class="bordered" style="font-size:10px;">
        <td class="text-center"><img src="{{storage_path('app/public/img/logos/3.jpg')}}" height="40"/></td>
        <td class="text-center"><img src="{{storage_path('app/public/img/logos/1.jpg')}}" height="40"/></td>
        <td class="text-center"><img src="{{storage_path('app/public/img/logos/4.jpg')}}" height="40"/></td>
        <td class="text-center"><img src="{{storage_path('app/public/img/logos/5.jpg')}}" height="40"/></td>
        <td class="text-center"><img src="{{storage_path('app/public/img/logos/2.jpg')}}" height="40"/></td>
        <td class="text-center"><img src="{{storage_path('app/public/img/logos/6.jpg')}}" height="40"/></td>
    </tr>
    </tbody>
</table>
<div class="footer-bar">
    Testing Equipment Specialist Team Company | P.O Box 4022, Dammam 34264, Saudi Arabia | +966 13 808 4277 | sales@testcosa.com | www.testcosa.com
</div>
<table width="100%">
    <tr>
        <td width="50%">TEST-QMS-P85I-NDT-002 Rev.{{ $record->rev_no ?? 0 }}</td>
        <td width="50%" class="text-right">Page {PAGENO} of {nbpg}</td>
    </tr>
</table>
