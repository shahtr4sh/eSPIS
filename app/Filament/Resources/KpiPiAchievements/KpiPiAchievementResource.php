<?php
namespace App\Filament\Resources;

use App\Filament\Resources\KpiPiAchievements\Pages;
use App\Models\KpiPiAchievement;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Actions;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class KpiPiAchievementResource extends Resource
{
    protected static ?string $model = KpiPiAchievement::class;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static string|null|\UnitEnum $navigationGroup = 'UniSHAMS KPI/PI';
    protected static ?string $navigationLabel = 'Quarterly Achievement';

    public static function schema(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('kpi_pi_id')
                ->relationship('kpiPi', 'title')
                ->searchable()
                ->preload()
                ->required()
                ->label('KPI/PI'),

            Forms\Components\TextInput::make('year')
                ->numeric()
                ->required(),

            Forms\Components\Select::make('quarter')
                ->options([
                    'Q1' => 'Q1',
                    'Q2' => 'Q2',
                    'Q3' => 'Q3',
                    'Q4' => 'Q4',
                ])
                ->required(),

            Forms\Components\TextInput::make('quarter_target')
                ->numeric(),

            Forms\Components\TextInput::make('actual_value')
                ->numeric()
                ->live(onBlur: true),

            Forms\Components\TextInput::make('achievement_percentage')
                ->numeric()
                ->readOnly(),

            Forms\Components\DatePicker::make('achievement_date'),

            Forms\Components\Textarea::make('remarks'),

            Forms\Components\FileUpload::make('evidence_path')
                ->directory('achievement-evidence'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kpiPi.code')->label('Code')->searchable(),
                Tables\Columns\TextColumn::make('kpiPi.title')->label('Title')->wrap()->searchable(),
                Tables\Columns\TextColumn::make('year')->sortable(),
                Tables\Columns\TextColumn::make('quarter')->badge(),
                Tables\Columns\TextColumn::make('quarter_target'),
                Tables\Columns\TextColumn::make('actual_value'),
                Tables\Columns\TextColumn::make('achievement_percentage')->suffix('%'),
            ])
            ->actions([
                Actions\ViewAction::make(),
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKpiPiAchievements::route('/'),
            'create' => Pages\CreateKpiPiAchievement::route('/create'),
            'view' => Pages\ViewKpiPiAchievement::route('/{record}'),
            'edit' => Pages\EditKpiPiAchievement::route('/{record}/edit'),
        ];
    }
}
