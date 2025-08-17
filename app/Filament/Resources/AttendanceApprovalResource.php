<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AttendanceApprovalResource\Pages;
use App\Filament\Resources\AttendanceApprovalResource\RelationManagers\SheetsRelationManager;
use App\Models\AttendanceApproval;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\HeaderAction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Response;

use Mpdf\Mpdf;

class AttendanceApprovalResource extends Resource
{
    protected static ?string $model = AttendanceApproval::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Attendance Approvals';
    protected static ?string $pluralLabel = 'Attendance Approvals';

    protected static ?string $modelLabel = 'Attendance';
    protected static ?string $navigationGroup = 'HR Attendance';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Attendance Information')
                    ->schema([
                        Forms\Components\TextInput::make('period_label')
                            ->label('Period')
                            ->disabled(),
                        Forms\Components\TextInput::make('department')
                            ->disabled(),
                        Forms\Components\TextInput::make('status')
                            ->disabled(),
                    ]),

                Forms\Components\Section::make('Manager Approval')
                    ->schema([
                        Forms\Components\Toggle::make('manager_approval')
                            ->label('Approved')
                            ->disabled(function () {
                                return !Auth::user()->hasRole('manager');
                            }),
                        Forms\Components\DateTimePicker::make('manager_approved_at')
                            ->label('Approved Date')
                            ->disabled(),
                        Forms\Components\Textarea::make('manager_comments')
                            ->label('Comments')
                            ->disabled(function () {

                                return !Auth::user()->hasRole('manager');
                            }),
                    ]),

                Forms\Components\Section::make('HR Approval')
                    ->schema([
                        Forms\Components\Toggle::make('hr_approval')
                            ->label('Approved')
                            ->disabled(function () {
                                return !Auth::user()->hasRole('HR');
                            }),
                        Forms\Components\DateTimePicker::make('hr_approved_at')
                            ->label('Approved Date')
                            ->disabled(),
                        Forms\Components\Textarea::make('hr_comments')
                            ->label('Comments')
                            ->disabled(function () {
                                return !Auth::user()->hasRole('HR');
                            }),
                    ]),

                Forms\Components\Section::make('Accountant Approval')
                    ->schema([
                        Forms\Components\Toggle::make('accountant_approval')
                            ->label('Approved')
                            ->disabled(function () {
                                return !Auth::user()->hasRole('accountant');
                            }),
                        Forms\Components\DateTimePicker::make('accountant_approved_at')
                            ->label('Approved Date')
                            ->disabled(),
                        Forms\Components\Textarea::make('accountant_comments')
                            ->label('Comments')
                            ->disabled(function () {
                                return !Auth::user()->hasRole('accountant');
                            }),
                    ]),

