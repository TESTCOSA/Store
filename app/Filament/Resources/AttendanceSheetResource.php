<?php
namespace App\Filament\Resources;


use App\Filament\Resources\AttendanceSheetResource\Pages;

use App\Filament\Resources\AttendanceSheetResource\RelationManagers;

use App\Models\AttendanceSheet;

use App\Models\User;

use Filament\Forms;

use Filament\Forms\Form;

use Filament\Forms\Get;
use Filament\Forms\Set;

use Filament\Resources\Resource;

use Filament\Tables;

use Filament\Tables\Table;

use Illuminate\Database\Eloquent\Builder;

use Illuminate\Database\Eloquent\SoftDeletingScope;

use Illuminate\Support\Carbon;

use Illuminate\Support\Facades\Auth;


class AttendanceSheetResource extends Resource

{

    protected static ?string $model = AttendanceSheet::class;
    protected static ?string $navigationLabel = 'Attendance Management';
    protected static ?string $navigationGroup = 'HR Attendance';
    protected static ?int $navigationSort = 1;





    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
// If user is not an admin, only show employees under them
        if (!Auth::user()->hasRole('admin') && !Auth::user()->hasRole('management')) {
            $supervisorId = Auth::id();
            return $query?->whereHas('user.userDetails', function ($query) use ($supervisorId) {
                $query?->where('sup_emp_id', $supervisorId);
            })->orWhere('user_id', Auth::id());
        }

        if (Auth::user()->hasRole('management')) {

            $user_ids = ['2','17','17', '207', '210'];
            $query->where(function (Builder $q) use ($user_ids) {
                $q->whereHas('userDetails', function (Builder $q2) use ($user_ids) {
                    $q2->whereIn('user_id', $user_ids);
                });
            });
        }

