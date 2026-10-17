<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InstitutionResource\Pages;
use App\Models\Institution;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class InstitutionResource extends Resource
{
    protected static ?string $model = Institution::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationGroup = 'Data Desa';

    protected static ?string $navigationLabel = 'Lembaga Desa';

    protected static ?string $modelLabel = 'Lembaga Desa';

    protected static ?string $pluralModelLabel = 'Lembaga Desa';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Lembaga Desa')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lembaga')
                            ->placeholder('Contoh: Badan Permusyawaratan Desa')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                            ->validationMessages([
                                'required' => 'Nama lembaga wajib diisi.',
                            ])
                            ->maxLength(255),
                        Forms\Components\TextInput::make('abbreviation')
                            ->label('Singkatan Lembaga')
                            ->placeholder('Contoh: BPD, PKK, Karang Taruna, LPM')
                            ->maxLength(50),
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug URL')
                            ->placeholder('bpd')
                            ->required()
                            ->unique(Institution::class, 'slug', ignoreRecord: true)
                            ->validationMessages([
                                'required' => 'Slug URL wajib diisi.',
                                'unique' => 'Slug ini sudah dipakai oleh lembaga lain.',
                            ])
                            ->maxLength(255),
                        Forms\Components\TextInput::make('order')
                            ->label('Urutan Tampilan')
                            ->numeric()
                            ->default(0)
                            ->helperText('Dapat diatur langsung melalui urutan drag-and-drop pada tabel.'),
                        Forms\Components\FileUpload::make('logo_path')
                            ->label('Logo / Lambang Lembaga')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(2048)
                            ->directory('institutions')
                            ->helperText('Format: JPG/PNG/WEBP, maksimal 2MB.')
                            ->validationMessages([
                                'max' => 'Ukuran logo maksimal 2MB.',
                                'mimes' => 'Format logo harus berupa JPG, PNG, atau WEBP.',
                            ]),
                        Forms\Components\Textarea::make('description')
                            ->label('Profil & Tugas Lembaga')
                            ->placeholder('Tuliskan visi, misi, atau deskripsi peran lembaga ini di desa...')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->defaultSort('order', 'asc')
            ->columns([
                Tables\Columns\ImageColumn::make('logo_path')
                    ->label('Logo')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Lembaga')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('abbreviation')
                    ->label('Singkatan')
                    ->badge(),
                Tables\Columns\TextColumn::make('members_count')
                    ->label('Jumlah Pengurus')
                    ->counts('members'),
                Tables\Columns\TextColumn::make('order')
                    ->label('Urutan')
                    ->sortable(),
            ])
            ->filters([
                //
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
            'index' => Pages\ListInstitutions::route('/'),
            'create' => Pages\CreateInstitution::route('/create'),
            'edit' => Pages\EditInstitution::route('/{record}/edit'),
        ];
    }
}
