<?php

namespace App\Filament\Resources\VillageHeadResource\Pages;

use App\Filament\Resources\VillageHeadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListVillageHeads extends ListRecords
{
    protected static string $resource = VillageHeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
