<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PointOfInterestResource\Pages;
use App\Models\PointOfInterest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PointOfInterestResource extends Resource
{
    protected static ?string $model = PointOfInterest::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'Data Desa';

    protected static ?string $navigationLabel = 'Peta Lokasi Penting';

    protected static ?string $modelLabel = 'Titik Lokasi';

    protected static ?string $pluralModelLabel = 'Peta Lokasi Penting';

    protected static ?int $navigationSort = 4;

    public const CATEGORY_OPTIONS = [
        'kantor_desa'        => 'Kantor Desa',
        'rumah_kadus'        => 'Rumah Kepala Dusun',
        'rumah_kepala_desa'  => 'Rumah Kepala Desa',
        'fasilitas_umum'     => 'Fasilitas Umum',
        'lainnya'            => 'Lainnya',
    ];

    private const PHOTO_REQUIRED_CATEGORIES = ['rumah_kadus', 'rumah_kepala_desa'];

    // Bounding Box Kasar Wilayah Kabupaten (Lat: -8.5 s.d. -6.0, Lng: 106.0 s.d. 115.0)
    public const MIN_LAT = -5.5;
    public const MAX_LAT = -4.5;
    public const MIN_LNG = 119.0;
    public const MAX_LNG = 120.0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Lokasi')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Titik Lokasi')
                            ->placeholder('Contoh: Kantor Kepala Desa, Poskesdes Dusun 2')
                            ->required()
                            ->validationMessages([
                                'required' => 'Nama titik lokasi wajib diisi.',
                            ])
                            ->maxLength(255),

                        Forms\Components\Select::make('category')
                            ->label('Kategori Tempat')
                            ->options(self::CATEGORY_OPTIONS)
                            ->required()
                            ->live()
                            ->validationMessages([
                                'required' => 'Kategori tempat wajib dipilih.',
                            ]),

                        Forms\Components\TextInput::make('hamlet')
                            ->label('Dusun Terkait')
                            ->placeholder('Contoh: Dusun Krajan')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('address')
                            ->label('Alamat Lengkap')
                            ->placeholder('Contoh: Jl. Raya No. 12, RT 01 / RW 02')
                            ->maxLength(500),
                    ])->columns(2),

                Forms\Components\Section::make('Peta Lokasi & Titik Koordinat')
                    ->description('Cari nama tempat, gunakan GPS lokasi Anda saat ini, atau klik/geser pin langsung pada peta.')
                    ->schema([
                        Forms\Components\ViewField::make('map_picker')
                            ->view('filament.forms.components.map-picker')
                            ->columnSpanFull(),

                        Forms\Components\Section::make('⚙️ Pengaturan Koordinat Manual (Opsional)')
                            ->description('Input angka koordinat manual jika diperlukan (terisi otomatis saat memilih titik di peta).')
                            ->collapsed()
                            ->compact()
                            ->schema([
                                Forms\Components\TextInput::make('latitude')
                                    ->label('Garis Lintang (Latitude)')
                                    ->placeholder('Contoh: -5.064099')
                                    ->helperText('Batas koordinat wilayah: ' . self::MIN_LAT . ' s.d. ' . self::MAX_LAT)
                                    ->required()
                                    ->numeric()
                                    ->rules(['numeric', 'min:' . self::MIN_LAT, 'max:' . self::MAX_LAT])
                                    ->validationMessages([
                                        'required' => 'Koordinat latitude wajib ditentukan dari peta.',
                                        'numeric'  => 'Latitude harus berupa angka desimal.',
                                        'min'      => 'Koordinat di luar wilayah kabupaten (minimal ' . self::MIN_LAT . ').',
                                        'max'      => 'Koordinat di luar wilayah kabupaten (maksimal ' . self::MAX_LAT . ').',
                                    ]),

                                Forms\Components\TextInput::make('longitude')
                                    ->label('Garis Bujur (Longitude)')
                                    ->placeholder('Contoh: 119.608050')
                                    ->helperText('Batas koordinat wilayah: ' . self::MIN_LNG . ' s.d. ' . self::MAX_LNG)
                                    ->required()
                                    ->numeric()
                                    ->rules(['numeric', 'min:' . self::MIN_LNG, 'max:' . self::MAX_LNG])
                                    ->validationMessages([
                                        'required' => 'Koordinat longitude wajib ditentukan dari peta.',
                                        'numeric'  => 'Longitude harus berupa angka desimal.',
                                        'min'      => 'Koordinat di luar wilayah kabupaten (minimal ' . self::MIN_LNG . ').',
                                        'max'      => 'Koordinat di luar wilayah kabupaten (maksimal ' . self::MAX_LNG . ').',
                                    ]),
                            ])->columns(2)->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Foto & Deskripsi')
                    ->schema([
                        Forms\Components\FileUpload::make('photo_path')
                            ->label('Foto Lokasi')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(2048)
                            ->directory('poi')
                            ->helperText('Format: JPG/PNG/WEBP, maksimal 2MB. Wajib diisi untuk kategori Rumah Kepala Dusun dan Rumah Kepala Desa.')
                            ->required(
                                fn (Forms\Get $get): bool =>
                                    in_array($get('category'), self::PHOTO_REQUIRED_CATEGORIES)
                            )
                            ->validationMessages([
                                'required' => 'Foto lokasi wajib diisi untuk kategori ini.',
                                'max'      => 'Ukuran foto maksimal 2MB.',
                                'mimes'    => 'Format foto harus berupa JPG, PNG, atau WEBP.',
                            ]),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Lokasi')
                            ->placeholder('Tuliskan keterangan singkat tentang lokasi ini...')
                            ->rows(3),
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
                    ->label('Nama Lokasi')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'kantor_desa'       => 'success',
                        'rumah_kepala_desa' => 'warning',
                        'rumah_kadus'       => 'info',
                        'fasilitas_umum'    => 'gray',
                        default             => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => self::CATEGORY_OPTIONS[$state] ?? $state),

                Tables\Columns\TextColumn::make('hamlet')
                    ->label('Dusun')
                    ->searchable(),

                Tables\Columns\TextColumn::make('latitude')
                    ->label('Koordinat')
                    ->formatStateUsing(fn ($record): string => "{$record->latitude}, {$record->longitude}"),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Kategori')
                    ->options(self::CATEGORY_OPTIONS),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Ubah'),
                Tables\Actions\DeleteAction::make()->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()->label('Hapus Terpilih'),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPointOfInterests::route('/'),
            'create' => Pages\CreatePointOfInterest::route('/create'),
            'edit'   => Pages\EditPointOfInterest::route('/{record}/edit'),
        ];
    }
}
