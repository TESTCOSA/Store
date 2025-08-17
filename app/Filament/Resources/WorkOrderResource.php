<?php
namespace App\Filament\Resources;

use App\Models\WorkOrder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Resources\Resource;
use App\Models\CrownBlock\Main;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\WorkOrderResource\Pages;

class WorkOrderResource extends Resource
{
    protected static ?string $model = WorkOrder::class;
//    protected static ?string $navigationIcon = 'heroicon-o-clipboard-list';
    protected static ?string $navigationGroup = 'Inspections';
    protected static ?string $label = 'Work Order';
    protected static ?string $pluralLabel = 'Work Orders';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('wo_id')->disabled()->label('WO#'),
            Forms\Components\DateTimePicker::make('wo_date')->label('WO Date'),
            Forms\Components\Select::make('customer_id')->relationship('customer', 'customer_name')->label('Customer'),
            Forms\Components\Textarea::make('notes')->label('Notes'),
            Forms\Components\TextInput::make('wo_file')->label('File'),
        ]);
    }

    public static function table(Table $table): Table
    {
       return $table

           ->columns([
            TextColumn::make('wo_id')->label('WO#')->sortable(),
            TextColumn::make('customer.customer_name')->label('Company')->sortable()->searchable()->wrap(),
            TextColumn::make('wo_date')->label('Date'),
            TextColumn::make('location_details')->label('Location'),
               ViewColumn::make('Actions')
                   ->view('filament.tables.columns.certification-buttons'),
        ])
            ->filters([])
            ->actions([
                Tables\Actions\Action::make('Crown Block')
                    ->label('Crown Block')
                    ->color(fn (WorkOrder $record) => Main::where('wo_id', $record->wo_id)->exists() ? 'success' : 'gray')
                    ->icon('heroicon-o-cube')
//                    ->url(fn (WorkOrder $record) => CrownBlockResource::getUrl('create', ['wo_id' => $record->wo_id]))
                    ->url(fn (WorkOrder $record) => $record->crownBlock()->exists()
                        ? route('filament.app.resources.crown-block.mains.index', ['wo_id' => $record->wo_id])
                        : route('filament.app.resources.crown-block.mains.create', ['wo_id' => $record->wo_id]))
                    ->openUrlInNewTab(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(fn ($record) => $record->approved == '0')
                    ->action(fn ($record) => $record->update([
                        'approved' => '1',
                        'approved_by' => auth()->id(),
                        'approved_date' => now(),
                    ])),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkOrders::route('/'),
            'create' => Pages\CreateWorkOrder::route('/create'),
            'edit' => Pages\EditWorkOrder::route('/{record}/edit'),
        ];
    }
}
