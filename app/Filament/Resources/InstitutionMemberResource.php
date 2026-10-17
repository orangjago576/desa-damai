<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InstitutionMemberResource\Pages;
use App\Models\InstitutionMember;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InstitutionMemberResource extends Resource
{
    protected static ?string $model = InstitutionMember::class;

    protected static ?string $navigationIcon = 'heroicon-o-identification';

    protected static ?string $navigationGroup = 'Data Desa';

    protected static ?string $navigationLabel = 'Anggota Lembaga';

    protected static ?string $modelLabel = 'Anggota Lembaga';

    protected static ?string $pluralModelLabel = 'Anggota Lembaga';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Pengurus / Anggota Lembaga')
                    ->schema([
                        Forms\Components\Select::make('institution_id')
                            ->label('Lembaga Desa')
                            ->relationship('institution', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->validationMessages([
                                'required' => 'Pilih lembaga desa tempat anggota bernaung.',
                            ]),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->placeholder('Contoh: Siti Rahmawati')
                            ->helperText('Hanya masukkan nama dan gelar. Dilarang memasukkan data pribadi seperti NIK/Nomor KK.')
                            ->required()
                            ->validationMessages([
                                'required' => 'Nama anggota wajib diisi.',
                            ])
                            ->maxLength(255),
                        Forms\Components\TextInput::make('position')
                            ->label('Jabatan dalam Lembaga')
                            ->placeholder('Contoh: Ketua, Sekretaris, Bendahara, Anggota')
                            ->required()
                            ->validationMessages([
                                'required' => 'Jabatan anggota wajib diisi.',
                            ])
                            ->maxLength(255),
                        Forms\Components\TextInput::make('period')
                            ->label('Masa Bakti / Periode')
                            ->placeholder('Contoh: 2021 - 2027')
                            ->maxLength(100),
                        Forms\Components\TextInput::make('order')
                            ->label('Urutan Struktur')
                            ->numeric()
                            ->default(0)
                            ->helperText('Bisa juga diurutkan melalui drag-and-drop pada daftar tabel.'),
                        Forms\Components\FileUpload::make('photo_path')
                            ->label('Foto Anggota')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(2048)
                            ->directory('institution-members')
                            ->helperText('Format: JPG/PNG/WEBP, maksimal 2MB.')
                            ->validationMessages([
                                'max' => 'Ukuran foto maksimal 2MB.',
                                'mimes' => 'Format foto harus berupa JPG, PNG, atau WEBP.',
                            ]),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('order')
            ->defaultSort('order', 'asc')
            ->columns([
                Tables\Columns\ImageColumn::make('photo_path')
                    ->label('Foto')
                    ->circular(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Anggota')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('institution.name')
                    ->label('Lembaga')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('position')
                    ->label('Jabatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('period')
                    ->label('Periode'),
                Tables\Columns\TextColumn::make('order')
                    ->label('Urutan')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('institution_id')
                    ->label('Lembaga')
                    ->relationship('institution', 'name'),
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
            'index' => Pages\ListInstitutionMembers::route('/'),
            'create' => Pages\CreateInstitutionMember::route('/create'),
            'edit' => Pages\EditInstitutionMember::route('/{record}/edit'),
        ];
    }
}
