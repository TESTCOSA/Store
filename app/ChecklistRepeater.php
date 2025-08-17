<?php

namespace App;

use App\Models\ChecklistDetail;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;

class ChecklistRepeater
{
    public static function make(string $fieldName, int $checklistId): Repeater
    {
        $items = ChecklistDetail::query()
            ->where('checklist_id', $checklistId)
            ->orderBy('sortorder')
            ->get();

        return Repeater::make($fieldName)
            ->label('Checklist Items')
            ->schema([
                Hidden::make('checklist_id')->default($checklistId),
                Fieldset::make('Checklist Results')
                    ->schema(array_map(function ($item) {
                        return Radio::make("results.{$item->item_id}")
                            ->label($item->item_title)
                            ->options([
                                '1' => 'Pass',
                                '2' => 'Fail',
                                '0' => 'N/A',
                            ])
                            ->inline()
                            ->required();
                    }, $items->all()))
            ])
            ->defaultItems(1) // Ensure one is rendered on create
            ->columnSpanFull()
            ->deletable(false)
            ->addable(false);
    }
}
