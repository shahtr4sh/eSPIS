<?php
namespace App\Filament\Resources;

use App\Filament\Resources\KpiPis\Pages;
use App\Models\KpiPi;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;

class KpiPiResource extends Resource
{
    protected static ?string $model = KpiPi::class;

    protected static string|null|\BackedEnum $navigationIcon = 'heroicon-o-chart-bar';
    protected static string|null|\UnitEnum $navigationGroup = 'UniSHAMS KPI/PI';
    protected static ?string $navigationLabel = 'KPI / PI Master';

    public static function schema(Schema $schema): Schema
    {
        return $schema->schema([
            TextInput::make('code')
                ->label('KPI/PI Code')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(255),

            Select::make('type')
                ->options([
                    'KPI' => 'KPI',
                    'PI'  => 'PI',
                ])
                ->required(),

            TextInput::make('thrust')
                ->label('Thrust / Strategic Theme'),

            TextInput::make('prime_objective')
                ->required()
                ->maxLength(255),

            Textarea::make('strategy')
                ->required()
                ->rows(3),

            TextInput::make('dimension')
                ->required()
                ->maxLength(255),

            TextInput::make('title')
                ->label('KPI/PI Title')
                ->required()
                ->maxLength(255),

            TextInput::make('reference')
                ->maxLength(255),

            Textarea::make('operational_definition')
                ->rows(4),

            Select::make('data_source_id')
                ->relationship('dataSource', 'name')
                ->searchable()
                ->preload()
                ->label('Source of Data'),

            TextInput::make('distribution_type')
                ->label('Distribution Type'),

            Select::make('responsible_office_id')
                ->relationship('office', 'name')
                ->searchable()
                ->preload()
                ->label('Responsible Office'),

            TextInput::make('baseline_value')
                ->numeric(),

            TextInput::make('annual_target')
                ->numeric(),

            Select::make('status')
                ->options([
                    'Draft' => 'Draft',
                    'Active' => 'Active',
                    'Inactive' => 'Inactive',
                ])
                ->default('Draft')
                ->required(),

            Textarea::make('remarks')
                ->rows(3),

            FileUpload::make('attachment_path')
                ->directory('kpi-attachments')
                ->label('Supporting Document'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->searchable()->sortable(),
                TextColumn::make('type')->badge(),
                TextColumn::make('title')->searchable()->wrap(),
                TextColumn::make('dimension')->searchable(),
                TextColumn::make('office.name')->label('Responsible Office')->searchable(),
                TextColumn::make('status')->badge(),
                TextColumn::make('created_at')->dateTime('d/m/Y H:i'),
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
                Actions\DeleteAction::make(),
            ]);
            /*->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);*/
    }

    public static function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();
        return $data;
    }

    public static function mutateFormDataBeforeSave(array $data): array
    {
        $data['updated_by'] = Auth::id();
        return $data;
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
