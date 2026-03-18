<?php

namespace App\Filament\Resources\KpiPis;

use App\Filament\Resources\KpiPis\Pages;
use App\Models\KpiPi;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Actions;
use Filament\Tables\Columns\TextColumn;

class KpiPiResource extends Resource
{
    protected static ?string $model = KpiPi::class;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-document-text';
    protected static string|null|\UnitEnum $navigationGroup = 'eSPIS Management';
    protected static ?string $navigationLabel = 'KPI / PI';
    protected static ?string $modelLabel = 'KPI / PI';
    protected static ?string $pluralModelLabel = 'KPI / PI';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Grid::make(1)->schema([

                Section::make('KPI / PI Details')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('code')
                                ->label('Code')
                                ->required()
                                ->maxLength(255)
                                ->unique(ignoreRecord: true),

                            Select::make('type')
                                ->label('Type')
                                ->options([
                                    'KPI' => 'KPI',
                                    'PI' => 'PI',
                                ])
                                ->required(),

                            TextInput::make('thrust')
                                ->label('Thrust')
                                ->numeric()
                                ->nullable(),
                        ]),

                        Grid::make(2)->schema([
                            Select::make('dimension')
                                ->label('Dimension')
                                ->required()
                                ->options([
                                    'KECEMERLANGAN AKADEMIK DAN PENGANTARABANGSAAN' => 'KECEMERLANGAN AKADEMIK DAN PENGANTARABANGSAAN',
                                    'MEMPERKASAKAN PENYELIDIKAN DAN PENERBITAN BERIMPAK TINGGI' => 'MEMPERKASAKAN PENYELIDIKAN DAN PENERBITAN BERIMPAK TINGGI',
                                    'KECEMERLANGAN PELAJAR SECARA HOLISTIK' => 'KECEMERLANGAN PELAJAR SECARA HOLISTIK',
                                    'KELESTARIAN INSTITUSI' => 'KELESTARIAN INSTITUSI',
                                    'KEMAMPANAN KEWANGAN DAN SUMBER PENDAPATAN' => 'KEMAMPANAN KEWANGAN DAN SUMBER PENDAPATAN',
                                    'TADBIR URUS YANG BAIK' => 'TADBIR URUS YANG BAIK',
                                ])
                                ->searchable(),

                            TextArea::make('indicator')
                                ->label('Indicator')
                                ->maxLength(255)
                                ->nullable()
                                ->rows(3),
                        ]),

                        TextInput::make('title')
                            ->label('Title')
                            ->maxLength(255)
                            ->nullable(),

                        TextInput::make('prime_objective')
                            ->label('Prime Objective')
                            ->maxLength(255)
                            ->nullable(),

                        Textarea::make('strategy')
                            ->label('Strategy')
                            ->rows(4)
                            ->nullable(),

                        TextInput::make('reference')
                            ->label('Reference')
                            ->maxLength(255)
                            ->nullable(),

