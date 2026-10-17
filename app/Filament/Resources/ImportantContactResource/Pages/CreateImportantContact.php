<?php

namespace App\Filament\Resources\ImportantContactResource\Pages;

use App\Filament\Resources\ImportantContactResource;
use Filament\Resources\Pages\CreateRecord;

class CreateImportantContact extends CreateRecord
{
    protected static string $resource = ImportantContactResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['phone_number'] = self::normalizePhone($data['phone_number'] ?? '');
        return $data;
    }

    public static function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '62')) {
            // Sudah dalam format 62, biarkan
        } elseif (str_starts_with($phone, '+62')) {
            $phone = '62' . substr($phone, 3);
        }

        return $phone;
    }
}
