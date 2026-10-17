<?php

namespace App\Filament\Resources\VillageHeadResource\Pages;

use App\Filament\Resources\VillageHeadResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVillageHead extends EditRecord
{
    protected static string $resource = VillageHeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