                        Textarea::make('operational_definition')
                            ->label('Operational Definition')
                            ->rows(8)
                            ->nullable(),
                    ]),

                Section::make('Source and Responsible Office')
                    ->columnSpanFull()
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('data_source_id')
                                ->label('Source of Data')
                                ->relationship('dataSource', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable(),

                            Select::make('responsible_office_id')
                                ->label('Responsible Office')
                                ->relationship('responsibleOffice', 'name')
                                ->searchable()
                                ->preload()
                                ->nullable(),

                            Select::make('status')
                                ->label('Status')
                                ->options([
                                    'Draft' => 'Draft',
                                    'Active' => 'Active',
                                    'Inactive' => 'Inactive',
                                ])
                                ->default('Draft')
                                ->required(),
                        ]),

                        Grid::make(3)->schema([
                            TextInput::make('measurement')
                                ->label('Measurement')
                                ->maxLength(50)
                                ->nullable(),

                            TextInput::make('status')
                                ->label('Status')
                                ->hidden(),
                        ]),
                    ]),

                Section::make('Target Input Mode')
                    ->columnSpanFull()
                    ->schema([
                        Select::make('target_input_mode')
                            ->label('Mode Sasaran')
                            ->options([
                                'annual_overall' => 'Sasaran Tahunan Keseluruhan',
                                'unit_quarterly' => 'Sasaran Mengikut Unit & Quarter',
                            ])
                            ->default('annual_overall')
                            ->required()
                            ->live(),

                        Grid::make(2)->schema([
                            TextInput::make('sasaran_tahunan')
                                ->label('Sasaran Tahunan')
                                ->numeric()
                                ->nullable()
                                ->visible(fn ($get) => $get('target_input_mode') === 'annual_overall'),

                            TextInput::make('pencapaian_tahunan')
                                ->label('Pencapaian Tahunan')
                                ->numeric()
                                ->nullable()
                                ->visible(fn ($get) => $get('target_input_mode') === 'annual_overall'),
                        ]),
                    ]),

                Section::make('Target & Achievement by Unit and Quarter')
                    ->columnSpanFull()
                    ->description('Isi sasaran dan pencapaian mengikut unit dan quarter.')
                    ->visible(fn ($get) => $get('target_input_mode') === 'unit_quarterly')
                    ->schema([
                        Repeater::make('distributionDetails')
                            ->relationship('distributionDetails')
                            ->label('Distribution Details')
                            ->schema([
                                Grid::make(4)->schema([
                                    Select::make('quarter')
                                        ->label('Quarter')
                                        ->options([
                                            'Q1' => 'Q1',
                                            'Q2' => 'Q2',
                                            'Q3' => 'Q3',
                                            'Q4' => 'Q4',
                                        ])
                                        ->required(),

                                    Select::make('distribution_unit_id')
                                        ->label('Unit')
                                        ->relationship('distributionUnit', 'code')
                                        ->searchable()
                                        ->preload()
                                        ->required(),

                                    TextInput::make('target_value')
                                        ->label('Target Value')
                                        ->numeric()
                                        ->nullable(),

                                    TextInput::make('achievement_value')
                                        ->label('Achievement Value')
                                        ->numeric()
                                        ->nullable(),
                                ]),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Add Unit Quarter Detail')
                            ->reorderable(false)
                            ->collapsible()
                            ->cloneable(false)
                            ->columnSpanFull(),
                    ]),

                Section::make('Weight Distribution by Unit')
                    ->columnSpanFull()
                    ->description('Masukkan weight bagi setiap unit. Contoh: KUBRA = 8, KWISH = 4, KSU = 8.')
                    ->schema([
                        Repeater::make('distributionWeights')
                            ->relationship('distributionWeights')
                            ->label('Distribution Weights')
                            ->schema([
                                Grid::make(2)->schema([
                                    Select::make('distribution_unit_id')
                                        ->label('Unit')
                                        ->relationship('distributionUnit', 'code')
                                        ->searchable()
                                        ->preload()
                                        ->required(),

                                    TextInput::make('weight_value')
                                        ->label('%Distribute / Weight')
                                        ->numeric()
                                        ->required(),
                                ]),
                            ])
                            ->defaultItems(0)
                            ->addActionLabel('Add Weight')
                            ->reorderable(false)
                            ->collapsible()
                            ->cloneable(false)
                            ->columnSpanFull(),
                    ]),

                Section::make('Lampiran')
                    ->columnSpanFull()
                    ->schema([
                        FileUpload::make('attachment_path')
                            ->label('Attachment')
                            ->directory('kpi-pi-attachments')
                            ->preserveFilenames()
                            ->nullable(),
                    ]),

            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Type')
                    ->badge(),

                TextColumn::make('dimension')
                    ->label('Dimension')
                    ->searchable()
                    ->wrap()
                    ->limit(50),

                TextColumn::make('indicator')
                    ->label('Indicator')
                    ->searchable()
                    ->wrap()
                    ->limit(50),

                TextColumn::make('thrust')
                    ->label('Thrust')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'KPI' => 'KPI',
                        'PI' => 'PI',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'Draft' => 'Draft',
                        'Active' => 'Active',
                        'Inactive' => 'Inactive',
                    ]),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKpiPis::route('/'),
            'create' => Pages\CreateKpiPi::route('/create'),
            'view' => Pages\ViewKpiPi::route('/{record}'),
            'edit' => Pages\EditKpiPi::route('/{record}/edit'),
        ];
    }
}
