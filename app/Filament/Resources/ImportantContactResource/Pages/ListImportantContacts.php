<?php

namespace App\Filament\Resources\ImportantContactResource\Pages;

use App\Filament\Resources\ImportantContactResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListImportantContacts extends ListRecords
{
    protected static string $resource = ImportantContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
