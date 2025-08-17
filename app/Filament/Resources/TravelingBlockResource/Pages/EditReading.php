<?php

namespace App\Filament\Resources\TravelingBlockResource\Pages;

use App\Filament\Resources\TravelingBlockResource;
use App\Models\TravelingBlock\Reading;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class EditReading extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = TravelingBlockResource::class;

    public Reading $record;
    public ?array $data = [];

    public function mount(Reading $record): void
    {
        $this->record = $record;
        $this->form->fill($record->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('sheaves_sn')->required(),
                TextInput::make('groove_a')->numeric()->required(),
                TextInput::make('groove_b')->numeric()->required(),
                TextInput::make('groove_c')->numeric()->required(),
                TextInput::make('groove_d')->numeric()->required(),
                Select::make('pass_fail')->options(['1' => 'Pass', '2' => 'Fail'])->required(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $this->record->update($this->data ?? []);
        $this->redirect(TravelingBlockResource::getUrl('edit', ['record' => $this->record->certification_id]));
    }

    protected static string $view = 'filament.resources.traveling-block-resource.pages.edit-reading';
}

