<?php

namespace App\Filament\Resources\AttendanceSheetResource\Pages;

use App\Filament\Resources\AttendanceSheetResource;
use App\Models\AttendanceApproval;
use App\Models\AttendanceSheet;
use App\Models\LeaveOrders;
use App\Models\PublicHoliday;
use App\Models\User;
use App\Models\UserDetails;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance as Fingerprint;

class MonthlyAttendanceSheet extends Page
{

    protected static string $resource = AttendanceSheetResource::class;

    protected static string $view = 'filament.resources.attendance-sheet-resource.pages.monthly-attendance-sheet';

    public ?array $data = [];
    public $month;
    public $employees = [];
    public $attendanceData = [];
    public $daysInPeriod = [];

    public function mount(): void
    {
        $this->form->fill([
            'month' => now()->format('Y-m'),
        ]);
        $this->loadAttendanceData();
    }

    public function initializeForm(): void
    {
        $this->form->fill();
        $this->loadAttendanceData();
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
            ->schema([
                DatePicker::make('period_start')
                    ->label('Start Date')
                    ->default(now()->subMonth()->setDay(21))
                    ->displayFormat('d F')
                    ->reactive()
                    ->afterStateUpdated(fn () => $this->loadAttendanceData())
                    ->required()
                    ->native(false),

                DatePicker::make('period_end')
                    ->label('End Date')
                    ->default(now()->setDay(20))
                    ->displayFormat('d F')
                    ->reactive()
                    ->afterStateUpdated(fn () => $this->loadAttendanceData())
                    ->required()
                    ->native(false),
            ]);
    }

