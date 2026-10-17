<?php

namespace App\Filament\Resources\InstitutionMemberResource\Pages;

use App\Filament\Resources\InstitutionMemberResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInstitutionMembers extends ListRecords
{
    protected static string $resource = InstitutionMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
