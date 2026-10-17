<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityCategoryResource\Pages;
use App\Models\ActivityCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ActivityCategoryResource extends Resource
{
    protected static ?string $model = ActivityCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Konten';

    protected static ?string $navigationLabel = 'Kategori Kegiatan';

    protected static ?string $modelLabel = 'Kategori Kegiatan';

    protected static ?string $pluralModelLabel = 'Kategori Kegiatan';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nama Kategori')
                    ->placeholder('Contoh: Pembangunan, Posyandu, Gotong Royong')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state)))
                    ->validationMessages([
                        'required' => 'Nama kategori wajib diisi.',
                    ])
                    ->maxLength(255),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug URL')
                    ->placeholder('contoh-kategori')
                    ->required()
                    ->unique(ActivityCategory::class, 'slug', ignoreRecord: true)
                    ->validationMessages([
                        'required' => 'Slug URL wajib terisi.',
                        'unique' => 'Slug URL sudah digunakan oleh kategori lain.',
                    ])
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug URL')
                    ->searchable(),
                Tables\Columns\TextColumn::make('activities_count')
                    ->label('Jumlah Kegiatan')
                    ->counts('activities'),
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
            'index' => Pages\ListActivityCategories::route('/'),
            'create' => Pages\CreateActivityCategory::route('/create'),
            'edit' => Pages\EditActivityCategory::route('/{record}/edit'),
        ];
    }
}
