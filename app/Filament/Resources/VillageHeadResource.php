<?php

namespace App\Filament\Resources;

use App\Filament\Resources\VillageHeadResource\Pages;
use App\Models\VillageHead;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class VillageHeadResource extends Resource
{
    protected static ?string $model = VillageHead::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationGroup = 'Data Desa';

    protected static ?string $navigationLabel = 'Riwayat Kepala Desa';

    protected static ?string $modelLabel = 'Kepala Desa';

    protected static ?string $pluralModelLabel = 'Riwayat Kepala Desa';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Kepala Desa')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap & Gelar')
                            ->placeholder('Contoh: H. Ahmad Subardjo, S.Sos.')
                            ->required()
                            ->validationMessages([
                                'required' => 'Nama lengkap dan gelar wajib diisi.',
                            ])
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('photo_path')
                            ->label('Foto Resmi')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(2048)
                            ->directory('village-heads')
                            ->helperText('Format: JPG, PNG, atau WEBP. Maksimal 2MB.')
                            ->validationMessages([
                                'max' => 'Ukuran foto maksimal adalah 2MB.',
                                'mimes' => 'Format foto hanya boleh JPG, PNG, atau WEBP.',
                            ]),
                        Forms\Components\DatePicker::make('period_start')
                            ->label('Mulai Menjabat')
                            ->required()
                            ->validationMessages([
                                'required' => 'Tanggal mulai menjabat wajib diisi.',
                            ]),
                        Forms\Components\DatePicker::make('period_end')
                            ->label('Selesai Menjabat')
                            ->helperText('Kosongkan jika masih aktif menjabat.'),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Sedang Menjabat (Aktif)')
                            ->helperText('Mengaktifkan ini akan otomatis menonaktifkan status aktif pada kepala desa lain.')
                            ->default(false),
                        Forms\Components\TextInput::make('order')
                            ->label('Urutan Tampilan')
                            ->numeric()
                            ->default(0)
                            ->helperText('Semakin kecil angka urutan, semakin awal ditampilkan.'),
                        Forms\Components\Textarea::make('welcome_speech')
                            ->label('Kata Sambutan Kepala Desa')
                            ->placeholder('Tuliskan kata sambutan resmi untuk warga desa...')
                            ->helperText('Sambutan ini akan ditampilkan di halaman utama website jika status aktif.')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo_path')
                    ->label('Foto')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('period_start')
                    ->label('Periode Jabatan')
                    ->formatStateUsing(fn ($record) => $record->period_start?->format('Y') . ' - ' . ($record->period_end ? $record->period_end->format('Y') : 'Sekarang')),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status Aktif')
                    ->boolean(),
                Tables\Columns\TextColumn::make('order')
                    ->label('Urutan')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Menjabat'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Ubah'),
                Tables\Actions\DeleteAction::make()->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Hapus Terpilih'),
                ]),
            ])
            ->defaultSort('order', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVillageHeads::route('/'),
            'create' => Pages\CreateVillageHead::route('/create'),
            'edit' => Pages\EditVillageHead::route('/{record}/edit'),
        ];
    }
}
