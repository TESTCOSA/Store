<?php

namespace App\Filament\Resources\CrownBlock\MainResource\Pages;

use App\Filament\Resources\CrownBlock\MainResource as CrownBlockResource;
use App\Models\CrownBlock\ReadingCL;
use App\Models\CrownBlock\ReadingFL;
use App\Models\CrownBlock\Main;
use Filament\Forms;
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
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Redirect;

class ManageReadings extends Page implements HasForms
{
    protected static string $resource = CrownBlockResource::class;

    protected static string $view = 'filament.resources.crown-block.main-resource.pages.manage-readings';
    use InteractsWithForms;
    public ?array $data = [];
    public Main $record;

    // Values needed for reactive calculations
    public float $cluster_min = 0;
    public float $cluster_max = 0;

    public function mount(): void
    {
        // Load the main record from the URL and its relationships
        $this->record->load(['clusterReadings', 'fastLineReading']);

        // Set values needed for validation, taken from the main certificate record
        $drill_line_dia = (float)$this->record->drill_line_dia;
        if ($drill_line_dia > 0) {
            $this->cluster_min = $drill_line_dia * 1.75;
            $this->cluster_max = $drill_line_dia * 3;
        }

        // Prepare initial data for the form by merging existing relationships
        $initialData = $this->record->toArray();
        $initialData['clusterReadings'] = $this->record->clusterReadings->toArray();
        $initialData['fastLineReading'] = $this->record->fastLineReading?->toArray() ?? [];
        $initialData['sandLineReading'] = $this->record->sandLineReading?->toArray() ?? [];

        // If there are no cluster readings yet, pre-populate the repeater from the S/N field
        if ($this->record->clusterReadings->isEmpty() && !empty($this->record->cluster_sheaves_sn)) {
            $clusterSns = explode(',', $this->record->cluster_sheaves_sn);
            $initialData['clusterReadings'] = collect($clusterSns)->map(fn($sn) => ['cluster_sn' => trim($sn)])->toArray();
        }

        // Fill the form with the prepared data
        $this->form->fill($initialData);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // This section mimics your top table for Min/Max values
                Section::make('Groove Depth Specifications')
                    ->schema([
                        Grid::make(4)
                            ->schema([
                                Placeholder::make('cluster_min_display')
                                    ->label('Cluster & Fast Line Min. Groove Depth')
                                    ->content(number_format($this->cluster_min, 2)),

                                Placeholder::make('cluster_max_display')
                                    ->label('Cluster & Fast Line Max. Groove Depth')
                                    ->content(number_format($this->cluster_max, 2)),

                                Placeholder::make('sand_line_min_display')
                                    ->label('Sand Line Min. Groove Depth')
                                    ->content(function(Get $get) {
                                        $nominal = (float)$get('sandLineReading.sl_nominal_wire');
                                        return $nominal > 0 ? number_format($nominal * 1.75, 2) : 'N/A';
                                    }),

                                Placeholder::make('sand_line_max_display')
                                    ->label('Sand Line Max. Groove Depth')
                                    ->content(function(Get $get) {
                                        $nominal = (float)$get('sandLineReading.sl_nominal_wire');
                                        return $nominal > 0 ? number_format($nominal * 3, 2) : 'N/A';
                                    }),
                            ]),
                    ])->collapsible(),

                Section::make('Cluster Sheaves Readings')
                    ->schema([
                        Repeater::make('clusterReadings')
//                            ->relationship() // This will automatically handle creating/updating ReadingCL models
                            ->schema([
                                TextInput::make('cluster_sn')->label('Serial No')->columnSpan(2),
                                ...$this->createGrooveFields(
                                    min: $this->cluster_min,
                                    max: $this->cluster_max,
                                    passFailPath: 'pass_fail' // Relative path within the repeater item
                                )
                            ])
                            ->columns(7)
                            ->defaultItems(0)
                            ->addActionLabel('Add Sheave Reading')
                            ->reorderable(false),
                    ]),

                Section::make('Fast Line Sheave Reading')
                    ->schema([
                        Grid::make(7)->schema([
                            TextInput::make('fl_sheave_sn')
                                ->label('Serial No')
                                ->default($this->record->fl_sheave_sn)
                                ->readOnly()
                                ->columnSpan(2),
                            ...$this->createGrooveFields(
                                min: $this->cluster_min,
                                max: $this->cluster_max,
                                passFailPath: 'fastLineReading.pass_fail', // Path from form root
                                basePath: 'fastLineReading' // All fields will be prefixed with this
                            )
                        ])
                    ]),


            ])
            ->statePath('data');
    }

    /** Helper function to avoid repeating groove fields */
    protected function createGrooveFields(float $min, float $max, string $passFailPath, string $basePath = '', ?string $nominalPath = null): array
    {
        $prefix = !empty($basePath) ? $basePath . '.' : '';

        $fields = [];
        foreach (['a', 'b', 'c', 'd'] as $point) {
            $fields[] = TextInput::make($prefix . 'groove_' . $point)
                ->label('Groove ' . strtoupper($point))
                ->numeric()->required()
                ->live(onBlur: true)
                ->afterStateUpdated(function (Get $get, Set $set, $state) use ($min, $max, $passFailPath, $nominalPath, $prefix) {
                    $value = (float)$state;
                    $isFail = false;

                    if ($nominalPath) { // Dynamic min/max for Sand Line
                        $nominal = (float)$get($nominalPath);
                        if ($nominal > 0) {
                            $min = $nominal * 1.75;
                            $max = $nominal * 3;
                        } else {
                            $isFail = true; // Fail if nominal is not set
                        }
                    }

                    if (!$isFail && ($value < $min || $value > $max)) {
                        $isFail = true;
                    }

                    if ($isFail) {
                        $set($passFailPath, '2'); // Fail
                    } else {
                        // Only set to pass if ALL other grooves are also passing
                        $allGrooves = [
                            (float)$get($prefix.'groove_a'), (float)$get($prefix.'groove_b'),
                            (float)$get($prefix.'groove_c'), (float)$get($prefix.'groove_d')
                        ];

                        $allPass = true;
                        foreach($allGrooves as $groove) {
                            if ($groove > 0 && ($groove < $min || $groove > $max)) {
                                $allPass = false;
                                break;
                            }
                        }
                        if($allPass) $set($passFailPath, '1'); // Pass
                    }
                });
        }

        $fields[] = Select::make($prefix . 'pass_fail')
            ->options(['1' => 'Pass', '2' => 'Fail'])
            ->default('1')->required();

        return $fields;
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Readings')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Update or Create Cluster Readings (HasMany)
        if (isset($data['clusterReadings'])) {
            foreach ($data['clusterReadings'] as $readingData) {
                $this->record->clusterReadings()->updateOrCreate(
                    ['cluster_sn' => $readingData['cluster_sn']], // Find by this
                    Arr::except($readingData, ['cluster_sn']) // Update with this
                );
            }
        }

        // Update or Create Fast Line Reading (HasOne)
        if (isset($data['fastLineReading'])) {
            $this->record->fastLineReading()->updateOrCreate(
                ['certification_id' => $this->record->certification_id], // Find by this
                array_merge($data['fastLineReading'], ['fast_line_sn' => $this->record->fl_sheave_sn]) // Update with this
            );
        }

        // Update or Create Sand Line Reading (HasOne)
        if (!empty($this->record->sand_line_sheave_sn) && isset($data['sandLineReading'])) {
            $this->record->sandLineReading()->updateOrCreate(
                ['certification_id' => $this->record->certification_id], // Find by this
                $data['sandLineReading'] // Update with this
            );
        }

        // Mark the main record as having readings
        $this->record->update(['readings' => '2']);

        Notification::make()->title('Readings saved successfully!')->success()->send();
    }
}
