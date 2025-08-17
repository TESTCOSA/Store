<?php

namespace App\Filament\Resources\AttendanceApprovalResource\Pages;

use App\Filament\Resources\AttendanceApprovalResource;
use App\Models\AttendanceApproval;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditAttendanceApproval extends EditRecord
{
    protected static string $resource = AttendanceApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label('Approve')
                ->visible(function () {
                    $user = Auth::user();
                    $record = $this->getRecord();

                    // Show approve button based on role and current approval state
                    if ($user->hasRole('HR') && !$record->hr_approval && $record->manager_approval) {
                        return true;
                    }
                    if ($user->hasRole('accountant') && !$record->accountant_approval && $record->hr_approval) {
                        return true;
                    }
                    if ($user->hasRole('director') && !$record->director_approval && $record->accountant_approval) {
                        return true;
                    }

                    return false;
                })
                ->action(function () {
                    $user = Auth::user();
                    $record = $this->getRecord();

                    if ($user->hasRole('HR') && !$record->hr_approval) {
                        $record->hr_approval = true;
                        $record->hr_approved_at = now();
                        $record->status = '1';
                    } elseif ($user->hasRole('accountant') && !$record->accountant_approval) {
                        $record->accountant_approval = true;
                        $record->accountant_approved_at = now();
                        $record->status = '1';
                    } elseif ($user->hasRole('director') && !$record->director_approval) {
                        $record->director_approval = true;
                        $record->director_approved_at = now();
                        $record->status = '2';
                    }

                    $record->save();

                    $this->notify('success', 'Approval saved successfully.');
                    $this->redirect(AttendanceApprovalResource::getUrl('index'));
                }),

            Actions\Action::make('reject')
                ->label('Reject')
                ->color('danger')
                ->visible(function () {
                    $user = Auth::user();
                    $record = $this->getRecord();

                    // Show reject button based on role and approval flow
                    return
                        ($user->hasRole('HR') && $record->manager_approval && !$record->hr_approval) ||
                        ($user->hasRole('accountant') && $record->hr_approval && !$record->accountant_approval) ||
                        ($user->hasRole('director') && $record->accountant_approval && !$record->director_approval);
                })
                ->action(function () {
                    $record = $this->getRecord();
                    $record->status = 'rejected';
                    $record->save();

                    $this->notify('success', 'Approval rejected.');
                    $this->redirect(AttendanceApprovalResource::getUrl('index'));
                }),

            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = Auth::user();
        $record = $this->getRecord();

        // Set approved_at timestamps when toggling approvals
        if ($user->hasRole('manager') && $data['manager_approval'] !== $record->manager_approval) {
            $data['manager_approved_at'] = $data['manager_approval'] ? now() : null;
            $data['status'] = $data['manager_approval'] ? '1' : '0';
        }

        if ($user->hasRole('hr') && $data['hr_approval'] !== $record->hr_approval) {
            $data['hr_approved_at'] = $data['hr_approval'] ? now() : null;
            if ($data['hr_approval']) {
                $data['status'] = '1';
            }
        }

        if ($user->hasRole('accountant') && $data['accountant_approval'] !== $record->accountant_approval) {
            $data['accountant_approved_at'] = $data['accountant_approval'] ? now() : null;
            if ($data['accountant_approval']) {
                $data['status'] = '1';
            }
        }

        if ($user->hasRole('director') && $data['director_approval'] !== $record->director_approval) {
            $data['director_approved_at'] = $data['director_approval'] ? now() : null;
            if ($data['director_approval']) {
                $data['status'] = '2';
            }
        }

        return $data;
    }
}
