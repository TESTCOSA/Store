<?php

namespace App\Filament\Resources\CrownBlock;

use App\ChecklistRepeater;
use App\Models\CrownBlock\Main as CrownBlock;
use App\Models\CrownBlock\Checklist;
use App\Models\CrownBlock\ChecklistDetail;
use App\Models\CrownBlock\Photo;
use App\Models\CrownBlock\ReadingCL;
use App\Models\CrownBlock\ReadingFL;
use App\Models\StockOut;
use App\Models\WorkOrder;
use App\Services\CrownBlockCertificateService;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Resources\Resource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Str;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Get;
use Filament\Forms\Set;

class MainResource extends Resource
{
    protected static ?string $model = CrownBlock::class;

//    protected static ?string $navigationGroup = 'Drilling';
//    protected static ?string $navigationIcon = 'heroicon-o-cube';
//    protected static ?string $label = 'Crown Block';
    protected static ?string $pluralLabel = 'Crown Blocks Log';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        $items = \App\Models\ChecklistDetail::query()
            ->where('checklist_id', 212)
            ->orderBy('sortorder')
            ->get();

        return $form
            ->schema([
                Section::make('Certificate Information')
                    ->columns(4)
                    ->schema([

                        TextInput::make('work_location')->required(),
                        DatePicker::make('test_date')->required(),
                        DatePicker::make('next_test_date')->required(),

                        Select::make('status')
                            ->options([
                                '1' => 'Accepted',
                                '2' => 'Rejected',
                            ])
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (Set $set, $state) {
                                if ($state == '1') {
                                    $set('mpi_results', "CARRIED PUT CROWN BLOCK INSPECTION ON THE AVAILABLE & ACCESSIBLE CRITICAL AREA OF THE ABOVE DESCRIBED ITEM AND FOUND IT'S FREE FROM SURFACE CRACKS AT THE TIME OF INSPECTION. <br>(ACCEPTED)");
                                    $set('dim_results', "DIMENSIONS HAVE BEEN RECORDED, AND FOUND WITHIN ALLOWABLE WEAR TOLERANCE, VISUAL INSPECTION FOUND SATISFACTORY. <br>(ACCEPTED)");
                                } elseif ($state == '2') {
                                    $set('mpi_results', "CARRIED PUT CROWN BLOCK INSPECTION ON THE AVAILABLE & ACCESSIBLE CRITICAL AREA OF THE ABOVE DESCRIBED ITEM AND FOUND IT'S FREE FROM SURFACE CRACKS AT THE TIME OF INSPECTION. <br>(REJECTED)");
                                    $set('dim_results', "DIMENSIONS HAVE BEEN RECORDED, AND FOUND WITHIN ALLOWABLE WEAR TOLERANCE, VISUAL INSPECTION FOUND SATISFACTORY. <br>(REJECTED)");
                                }
                            }),
                        Checkbox::make('standard_type_api')->label('Specification API'),
                        TextInput::make('standard_name_api'),
                        Checkbox::make('standard_type_astm')->label('Specification ASTM'),
                        TextInput::make('standard_name_astm'),

                        Select::make('inspection_method')
                            ->options(['1' => 'Visible', '2' => 'Fluorescent'])
                            ->required(),

                        Select::make('insp_type')
                            ->options(['1' => 'Cat III', '2' => 'Cat IV'])
                            ->required(),

                        Select::make('mpi_type')
                            ->options(['1' => 'Wet', '2' => 'Dry'])
                            ->required(),

                        Select::make('mg_eq_used')
                            ->options(['1' => 'AC', '2' => 'DC', '3' => 'Permanent'])
                            ->required(),

                        TextInput::make('mg_eq_manuf')->required(),
                        TextInput::make('magnet_no')->required(),
                        DatePicker::make('manuf_date')->required(),
                        TextInput::make('model')->required(),
                        Textarea::make('description')->columnSpanFull(),
                        TextInput::make('manufacturer')->required(),

                        TextInput::make('sheaves_od'),
                        TextInput::make('drill_line_dia')->numeric()->required(),
                        TextInput::make('rated_loading')->required(),
                        TextInput::make('equipment_no')->required(),
                        TextInput::make('fl_sheave_sn')->required(),
                        TextInput::make('sand_line_sheave_sn'),
                        TextInput::make('cluster_sheaves_sn')->required(),

                        TextInput::make('contrast_media')->required(),
                        TextInput::make('contrast_media_batch')->required(),
                        TextInput::make('contrast_media_manuf')->required(),

                        TextInput::make('indicator')->required(),
                        TextInput::make('indicator_batch')->required(),
                        TextInput::make('indicator_manuf')->required(),

                        Select::make('cal_test_weight')
                            ->options(['0' => 'N/A', '1' => '10 LBS', '2' => '40 LBS'])
                            ->required(),

                        TextInput::make('pole_spacing')->numeric()->required(),
                        TextInput::make('light_intensity_value')->required(),
                        TextInput::make('light_meter_no')->required(),
                        TextInput::make('surface_condition')->required(),
                        TextInput::make('temprature')->required(),
                        TextInput::make('sheave_gauge_no')->required(),
                        TextInput::make('caliper_no')->columnSpan(3)->required(),

                        Textarea::make('dim_results')->rows(3)->columnSpan(2)->required(),
                        Textarea::make('mpi_results')->rows(3)->columnSpan(2)->required(),
                        ChecklistRepeater::make('checklist', 212),



                        FileUpload::make('crown_photo')->directory('crown_block')->image(),
                        FileUpload::make('cluster_wear_photo')->directory('crown_block')->image(),
                        FileUpload::make('fast_line_wear_photo')->directory('crown_block')->image(),
                        FileUpload::make('cluster_photo')->directory('crown_block')->image(),
                        FileUpload::make('fast_line_photo')->directory('crown_block')->image(),

                    ]),




            ]);
    }


    protected function paginateTableQuery(Builder $query): Paginator
    {
        return $query->fastPaginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                // WO# Column
                TextColumn::make('wo_id')->label('WO#')->sortable(),
                // Certification No Column - now uses eager loaded data

                // Company Column - uses eager loaded data
                TextColumn::make('customer.customer_name')->label('Company')->searchable(),
                TextColumn::make('certification_name')->label('Certification Name')->default('Crown Block'),
                TextColumn::make('test_date')->label('Insp. Date')->date()->sortable(),
                TextColumn::make('next_test_date')->label('Next Date')->date()->sortable(),
                // Issued By - uses eager loaded data
                TextColumn::make('inspector.full_name_en')->label('Issued By'),
                // Status Column - uses eager loaded data
                IconColumn::make('approved')
                    ->label('Status')
                    ->icon(fn (string $state): string => match ($state) {
                        '0' => 'heroicon-o-clock',
                        '1' => 'heroicon-o-check-circle',
                        '2' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '0' => 'warning', '1' => 'success',
                        '2' => 'danger',  default => 'gray',
                    })
                    ->action(
                        Action::make('view_reason')
                            ->modalHeading('Rejection Details')
                            // **FIXED LINE:** Use the view() helper to return a View object
                            ->modalContent(fn (CrownBlock $record) => view('filament.modals.rejection-reason', ['record' => $record]))
                            ->modalSubmitAction(false)
                            ->modalCancelActionLabel('Close')
                            ->visible(fn (CrownBlock $record) => $record->approved === '2')
                    )
            ])
            ->actions([
                // **FIX: Wrap all actions in a single ActionGroup**
                ActionGroup::make([
                    Action::make('photos')
                        ->label(fn(CrownBlock $record) => 'Photos (' . $record->photos_count . ')')
                        ->icon('heroicon-o-photo')
                        ->color(fn ($record) => $record->photos_count > 0 ? 'success' : 'gray')
                        ->url(fn ($record) => MainResource::getUrl('photos', ['record' => $record])),

                    Action::make('readings')
                        ->label('Readings')
                        ->icon('heroicon-o-table-cells')
                        ->color(fn ($record) => $record->readings === '2' ? 'success' : 'danger')
                        ->url(fn ($record) => MainResource::getUrl('readings', ['record' => $record])),

                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()->visible(fn (CrownBlock $record) => $record->approved !== '1'),

                    // -- Approval Actions --
                    Action::make('approve_supervisor')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (CrownBlock $record) {
                            $record->update([
                                'approved' => '1',
                                'approved_by' => auth()->id(),
                                'approved_date' => now(),
                            ]);
                            Notification::make()->title('Certification Approved')->success()->send();
                        })
                        ->visible(fn (CrownBlock $record) => $record->approved === '0' && auth()->user()->hasRole('Drilling_approval')),

                    Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Reject Certification')
                        ->form([
                            Textarea::make('reject_reason')->label('Reason')->required(),
                        ])
                        ->action(function (CrownBlock $record, array $data) {
                            $record->update([
                                'approved' => '2',
                                'reject_reason' => $data['reject_reason'],
                                'approved_by' => auth()->id(),
                                'approved_date' => now(),
                            ]);
                            Notification::make()->title('Certification Rejected')->success()->send();
                        })
                        ->visible(fn (CrownBlock $record) => $record->approved === '0' && auth()->user()->hasRole('Drilling_approval')),
                    Action::make('Download Approved')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (CrownBlock $record, CrownBlockCertificateService $certificateService) {
                            $pdfData = $certificateService->generatePdf($record, false);
                            $fileName = "CERT-{$record->id}-APPROVED.pdf";

                            return response()->streamDownload(
                                fn() => print($pdfData),
                                $fileName
                            );
                        }),

                    Action::make('Download Draft')
                        ->icon('heroicon-o-document-text')
                        ->color('gray')
                        ->action(function (CrownBlock $record, CrownBlockCertificateService $certificateService) {
                            $pdfData = $certificateService->generatePdf($record, true);
                            $fileName = "CERT-{$record->id}-DRAFT.pdf";

                            return response()->streamDownload(
                                fn() => print($pdfData),
                                $fileName
                            );
                        }),
                ]),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);

    }

    public static function getPages(): array
    {
        return [
            'index' => MainResource\Pages\ListMains::route('/{wo_id?}'),
            'create' => MainResource\Pages\CreateMain::route('/create/{wo_id?}'),
            'edit' => MainResource\Pages\EditMain::route('/{record}/edit'),
            'readings' => MainResource\Pages\ManageReadings::route('/{record}/readings'),
            'photos' => \App\Filament\Resources\CrownBlock\MainResource\Pages\ManagePhotos::route('/{record}/photos'),
        ];
    }



}
