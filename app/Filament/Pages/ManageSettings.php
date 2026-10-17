<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Pengaturan';

    protected static ?string $navigationLabel = 'Pengaturan Situs';

    protected static ?string $title = 'Pengaturan Situs';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.manage-settings';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public function mount(): void
    {
        $this->form->fill([
            'village_name' => Setting::get('village_name', env('VILLAGE_NAME', 'Desa Damai')),
            'district_name' => Setting::get('district_name', env('VILLAGE_DISTRICT', 'Kecamatan Melati')),
            'regency_name' => Setting::get('regency_name', env('VILLAGE_REGENCY', 'Kabupaten Nusantara')),
            'office_address' => Setting::get('office_address'),
            'contact_phone' => Setting::get('contact_phone'),
            'contact_email' => Setting::get('contact_email'),
            'service_hours' => Setting::get('service_hours', 'Senin - Jumat: 08:00 - 15:00 WIB'),
            'social_facebook' => Setting::get('social_facebook'),
            'social_instagram' => Setting::get('social_instagram'),
            'social_youtube' => Setting::get('social_youtube'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identitas Desa')
                    ->description('Informasi nama dan wilayah pemerintahan desa.')
                    ->schema([
                        Forms\Components\TextInput::make('village_name')
                            ->label('Nama Desa')
                            ->placeholder('Contoh: Desa Damai')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('district_name')
                            ->label('Kecamatan')
                            ->placeholder('Contoh: Kecamatan Melati')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('regency_name')
                            ->label('Kabupaten')
                            ->placeholder('Contoh: Kabupaten Nusantara')
                            ->required()
                            ->maxLength(255),
                    ])->columns(3),

                Forms\Components\Section::make('Kontak & Jam Pelayanan Balai Desa')
                    ->description('Informasi kantor untuk kemudahan akses warga.')
                    ->schema([
                        Forms\Components\TextInput::make('contact_phone')
                            ->label('Nomor Telepon Kantor Desa')
                            ->placeholder('08xxxxxxxxxx')
                            ->tel()
                            ->maxLength(50),
                        Forms\Components\TextInput::make('contact_email')
                            ->label('Email Resmi Desa')
                            ->placeholder('desa@desadamai.go.id')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('service_hours')
                            ->label('Jam Pelayanan Kantor')
                            ->placeholder('Senin - Jumat: 08.00 - 15.00')
                            ->maxLength(255),
                        Forms\Components\Textarea::make('office_address')
                            ->label('Alamat Lengkap Kantor Desa')
                            ->placeholder('Jl. Raya Desa No. 1...')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])->columns(3),

                Forms\Components\Section::make('Tautan Media Sosial')
                    ->description('Akun media sosial resmi desa untuk publikasi warga.')
                    ->schema([
                        Forms\Components\TextInput::make('social_facebook')
                            ->label('Facebook URL')
                            ->placeholder('https://facebook.com/...'),
                        Forms\Components\TextInput::make('social_instagram')
                            ->label('Instagram URL')
                            ->placeholder('https://instagram.com/...'),
                        Forms\Components\TextInput::make('social_youtube')
                            ->label('YouTube URL')
                            ->placeholder('https://youtube.com/...'),
                    ])->columns(3),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        foreach ($state as $key => $value) {
            Setting::set($key, $value);
        }

        Notification::make()
            ->title('Pengaturan berhasil disimpan')
            ->success()
            ->send();
    }
}
