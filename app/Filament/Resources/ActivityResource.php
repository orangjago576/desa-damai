<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use App\Models\Activity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Kegiatan Desa';

    protected static ?string $modelLabel = 'Kegiatan';

    protected static ?string $pluralModelLabel = 'Kegiatan Desa';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Kegiatan')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('Kategori Kegiatan')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->validationMessages([
                                'required' => 'Kategori kegiatan wajib dipilih.',
                            ]),
                        Forms\Components\TextInput::make('title')
                            ->label('Judul Kegiatan')
                            ->placeholder('Contoh: Pelaksanaan Posyandu Balita & Lansia')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                            ->validationMessages([
                                'required' => 'Judul kegiatan wajib diisi.',
                            ])
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug URL')
                            ->helperText('Dibuat otomatis dari judul, digunakan untuk alamat link kegiatan.')
                            ->required()
                            ->unique(Activity::class, 'slug', ignoreRecord: true)
                            ->validationMessages([
                                'required' => 'Slug URL wajib terisi.',
                                'unique' => 'Slug ini sudah digunakan pada kegiatan lain.',
                            ])
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('activity_date')
                            ->label('Tanggal Kegiatan')
                            ->required()
                            ->default(now())
                            ->validationMessages([
                                'required' => 'Tanggal kegiatan wajib diisi.',
                            ]),
                        Forms\Components\TextInput::make('location')
                            ->label('Lokasi Kegiatan')
                            ->placeholder('Contoh: Balai Desa, Dusun 1')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('status')
                            ->label('Status Publikasi (Aktifkan untuk Terbit)')
                            ->helperText('Jika nonaktif berstatus Draf, jika aktif berstatus Terbit.')
                            ->dehydrateStateUsing(fn ($state) => $state ? 'published' : 'draft')
                            ->formatStateUsing(fn ($state) => $state === 'published')
                            ->default(false),
                        Forms\Components\Hidden::make('user_id')
                            ->default(fn () => auth()->id()),
                    ])->columns(2),

                Forms\Components\Section::make('Konten & Ringkasan')
                    ->schema([
                        Forms\Components\Textarea::make('excerpt')
                            ->label('Ringkasan (Excerpt)')
                            ->placeholder('Tuliskan ringkasan singkat kegiatan untuk cuplikan medsos...')
                            ->helperText('Maksimal 200 karakter. Digunakan untuk cuplikan daftar berita dan pengantar medsos.')
                            ->maxLength(200)
                            ->validationMessages([
                                'max' => 'Ringkasan kegiatan maksimal 200 karakter.',
                            ])
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('content')
                            ->label('Isi Konten Kegiatan')
                            ->placeholder('Tuliskan detail pelaksanaan kegiatan di sini...')
                            ->toolbarButtons([
                                'bold',
                                'bulletList',
                                'h2',
                                'h3',
                                'italic',
                                'link',
                                'orderedList',
                                'redo',
                                'undo',
                            ])
                            ->required()
                            ->validationMessages([
                                'required' => 'Isi konten kegiatan wajib dituliskan.',
                            ])
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Media Gambar')
                    ->description('Batas ukuran maksimal tiap berkas adalah 2MB dengan format JPG, PNG, atau WEBP.')
                    ->schema([
                        Forms\Components\FileUpload::make('cover_image')
                            ->label('Gambar Sampul (Cover)')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(2048)
                            ->directory('activities/covers')
                            ->helperText('Format: JPG/PNG/WEBP, maks 2MB. Tampil di halaman detail website.')
                            ->validationMessages([
                                'max' => 'Ukuran foto sampul maksimal 2MB.',
                                'mimes' => 'Format sampul harus berupa JPG, PNG, atau WEBP.',
                            ]),
                        Forms\Components\FileUpload::make('og_image')
                            ->label('Gambar Khusus Medsos (OG Image)')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(2048)
                            ->directory('activities/og')
                            ->helperText('Format: JPG/PNG/WEBP, maks 2MB. Gambar khusus yang muncul saat link dibagikan ke WhatsApp / Facebook.')
                            ->validationMessages([
                                'max' => 'Ukuran OG Image maksimal 2MB.',
                                'mimes' => 'Format OG Image harus berupa JPG, PNG, atau WEBP.',
                            ]),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('Sampul')
                    ->circular(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('activity_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'published' => 'Terbit',
                        'draft' => 'Draf',
                        default => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name'),
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'published' => 'Terbit',
                        'draft' => 'Draf',
                    ]),
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
            ->defaultSort('activity_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
            'create' => Pages\CreateActivity::route('/create'),
            'edit' => Pages\EditActivity::route('/{record}/edit'),
        ];
    }
}
