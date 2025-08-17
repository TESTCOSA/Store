<?php

namespace App\Filament\Resources;

use App\Models\TravelingBlock\Main as TravelingBlock;
use App\Services\TravelingBlockCertificateService;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
<<<<<<< HEAD
use Filament\Notifications\Notification;
=======
>>>>>>> d3edaf8 (feat: add traveling block certification module)
use Illuminate\Support\Str;

class TravelingBlockResource extends Resource
{
    protected static ?string $model = TravelingBlock::class;
    protected static ?string $pluralLabel = 'Traveling Block Certifications';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('General Info')->columns(3)->schema([
                    TextInput::make('wo_id')->required(),
                    TextInput::make('work_location')->required(),
                    DatePicker::make('test_date')->required(),
                    DatePicker::make('next_test_date'),
                    Select::make('status')->options(['1' => 'Accepted', '2' => 'Rejected'])->required(),
                ]),
                Section::make('Standards')->columns(2)->schema([
                    Checkbox::make('standard_type_api')->label('Specification API'),
                    TextInput::make('standard_name_api'),
                    Checkbox::make('standard_type_astm')->label('Specification ASTM'),
                    TextInput::make('standard_name_astm'),
                ]),
                Section::make('MPI / Equipment')->columns(2)->schema([
                    Select::make('inspection_method')->options(['1' => 'Visible', '2' => 'Fluorescent'])->required(),
                    Select::make('mpi_type')->options(['1' => 'Wet', '2' => 'Dry'])->required(),
                    Select::make('insp_type')->options(['1' => 'Cat III', '2' => 'Cat IV'])->required(),
                    Select::make('mg_eq_used')->options(['1' => 'AC', '2' => 'DC', '3' => 'Permanent'])->required(),
                    TextInput::make('mg_eq_manuf'),
                    TextInput::make('magnet_no'),
                    DatePicker::make('manuf_date'),
                    TextInput::make('model'),
                    Textarea::make('description')->columnSpanFull(),
                    TextInput::make('manufacturer'),
                    TextInput::make('contrast_media'),
                    TextInput::make('contrast_media_batch'),
                    TextInput::make('contrast_media_manuf'),
                    TextInput::make('indicator'),
                    TextInput::make('indicator_batch'),
                    TextInput::make('indicator_manuf'),
                    TextInput::make('cal_test_weight'),
                    TextInput::make('pole_spacing'),
                    TextInput::make('light_meter_no'),
                    TextInput::make('light_intensity_value'),
                    TextInput::make('surface_condition'),
                    TextInput::make('temprature'),
                    TextInput::make('sheave_gauge_no'),
                    TextInput::make('caliper_no'),
                ]),
                Section::make('Sheaves Info')->columns(2)->schema([
                    TextInput::make('sheaves_od'),
                    TextInput::make('drill_line_dia'),
                    TextInput::make('rated_loading'),
                    TextInput::make('equipment_no'),
                    TextInput::make('sheaves_sn'),
                    TextInput::make('sheaves_sn_disc'),
                ]),
                Section::make('Results')->columns(2)->schema([
                    Textarea::make('mpi_results'),
                    Textarea::make('dim_results'),
                ]),
                Section::make('Photos')->columns(3)->schema([
                    FileUpload::make('traveling_photo')->directory('traveling_block')->image(),
                    FileUpload::make('sheave_wear_photo')->directory('traveling_block')->image(),
                    FileUpload::make('sheave_groove_photo')->directory('traveling_block')->image(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('certification_id')->label('ID'),
                Tables\Columns\TextColumn::make('wo_id'),
                Tables\Columns\TextColumn::make('test_date'),
                Tables\Columns\TextColumn::make('status')->badge(),
            ])
            ->actions([
                ActionGroup::make([
<<<<<<< HEAD
                    Action::make('photos')
                        ->label(fn(TravelingBlock $record) => 'Photos (' . $record->photos()->count() . ')')
                        ->icon('heroicon-o-photo')
                        ->color(fn(TravelingBlock $record) => $record->photos()->count() > 0 ? 'success' : 'gray')
                        ->url(fn(TravelingBlock $record) => self::getUrl('photos', ['record' => $record])),

                    Action::make('readings')
                        ->label('Readings')
                        ->icon('heroicon-o-table-cells')
                        ->color(fn(TravelingBlock $record) => $record->readings === '2' ? 'success' : 'danger')
                        ->url(fn(TravelingBlock $record) => self::getUrl('readings', ['record' => $record])),

                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make()->visible(fn(TravelingBlock $record) => $record->approved !== '1'),

                    Action::make('approve_supervisor')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (TravelingBlock $record) {
                            $record->update([
                                'approved' => '1',
                                'approved_by' => auth()->id(),
                                'approved_date' => now(),
                            ]);
                            Notification::make()->title('Certification Approved')->success()->send();
                        })
                        ->visible(fn(TravelingBlock $record) => $record->approved === '0' && auth()->user()->hasRole('Drilling_approval')),

                    Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Reject Certification')
                        ->form([
                            Textarea::make('reject_reason')->label('Reason')->required(),
                        ])
                        ->action(function (TravelingBlock $record, array $data) {
                            $record->update([
                                'approved' => '2',
                                'reject_reason' => $data['reject_reason'],
                                'approved_by' => auth()->id(),
                        'approved_date' => now(),
                            ]);
                            Notification::make()->title('Certification Rejected')->success()->send();
                        })
                        ->visible(fn(TravelingBlock $record) => $record->approved === '0' && auth()->user()->hasRole('Drilling_approval')),

                    Action::make('Download Approved')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (TravelingBlock $record, TravelingBlockCertificateService $svc) {
                            $pdfData = $svc->generatePdf($record, false);
                            $fileName = "CERT-{$record->id}-APPROVED.pdf";

                            return response()->streamDownload(
                                fn() => print($pdfData),
                                $fileName
                            );
                        }),

                    Action::make('Download Draft')
                        ->icon('heroicon-o-document-text')
                        ->color('gray')
                        ->action(function (TravelingBlock $record, TravelingBlockCertificateService $svc) {
                            $pdfData = $svc->generatePdf($record, true);
                            $fileName = "CERT-{$record->id}-DRAFT.pdf";

                            return response()->streamDownload(
                                fn() => print($pdfData),
                                $fileName
                            );
                        }),
                ]),
=======
                    Action::make('print')
                        ->label('Download Approved')
                        ->icon('heroicon-o-check-circle')
                        ->action(fn(TravelingBlock $record, TravelingBlockCertificateService $svc) => response()->streamDownload(fn() => print($svc->generatePdf($record, false)), 'CERT-'.$record->id.'-APPROVED.pdf')),
                    Action::make('print-draft')
                        ->label('Download Draft')
                        ->icon('heroicon-o-document-text')
                        ->action(fn(TravelingBlock $record, TravelingBlockCertificateService $svc) => response()->streamDownload(fn() => print($svc->generatePdf($record, true)), 'CERT-'.$record->id.'-DRAFT.pdf')),
                ])->label('Download PDF')->icon('heroicon-o-arrow-down-tray')->color('info'),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
>>>>>>> d3edaf8 (feat: add traveling block certification module)
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTravelingBlocks::route('/'),
            'create' => Pages\CreateMain::route('/create'),
            'edit' => Pages\EditMain::route('/{record}/edit'),
<<<<<<< HEAD
            'readings' => Pages\ManageReadings::route('/{record}/readings'),
            'photos' => Pages\ManagePhotos::route('/{record}/photos'),
=======
            'create-reading' => Pages\CreateReading::route('/{record}/readings/create'),
            'edit-reading' => Pages\EditReading::route('/readings/{record}/edit'),
>>>>>>> d3edaf8 (feat: add traveling block certification module)
            'print-cert' => Pages\PrintCertification::route('/{record}/print'),
            'print-draft' => Pages\PrintDraftCertification::route('/{record}/draft'),
            'logs' => Pages\LogsPage::route('/{record}/logs'),
        ];
    }
}

