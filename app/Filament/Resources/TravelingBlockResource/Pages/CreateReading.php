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

class CreateReading extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = TravelingBlockResource::class;

    public ?int $record = null;
    public ?array $data = [];

    public function mount(int $record): void
    {
        $this->record = $record;
        $this->form->fill();
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

    public function create(): void
    {
        Reading::create(array_merge($this->data ?? [], [
            'certification_id' => $this->record,
        ]));
        $this->redirect(TravelingBlockResource::getUrl('edit', ['record' => $this->record]));
    }

    protected static string $view = 'filament.resources.traveling-block-resource.pages.create-reading';
}

