<?php

namespace App\Filament\Resources\CrownBlock\MainResource\Pages;

use App\Filament\Resources\CrownBlock\MainResource;
use App\Models\CrownBlock\Main as CrownBlock;
use App\Models\UserDetails;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListMains extends ListRecords
{
    protected static string $resource = MainResource::class;

    public ?int $wo_id = null;

    public function mount(): void
    {
        parent::mount();

        $this->wo_id = (int) request()->route('wo_id');
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()
            ->where('wo_id', $this->wo_id)
            // Eager load the relationships to prevent N+1 queries
            ->with([
                'customer',
                'inspector',
                'approver'
            ])
            // Eager load the count of photos for each record
            ->withCount('photos');
    }

    protected function getHeaderActions(): array
    {
        $woId = $this->wo_id;

        $inspectorOptions = fn () => UserDetails::whereIn(
            'user_id',
            CrownBlock::query()
                ->where('wo_id', $woId)
                ->pluck('inspector_id')
                ->unique()
        )
            ->pluck('full_name_en', 'user_id')
            ->toArray();

        return [
            Actions\CreateAction::make()
                ->label('New Certification')
                ->url(fn () => MainResource::getUrl('create', ['wo_id' => $woId])),
//            Actions\Action::make('print_all')
//                ->label('Print All')
//                ->icon('heroicon-o-printer')
//                ->form([
//                    Select::make('inspector_id')
//                        ->label('Inspector')
//                        ->options($inspectorOptions)
//                        ->searchable()
//                        ->native(false),
//                    Select::make('status')
//                        ->label('Status')
//                        ->options([
//                            'all' => 'All',
//                            'draft' => 'Draft',
//                            'approved' => 'Approved',
//                            'rejected' => 'Rejected',
//                        ])
//                        ->default('all'),
//                    TextInput::make('from_no')
//                        ->label('From Sequence No')
//                        ->numeric(),
//                    TextInput::make('to_no')
//                        ->label('To Sequence No')
//                        ->numeric(),
//                    Select::make('eq_status')
//                        ->label('Eq. Status')
//                        ->options([
//                            'all' => 'All',
//                            '1' => 'Accepted',
//                            '2' => 'Rejected',
//                        ])
//                        ->default('all'),
//                    Select::make('stamp')
//                        ->label('With Stamp')
//                        ->options([
//                            '0' => 'No',
//                            '1' => 'Yes',
//                        ])
//                        ->default('0'),
//                    Select::make('insp_type')
//                        ->label('Inspection Type')
//                        ->options([
//                            'API' => 'API',
//                            'ASTM' => 'ASTM',
//                        ]),
//                ])
//                ->action(function (array $data) {
//
//                }),
        ];
    }

//    protected function getTableQuery(): Builder
//    {
//        $query = parent::getTableQuery();
//
//        if ($this->wo_id) {
//            $query->where('wo_id', $this->wo_id);
//        }
//
//        return $query;
//    }
}