        return $query;

    }



    public static function form(Form $form): Form

    {

        return $form

            ->schema([

                Forms\Components\Section::make('Attendance Information')

                    ->schema([

                        Forms\Components\DatePicker::make('period_start')

                            ->label('Start Date')
                            ->readOnly()
                            ->displayFormat('F'),

                        Forms\Components\DatePicker::make('period_end')
                            ->label('End Date')
                            ->readOnly()
                            ->displayFormat('F'),


                        Forms\Components\Select::make('user_id')
                            ->label('Name')
                            ->options(function () {
                                $currentUserId = Auth::id();
                                $employeeQuery = User::with('userDetails')->where('enabled', '1');
                                if (!Auth::user()->hasRole('super_admin')) {
                                    $employeeQuery->whereHas('userDetails', function ($query) use ($currentUserId) {
                                        $query->where('sup_emp_id', $currentUserId)
                                            ->orWhere('user_id', $currentUserId);
                                    });
                                }
                                return $employeeQuery->get()->mapWithKeys(fn ($user) => [
                                    $user->user_id => $user->userDetails->full_name_en ?? $user->username
                                ]);
                            })
                            ->native(false)
                            ->disabled(),

                    ])->columns(3)
                    ->columnSpan('full'),

                Forms\Components\Section::make('Notes')
                ->schema([
                    Forms\Components\Textarea::make('notes')
                        ->autosize(5)
                    ,])
                    ->columns(1)
                    ->columnSpan('full'),


                Forms\Components\Section::make('Daily Attendance')
                    ->label(fn (Get $get) => User::find($get('user_id'))?->userDetails->full_name_en ?? 'Daily Attendance')
                    ->schema([
                        Forms\Components\Toggle::make('mark_all_present')
                            ->label('Mark All Absent to Present')
                            ->live()
                            ->afterStateUpdated(function (\Filament\Forms\Get $get, \Filament\Forms\Set $set, $state) {
                                if ($state) {
                                    $attendanceData = $get('attendance_data');

                                    foreach ($attendanceData as $key => $value) {
                                        // Only update days that are marked as 'X' (Absent)
                                        if (str_starts_with($key, 'day_') && $value === 'X') {
                                            $set("attendance_data.{$key}", 'A');
                                        }
                                    }
                                }
                            }),
                        Forms\Components\Grid::make()

                            ->schema(function (\Filament\Forms\Get $get) {

                                $periodStart = $get('period_start');

                                $periodEnd = $get('period_end');


                                if (!$periodStart || !$periodEnd) {

                                    return [];

                                }


                                $startDate = Carbon::parse($periodStart);

                                $endDate = Carbon::parse($periodEnd);

                                $daysInPeriod = $startDate->diffInDays($endDate) + 1;


                                $daysFields = [];

                                for ($day = 0; $day < $daysInPeriod; $day++) {

                                    $date = $startDate->copy()->addDays($day);

                                    $dayOfWeek = $date->format('D');

                                    $dateFormatted = $date->format('M d');

                                    $dayNumber = $day + 1;


                                    $daysFields[] = Forms\Components\Select::make("day_{$dayNumber}")

                                        ->label("{$dayOfWeek} {$dateFormatted}")

                                        ->options([

                                            'A' => 'Present (A)',
                                            'X' => 'Absent (X)',
                                            'CK' => 'No Check In or Out (CK)',

                                            'AL' => 'Annual Leave (AL)',

                                            'DO' => 'Day Off (DO)',

                                            'SL' => 'Sick Leave (SL)',
                                            'PH' => 'Public Holiday (PH)',

                                            'UL' => 'Unpaid Leave (UL)',



                                        ])

                                        ->default(fn () => in_array($dayOfWeek, ['Fri', 'Sat']) ? 'PH' : 'A')

                                        ->live()

                                        ->required()

                                        ->columnSpan(1)

                                        ->extraAttributes(function ($state) {

                                            return $state === 'PH' ? ['class' => 'ph-option'] : [];

                                        });

                                }

                                return $daysFields;

                            })

                            ->statePath('attendance_data')

                            ->columns(7)

                    ])

                    ->columnSpan('full'),

            ]);

    }


    public static function table(Table $table): Table

    {

        return $table

            ->columns([

                Tables\Columns\TextColumn::make('user.userDetails.full_name_en')

                    ->label('Employee')

                    ->searchable()

                    ->sortable(),


                Tables\Columns\TextColumn::make('user.userDetails.emp_code')

                    ->label('Employee Code')

                    ->searchable()

                    ->sortable(),


                Tables\Columns\TextColumn::make('period_label')

                    ->label('Month')


                    ->searchable()

                    ->sortable(),


                Tables\Columns\TextColumn::make('department.name')

                    ->label('Department')

                    ->searchable()

                    ->sortable(),


                Tables\Columns\TextColumn::make('present_count')

                    ->label('Present')

                    ->sortable(),


                Tables\Columns\TextColumn::make('absent_count')

                    ->label('Absent')

                    ->sortable(),


                Tables\Columns\TextColumn::make('annual_leave_count')

                    ->label('Annual Leave')

                    ->sortable(),


                Tables\Columns\TextColumn::make('sick_leave_count')

                    ->label('Sick Leave')

                    ->sortable(),

            ])

            ->filters([


                Tables\Filters\Filter::make('month')

                    ->form([

                        Forms\Components\DatePicker::make('month')

                            ->label('Month')

                            ->format('Y-m')

                            ->displayFormat('F Y'),

                    ])

                    ->query(function (Builder $query, array $data): Builder {

                        return $query

                            ->when(

                                $data['month'],

                                fn (Builder $query, $date): Builder => $query->whereMonth('month', Carbon::parse($date)->month)

                                    ->whereYear('month', Carbon::parse($date)->year),

                            );

                    }),

            ])

            ->actions([

                Tables\Actions\EditAction::make(),

                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('viewApproval')
                    ->label('View Approval')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->url(fn ($record) => $record->approval
                        ? AttendanceApprovalResource::getUrl('edit', ['record' => $record->approval->id])
                        : null)
                    ->visible(fn ($record) => $record->approval !== null),

            ])

            ->bulkActions([

                Tables\Actions\BulkActionGroup::make([

                    Tables\Actions\DeleteBulkAction::make(),

                ]),

            ]);

    }


    public static function getRelations(): array

    {

        return [

//

        ];

    }


    public static function getPages(): array

    {

        return [

            'index' => Pages\ListAttendanceSheets::route('/'),

            'create' => Pages\CreateAttendanceSheet::route('/create'),

            'edit' => Pages\EditAttendanceSheet::route('/{record}/edit'),

            'monthly-sheet' => Pages\MonthlyAttendanceSheet::route('/monthly-sheet'),


        ];

    }

}
