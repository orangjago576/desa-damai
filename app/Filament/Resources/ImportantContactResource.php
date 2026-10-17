<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ImportantContactResource\Pages;
use App\Models\ImportantContact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ImportantContactResource extends Resource
{
    protected static ?string $model = ImportantContact::class;

    protected static ?string $navigationIcon = 'heroicon-o-phone';

    protected static ?string $navigationGroup = 'Data Desa';

    protected static ?string $navigationLabel = 'Kontak Penting';

    protected static ?string $modelLabel = 'Kontak Penting';

    protected static ?string $pluralModelLabel = 'Direktori Kontak Penting';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Pejabat / Kontak')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Pejabat')
                            ->placeholder('Contoh: Bapak Suripto')
                            ->required()
                            ->validationMessages([
                                'required' => 'Nama pejabat wajib diisi.',
                            ])
                            ->maxLength(255),

                        Forms\Components\TextInput::make('position')
                            ->label('Jabatan Publik')
                            ->placeholder('Contoh: Kepala Desa, Kepala Dusun 1, Babinsa')
                            ->required()
                            ->validationMessages([
                                'required' => 'Jabatan publik wajib diisi.',
                            ])
                            ->maxLength(255),

                        Forms\Components\TextInput::make('work_area')
                            ->label('Wilayah Tugas / Dusun')
                            ->placeholder('Contoh: Dusun Krajan, seluruh desa')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone_number')
                            ->label('Nomor HP / WhatsApp')
                            ->placeholder('08xxxxxxxxxx atau +62xxxxxxxxxx')
                            ->helperText('Nomor akan dikonversi otomatis ke format internasional (62xxx) saat disimpan.')
                            ->required()
                            ->tel()
                            ->maxLength(50)
                            ->validationMessages([
                                'required' => 'Nomor HP/WhatsApp wajib diisi.',
                            ]),

                        Forms\Components\Select::make('number_type')
                            ->label('Jenis Nomor')
                            ->options([
                                'dinas'   => 'Nomor Dinas / Kantor',
                                'pribadi' => 'Nomor Pribadi',
                            ])
                            ->default('dinas')
                            ->required()
                            ->validationMessages([
                                'required' => 'Jenis nomor wajib dipilih.',
                            ]),

                        Forms\Components\DatePicker::make('last_verified_at')
                            ->label('Tanggal Terakhir Diverifikasi')
                            ->helperText('Tandai kapan terakhir nomor ini dikonfirmasi masih aktif.')
                            ->default(now()),
                    ])->columns(2),

                Forms\Components\Section::make('Status Persetujuan Publikasi')
                    ->description('Nomor HP hanya akan tampil di website publik jika pejabat yang bersangkutan telah memberikan persetujuan. Default: tidak disetujui.')
                    ->schema([
                        Forms\Components\Toggle::make('is_approved_for_publication')
                            ->label('Diizinkan Tampil di Website Publik')
                            ->helperText('Aktifkan hanya setelah pejabat yang bersangkutan memberikan persetujuan secara sadar. Kepatuhan UU PDP.')
                            ->default(false)
                            ->onColor('success')
                            ->offColor('danger'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Pejabat')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('position')
                    ->label('Jabatan')
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Nomor Kontak')
                    ->searchable(),

                Tables\Columns\TextColumn::make('number_type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'dinas'   => 'info',
                        'pribadi' => 'gray',
                        default   => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'dinas'   => 'Dinas',
                        'pribadi' => 'Pribadi',
                        default   => $state,
                    }),

                Tables\Columns\IconColumn::make('is_approved_for_publication')
                    ->label('Disetujui')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle'),

                Tables\Columns\TextColumn::make('last_verified_at')
                    ->label('Terverifikasi')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_approved_for_publication')
                    ->label('Status Persetujuan')
                    ->trueLabel('Sudah Disetujui')
                    ->falseLabel('Belum Disetujui'),
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
            'index'  => Pages\ListImportantContacts::route('/'),
            'create' => Pages\CreateImportantContact::route('/create'),
            'edit'   => Pages\EditImportantContact::route('/{record}/edit'),
        ];
    }
}
