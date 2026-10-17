<?php

namespace App\Filament\Resources\ImportantContactResource\Pages;

use App\Filament\Resources\ImportantContactResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditImportantContact extends EditRecord
{
    protected static string $resource = ImportantContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()->label('Hapus'),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['phone_number'] = CreateImportantContact::normalizePhone($data['phone_number'] ?? '');
        return $data;
    }
}
