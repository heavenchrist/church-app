<?php

namespace App\Filament\Resources\Ministries;

use App\Filament\Resources\Ministries\Schemas\MinistryForm;
use App\Filament\Resources\Ministries\Tables\MinistriesTable;
use App\Models\Ministry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MinistryResource extends Resource
{
    protected static ?string $model = Ministry::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static ?string $navigationLabel = 'Ministries';

    protected static string|UnitEnum|null $navigationGroup = 'Membership';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return MinistryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MinistriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MembersRelationManager::class,
            RelationManagers\ExecutivesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMinistries::route('/'),
            'create' => Pages\CreateMinistry::route('/create'),
            'view' => Pages\ViewMinistry::route('/{record}'),
            'edit' => Pages\EditMinistry::route('/{record}/edit'),
        ];
    }
}
