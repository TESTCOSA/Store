<?php

namespace App\Filament\Resources\TravelingBlockResource\Pages;

use App\Filament\Resources\TravelingBlockResource;
use App\Models\TravelingBlock\Main as TravelingBlock;
use App\Models\TravelingBlock\Reading;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Arr;

class ManageReadings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = TravelingBlockResource::class;
    protected static string $view = 'filament.resources.traveling-block-resource.pages.manage-readings';

    public ?array $data = [];
    public TravelingBlock $record;

    public float $groove_min = 0;
    public float $groove_max = 0;

    public function mount(): void
    {
        $this->record->load('readings');

        $drill = (float) $this->record->drill_line_dia;
        if ($drill > 0) {
            $this->groove_min = $drill * 1.75;
            $this->groove_max = $drill * 3;
        }

        $initial = $this->record->toArray();
        $initial['readings'] = $this->record->readings->toArray();

        if ($this->record->readings->isEmpty() && ! empty($this->record->sheaves_sn)) {
            $sns = explode(',', $this->record->sheaves_sn);
            $initial['readings'] = collect($sns)->map(fn($sn) => ['sheaves_sn' => trim($sn)])->toArray();
        }

        $this->form->fill($initial);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Groove Depth Specifications')
                    ->schema([
                        Grid::make(2)->schema([
                            Placeholder::make('min_display')
                                ->label('Min. Groove Depth')
                                ->content(number_format($this->groove_min, 2)),
                            Placeholder::make('max_display')
                                ->label('Max. Groove Depth')
                                ->content(number_format($this->groove_max, 2)),
                        ]),
                    ])->collapsible(),
                Section::make('Sheave Readings')
                    ->schema([
                        Repeater::make('readings')
                            ->schema([
                                TextInput::make('sheaves_sn')->label('Serial No')->columnSpan(2),
                                ...$this->createGrooveFields($this->groove_min, $this->groove_max, 'pass_fail'),
                            ])
                            ->columns(7)
                            ->defaultItems(0)
                            ->addActionLabel('Add Sheave Reading')
                            ->reorderable(false),
                    ]),
            ])
            ->statePath('data');
    }

    protected function createGrooveFields(float $min, float $max, string $passFailPath): array
    {
        $fields = [];
        foreach (['a', 'b', 'c', 'd'] as $point) {
            $fields[] = TextInput::make('groove_' . $point)
                ->label('Groove ' . strtoupper($point))
                ->numeric()
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(function (Get $get, Set $set, $state) use ($min, $max, $passFailPath) {
                    $value = (float) $state;
                    $isFail = $value < $min || $value > $max;
                    if ($isFail) {
                        $set($passFailPath, '2');
                    } else {
                        $all = [
                            (float) $get('groove_a'),
                            (float) $get('groove_b'),
                            (float) $get('groove_c'),
                            (float) $get('groove_d'),
                        ];
                        $pass = collect($all)->every(fn($g) => $g >= $min && $g <= $max);
                        if ($pass) {
                            $set($passFailPath, '1');
                        }
                    }
                });
        }

        $fields[] = Select::make($passFailPath)
            ->options(['1' => 'Pass', '2' => 'Fail'])
            ->default('1')
            ->required();

        return $fields;
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')->label('Save Readings')->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        if (isset($data['readings'])) {
            foreach ($data['readings'] as $reading) {
                $this->record->readings()->updateOrCreate(
                    ['sheaves_sn' => $reading['sheaves_sn']],
                    Arr::except($reading, ['sheaves_sn'])
                );
            }
        }

        $this->record->update(['readings' => '2']);
        Notification::make()->title('Readings saved successfully!')->success()->send();
    }
}

