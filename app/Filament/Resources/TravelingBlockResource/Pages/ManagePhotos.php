<?php

namespace App\Filament\Resources\TravelingBlockResource\Pages;

use App\Filament\Resources\TravelingBlockResource;
use App\Models\TravelingBlock\Main as TravelingBlock;
use App\Models\TravelingBlock\Photo;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions\Action as InfolistAction;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Storage;

class ManagePhotos extends Page implements HasForms, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;
    use InteractsWithRecord;

    protected static string $resource = TravelingBlockResource::class;
    protected static string $view = 'filament.resources.traveling-block-resource.pages.manage-photos';

    public ?array $data = [];

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('photos')
                    ->label('Upload New Photos')
                    ->multiple()
                    ->directory('traveling_block')
                    ->disk('public')
                    ->required()
                    ->image()
                    ->imageEditor()
                    ->panelLayout('grid'),
            ])
            ->statePath('data');
    }

    public function photosInfolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->record($this->record)
            ->schema([
                RepeatableEntry::make('photos')
                    ->label('')
                    ->grid(4)
                    ->schema([
                        ImageEntry::make('file_name')
                            ->label('Click on photo to Delete')
                            ->disk('public')
                            ->height(250)
                            ->extraAttributes([
                                'class' => 'bg-red-700 dark:bg-red-800 rounded-lg',
                            ])
                            ->extraImgAttributes([
                                'class' => 'object-contain w-full h-full',
                                'loading' => 'lazy',
                            ])
                            ->action(
                                InfolistAction::make('deletePhoto')
                                    ->label('Delete')
                                    ->icon('heroicon-o-trash')
                                    ->color('danger')
                                    ->requiresConfirmation()
                                    ->modalHeading('Delete Photo')
                                    ->action(function (Photo $record) {
                                        Storage::disk('public')->delete($record->file_name);
                                        $record->delete();
                                        Notification::make()->title('Photo deleted.')->success()->send();
                                    })
                                    ->button()->iconButton(),
                            ),
                    ]),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        if (empty($data['photos'])) {
            return;
        }

        foreach ($data['photos'] as $photoPath) {
            $this->record->photos()->create([
                'file_name' => $photoPath,
            ]);
        }

        Notification::make()->title('Photos uploaded successfully!')->success()->send();
        $this->form->fill();
    }
}