                Forms\Components\Section::make('Managing Director Approval')
                    ->schema([
                        Forms\Components\Toggle::make('director_approval')
                            ->label('Approved')
                            ->disabled(function () {
                                return !Auth::user()->hasRole('director');
                            }),
                        Forms\Components\DateTimePicker::make('director_approved_at')
                            ->label('Approved Date')
                            ->disabled(),
                        Forms\Components\Textarea::make('director_comments')
                            ->label('Comments')
                            ->disabled(function () {
                                return !Auth::user()->hasRole('director');
                            }),
                    ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('period_label')
                    ->label('Period'),
                TextEntry::make('period_start')
                    ->label('From')
                    ->date(),
                TextEntry::make('period_end')
                    ->label('Until')
                    ->date(),
                TextEntry::make('department')
                    ->label('Department'),
                IconEntry::make('status')
                    ->label('Status')
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'heroicon-o-clock',
                        '1' => 'heroicon-o-exclamation-circle',
                        '2' => 'heroicon-o-check-circle',
                        '3' => 'heroicon-o-x-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'warning',
                        '1' => 'warning',
                        '2' => 'success',
                        '3' => 'danger',
                    })
                    ->tooltip(fn (string $state): string => match ($state) {
                        '0' => 'Pending',
                        '1' => 'In Progress',
                        '2' => 'Approved',
                        '3' => 'Rejected',
                    })
                    ->size('lg'),

            ])->columns(5);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('period_label')
                    ->label('Period')
                    ->searchable(),
                Tables\Columns\TextColumn::make('department')
                    ->searchable(),
                ColumnGroup::make('Manager', [
                    Tables\Columns\IconColumn::make('manager_approval')
                        ->boolean()
                        ->label('Status'),
                    Tables\Columns\TextColumn::make('manager_approved_at')
                        ->label('Date')
                        ->date()
                        ->sortable(),
                ]),
                ColumnGroup::make('HR', [
                    Tables\Columns\IconColumn::make('hr_approval')
                        ->boolean()
                        ->label('Status'),
                    Tables\Columns\TextColumn::make('hr_approved_at')
                        ->label('Date')
                        ->date()
                        ->sortable(),
                ]),

                ColumnGroup::make('Accountant', [
                    Tables\Columns\IconColumn::make('accountant_approval')
                        ->boolean()
                        ->label('Status'),
                    Tables\Columns\TextColumn::make('accountant_approved_at')
                        ->label('Date')
                        ->date()
                        ->sortable(),
                ]),
                ColumnGroup::make('Managing Director', [
                    Tables\Columns\IconColumn::make('director_approval')
                        ->boolean()
                        ->label('Status'),
                    Tables\Columns\TextColumn::make('director_approved_at')
                        ->label('Date')
                        ->date()
                        ->sortable(),
                ]),

                Tables\Columns\IconColumn::make('status')
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'heroicon-o-clock',
                        '1' => 'heroicon-o-exclamation-circle',
                        '2' => 'heroicon-o-check-circle',
                        '3' => 'heroicon-o-x-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'warning',
                        '1' => 'warning',
                        '2' => 'success',
                        '3' => 'danger',
                    }),

            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        '0' => 'Pending',
                        '1' => 'In Progress',
                        '2' => 'Approved',
                        '3' => 'Rejected',
                    ]),
            ])
            ->headerActions([
              Action::make('export_payroll_pdf')
        ->label('Export Payroll PDF')
        ->icon('heroicon-o-document-arrow-down')
        ->color('success')
        ->slideOver()
        ->form([
            Forms\Components\Select::make('period_label')
                ->label('Select Payroll Period')
                ->options(fn () => AttendanceApproval::where('status', '2')
                    ->distinct()
                    ->pluck('period_label', 'period_label')
                    ->toArray())
                ->required(),
        ])
        ->action(function (array $data) {
            $period = $data['period_label'];

            return redirect()->route('export.payroll.pdf', ['period' => $period]);
        })

            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->slideOver()
                    ->form([
                        Forms\Components\Textarea::make('comments')
                            ->label('Comments')
                            ->rows(4),
                        Forms\Components\Textarea::make('manager_comments')
                            ->visible(fn(AttendanceApproval $record) => $record->manager_approval && $record->manager_comments != '')
                            ->readOnly()
                            ->default(fn(AttendanceApproval $record) => $record->manager_comments)
                            ->readOnly(),

                        Forms\Components\Textarea::make('hr_comments')
                            ->visible(fn(AttendanceApproval $record) => $record->hr_approval && $record->hr_comments != '')
                            ->autosize()
                            ->default(fn(AttendanceApproval $record) => $record->hr_comments)
                            ->readOnly(),

                        Forms\Components\Textarea::make('accountant_comments')
                            ->visible(fn(AttendanceApproval $record) => $record->accountant_approval && $record->accountant_comments != '')
                            ->readOnly()
                            ->default(fn(AttendanceApproval $record) => $record->accountant_comments)
                            ->readOnly(),

                    ])
                    ->visible(fn (AttendanceApproval $record): bool => match (true) {

                        Auth::user()->hasRole('department_head') && !$record->manager_approval => true && $record->user_id == Auth::id() ,
                        Auth::user()->hasRole('HR') && $record->manager_approval && !$record->hr_approval => true,
                        Auth::user()->hasRole('accountant') && $record->hr_approval && !$record->accountant_approval => true,
                        Auth::user()->hasRole('director') && $record->accountant_approval && !$record->director_approval => true,
                        default => false,
                    })
                    ->action(function (AttendanceApproval $record, array $data) {
                        $user = Auth::user();

                        if ($user->hasRole('department_head') && !$record->manager_approval ) {
                            $record->manager_approval = true;
                            $record->manager_approved_at = now();
                            $record->manager_comments = $data['comments'];
                            $record->status = '1';
                        } elseif ($user->hasRole('HR') && !$record->hr_approval) {
                            $record->hr_approval = true;
                            $record->hr_approved_at = now();
                            $record->hr_comments = $data['comments'];
                            $record->status = '1';
                        } elseif ($user->hasRole('accountant') && !$record->accountant_approval) {
                            $record->accountant_approval = true;
                            $record->accountant_approved_at = now();
                            $record->accountant_comments = $data['comments'];
                            $record->status = '1';
                        } elseif ($user->hasRole('director') && !$record->director_approval) {
                            $record->director_approval = true;
                            $record->director_approved_at = now();
                            $record->director_comments = $data['comments'];
                            $record->status = '2'; // approved
                        }

                        $record->save();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->slideOver()
                    ->form([
                        Forms\Components\Textarea::make('comments')
                            ->label('Rejection Reason')
                            ->required()
                            ->rows(4),
                    ])
                    ->visible(fn (AttendanceApproval $record): bool => match (true) {
                        Auth::user()->hasRole('department_head') && !$record->manager_approval => true,
                        Auth::user()->hasRole('HR') && $record->manager_approval && !$record->hr_approval => true,
                        Auth::user()->hasRole('accountant') && $record->hr_approval && !$record->accountant_approval => true,
                        Auth::user()->hasRole('director') && $record->accountant_approval && !$record->director_approval => true,
                        default => false,
                    })
                    ->action(function (AttendanceApproval $record, array $data) {
                        $user = Auth::user();

                        // You can route all comments into director_comments, etc.
                        if ($user->hasRole('department_head')) {
                            $record->manager_comments = $data['comments'];
                        } elseif ($user->hasRole('HR')) {
                            $record->hr_comments = $data['comments'];
                        } elseif ($user->hasRole('accountant')) {
                            $record->accountant_comments = $data['comments'];
                        } elseif ($user->hasRole('director')) {
                            $record->director_comments = $data['comments'];
                        }

                        $record->status = '3'; // rejected
                        $record->save();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
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
            SheetsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAttendanceApprovals::route('/'),
            'create' => Pages\CreateAttendanceApproval::route('/create'),
            'edit' => Pages\EditAttendanceApproval::route('/{record}/edit'),
            'view' => Pages\ViewAttendanceApproval::route('/{record}'),
        ];
    }
}
