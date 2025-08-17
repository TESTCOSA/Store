<?php

namespace App\Filament\Resources\ItemResource\Pages;

use App\Filament\Resources\ItemResource;
use App\Models\Category;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateItem extends CreateRecord
{
    protected function mutateFormDataBeforeCreate(array $data): array
    {
       $category = Category::find($data['category_id']);
        $prefix ='';
        if($category->type->is_consumable){
            $prefix = 'N/A';
        }else{
        $firstWord = explode(' ', trim($category->name))[0] ?? '';
        $lastWord = explode(' ', trim($category->name));
        $lastWord = end($lastWord) ?? '';

        $sequence = '';
        if($category->count() < 10){
            $sequence = '00'.$category->items->count();
        }
        elseif($category->count() > 10){
            $sequence = '0'.$category->items->count();
        }
            $prefix = 'TEST-'.strtoupper(substr($firstWord, 0, 1) . substr($lastWord, 0, 1)).'-'.$sequence;
        }
            $data['test_tag'] = $prefix;

        return $data;
    }
    protected static string $resource = ItemResource::class;
}