    public function loadAttendanceData(): void
    {
        // Get period
        $state = $this->form->getState();
        $startDate = Carbon::parse($state['period_start'])->startOfDay();
        $endDate   = Carbon::parse($state['period_end'])->startOfDay();
        // Build header days
        $daysInPeriod = $startDate->diffInDays($endDate) + 1;
        $this->daysInPeriod = [];
        for ($i = 0; $i < $daysInPeriod; $i++) {
            $date = $startDate->copy()->addDays($i);
            $this->daysInPeriod[] = [
                'day'       => $i + 1,
                'date'      => $date->format('Y-m-d'),
                'dayOfWeek' => $date->format('D'),
                'dayOfMonth'=> $date->day,
                'month'     => $date->format('M'),
            ];
        }
        // Fetch public holidays in this range
        $holidayModels = PublicHoliday::query()
            ->where(function($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date',   [$startDate, $endDate])
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('start_date', '<=', $startDate)
                            ->where('end_date',   '>=', $endDate);
                    });
            })
            ->get();
        $publicHolidayDates = [];
        foreach ($holidayModels as $holiday) {
            $from = Carbon::parse($holiday->start_date);
            $to   = Carbon::parse($holiday->end_date ?? $holiday->start_date);
            for ($d = $from->copy(); $d->lte($to); $d->addDay()) {
                $publicHolidayDates[$d->format('Y-m-d')] = $holiday->name;
            }
        }
        $query = User::with('userDetails')
            ->where('enabled', '1');

        if (! Auth::user()->hasRole('super_admin') && Auth::user()->hasRole('department_head') && !Auth::user()->hasRole('management')) {
            $supervisorId = Auth::id();
            $query->where(function (Builder $q) use ($supervisorId) {
                $q->whereHas('userDetails', function (Builder $q2) use ($supervisorId) {
                        $q2->where('sup_emp_id', $supervisorId);
                    })->orWhere('user_id', $supervisorId);
            });
        }

        if (Auth::user()->hasRole('management')) {

            $user_ids = ['2','17','17', '207', '210'];
            $query->where(function (Builder $q) use ($user_ids) {
                $q->whereHas('userDetails', function (Builder $q2) use ($user_ids) {
                    $q2->whereIn('user_id', $user_ids);
                });
            });
         }

        $this->employees = $query->get();



        $this->attendanceData = [];
        foreach ($this->employees as $employee) {
            $fingerprints = Fingerprint::where('user_id', $employee->user_id)
                ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
                ->get()
                ->keyBy(fn($row) => $row->date->format('Y-m-d'));
            $sheet = AttendanceSheet::where('user_id', $employee->id)
                ->where('period_start', $startDate->format('Y-m-d'))
                ->where('period_end',   $endDate->format('Y-m-d'))
                ->first();
            $leaves = LeaveOrders::with('leaveType')
                ->where('emp_id', $employee->user_id)
                ->where('leave_status', '1')
                ->where(function($q) use ($startDate, $endDate) {
                    $q->whereBetween('start_date', [$startDate, $endDate])
                        ->orWhereBetween('end_date',   [$startDate, $endDate])
                        ->orWhere(function($q2) use ($startDate, $endDate) {
                            $q2->where('start_date', '<=', $startDate)
                                ->where('end_date',   '>=', $endDate);
                        });
                })
                ->get();
            $employeeAttendance = [];
            for ($dayIndex = 1; $dayIndex <= $daysInPeriod; $dayIndex++) {
                $date = $startDate->copy()->addDays($dayIndex - 1);
                $key  = $date->format('Y-m-d');
                $dow  = $date->format('D');

                // Already in sheet?
                if ($sheet && isset($sheet->attendance_data["day_{$dayIndex}"])) {
                    $status = $sheet->attendance_data["day_{$dayIndex}"];

                    // Public holiday override
                } elseif (isset($publicHolidayDates[$key])) {
                    $status = 'PH';

                    // Weekend
                } elseif (in_array($dow, ['Fri', 'Sat'])) {
                    $status = 'DO';

                } else {
                    // Check leave
                    $onLeave = $leaves->first(function($leave) use ($date) {
                        return $date->between($leave->start_date, $leave->end_date);
                    });
                    if ($onLeave) {
                        $status = $onLeave->leaveType->slug;
                    } else {
                        // Fingerprint check
                        $fp = $fingerprints->get($key);
                        if ( empty($fp->clock_in) && empty($fp->clock_out))
                        {
                            $status = 'X';
                        } else if ( empty($fp->clock_in) || empty($fp->clock_out))
                        {
                            $status = 'CK';
                        }
                        else
                        {
                            $status = 'A';
                        }

                    }
                }
                $employeeAttendance[$dayIndex] = $status;
            }
            $this->attendanceData[$employee->user_id] = [
                'employee'   => $employee->toArray(),
                'attendance' => $employeeAttendance,
                'totals'     => $sheet ? [
                    'present' => $sheet->present_count,
                    'absent'  => $sheet->absent_count,
                    'annual'  => $sheet->annual_leave_count,
                    'sick'    => $sheet->sick_leave_count,
                    'public'  => $sheet->public_holiday_count,
                    'unpaid'  => $sheet->unpaid_leave_count,
                ] : array_fill_keys(['present','absent','annual','sick','public','unpaid'], 0),
            ];
        }
    }

    public function saveAttendance(): void
    {
        $state = $this->form->getState();
        $startDate = Carbon::parse($state['period_start'])->startOfDay();
        $endDate   = Carbon::parse($state['period_end'])->startOfDay();

        $daysInPeriod = $startDate->diffInDays($endDate) + 1;


        foreach ($this->attendanceData as $employeeId => $data) {
            $present = $absent = $annual = $sick = $emergency = $public = $unpaid = 0;

            for ($i = 1; $i <= $daysInPeriod; $i++) {;
                $status = $data['attendance'][$i] ?? 'A';
                $attendanceData["day_{$i}"] = $status;

                switch ($status) {
                    case 'A':
                        $present++;
                        break;
                    case '-':
                    case 'X':
                        $absent++;
                        break;
                    case 'AL':
                        $annual++;
                        break;
                    case 'SL':
                        $sick++;
                        break;
                    case 'EL':
                        $emergency++;
                        break;
                    case 'PH':
                        $public++;
                        break;
                    case 'UL':
                        $unpaid++;
                        break;
                    case 'DO':
                        break;
                }
            }

            $approval = AttendanceApproval::firstOrCreate(
                [
                    'user_id' => Auth::id() ,
                    'department' => UserDetails::find(Auth::id())->department->department_name_en ,
                    'period_start'  => $startDate->toDateString(),
                    'period_end'    => $endDate->toDateString(),
                ],
                [
                    'period_label' => $endDate->format('F').' – Payroll',
                    'status'       => 0,
                ]
            );

            AttendanceSheet::updateOrCreate(
                [
                    'approval_id'     => $approval->id,
                    'user_id'     => $data['employee']['user_id'],
                    'department_id' => UserDetails::find($data['employee']['user_id'])->department_id,
                    'period_start'=> $startDate->format('Y-m-d'),
                    'period_end'  => $endDate->format('Y-m-d'),
                ],
                [
                    'period_label'         => Carbon::parse($endDate)->format('F').' - Payroll',
                    'attendance_data'      => $attendanceData,
                    'present_count'        => $present,
                    'absent_count'         => $absent,
                    'annual_leave_count'   => $annual,
                    'sick_leave_count'     => $sick,
                    'emergency_leave_count'=> $emergency,
                    'public_holiday_count' => $public,
                    'unpaid_leave_count'   => $unpaid,
                ]
            );
        }

        Notification::make()
            ->title('Attendance data saved successfully!')
            ->success()
            ->send();

        $this->loadAttendanceData();
    }
}
