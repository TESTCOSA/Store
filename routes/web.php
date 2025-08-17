 <?php
use App\Mail\ApprovedRequest;

 use App\Models\AttendanceApproval;
 use App\Models\Category;
use App\Models\Item;
use App\Models\Stock;
use App\Models\StockOut;
use App\Models\UserDetails;
use Filament\Actions\Exports\Models\Export;
// use Filament\Notifications\Notification;
use Illuminate\Support\Arr;
 use Illuminate\Support\Facades\Artisan;
 use Illuminate\Support\Facades\Auth;
 use Illuminate\Support\Facades\DB;
 use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Notifications\RequestApprovalNotification;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

 use Mpdf\Mpdf;



    Route::get('/clear-cache', function() {
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
           Artisan::call('route:clear');
        return 'Cache cleared';
    });

  Route::get('/', function() {
    return redirect('/app');
});





    Route::get('/clear-route', function() {
        Artisan::call('route:clear');
        Artisan::call('config:cache');
        return 'Route cleared';
    });


    Route::get('/c', function() {

        Artisan::call('storage:link');
        return Artisan::output();
    });
       Route::get('/up', function() {

        Artisan::call('up');
        return Artisan::output();
    });





Route::get('/export-payroll-pdf/{period}', function ($period) {
    if (!Auth::check() || !Auth::user()->hasAnyRole(['accountant', 'super_admin', 'HR'])) {
        abort(403, 'Unauthorized action.');
    }

    $approvedRecords = AttendanceApproval::where('period_label', $period)
        ->where('status', '2')
        ->with('sheets.userDetails')
        ->get();

    foreach ($approvedRecords as $record) {
        foreach ($record->sheets as $sheet) {
            $name = $sheet->userDetails->full_name_en ?? '';
            if (!mb_check_encoding($name, 'UTF-8')) {
                $sheet->userDetails->full_name_en = mb_convert_encoding($name, 'UTF-8', 'auto');
            }
        }
    }

    $generatedAt = now();

    $html = view('pdf.payroll', [
        'period' => $period,
        'records' => $approvedRecords,
        'generatedAt' => $generatedAt,
    ])->render();

    $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4']);
    $mpdf->WriteHTML($html);


    return response(
        $mpdf->Output("{$generatedAt->format('Y')}-{$period}-payroll.pdf", \Mpdf\Output\Destination::STRING_RETURN),
        200,
        [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename=" ' . $generatedAt->format('Y') .'-'. $period . '-payroll.pdf"',
        ]
    );
})->middleware('auth')->name('export.payroll.pdf');







 Route::get('/fix', function() {

    Artisan::call('optimize');

    $redirectUrl = 'https://test-demos.site/app/statistics';
    return response()->make('
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Now Fixed</title>
            <!-- Optional meta refresh if JavaScript is disabled -->
            <meta http-equiv="refresh" content="3;url=' . $redirectUrl . '">
            <script>
                // Set the initial countdown value
                let seconds = 3;
                function countdown() {
                    if (seconds <= 0) {
                        // Redirect when countdown is done
                        window.location.href = "' . $redirectUrl . '";
                    } else {
                        // Update the countdown display
                        document.getElementById("countdown").innerHTML = seconds;
                        seconds--;
                        // Call the function again every 1000ms (1 second)
                        setTimeout(countdown, 1000);
                    }
                }
                // Start the countdown when the page loads
                window.onload = countdown;
            </script>
        </head>
        <body>
            <h1>.....Now Fixed</h1>
            <p>Redirecting in <span id="countdown">3</span> seconds...</p>
        </body>
        </html>
    ', 200, ['Content-Type' => 'text/html']);
});
