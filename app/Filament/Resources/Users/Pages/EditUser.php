<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! auth()->user()?->hasRole('super_admin')) {
            $superAdminRole = \Spatie\Permission\Models\Role::where('name', 'super_admin')->first();
            if ($superAdminRole && isset($data['roles'])) {
                $data['roles'] = array_values(
                    array_filter($data['roles'], fn ($id) => (int) $id !== (int) $superAdminRole->id)
                );
            }
        }

        return $data;
    }
}
