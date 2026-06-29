<?php

namespace App\Filament\Resources\BibleStudyGroups;

use App\Filament\Resources\BibleStudyGroups\Schemas\BibleStudyGroupForm;
use App\Filament\Resources\BibleStudyGroups\Tables\BibleStudyGroupsTable;
use App\Models\BibleStudyGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BibleStudyGroupResource extends Resource
{
    protected static ?string $model = BibleStudyGroup::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $navigationLabel = 'Bible Study Groups';

    protected static string|UnitEnum|null $navigationGroup = 'Membership';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return BibleStudyGroupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BibleStudyGroupsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\MembersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBibleStudyGroups::route('/'),
            'create' => Pages\CreateBibleStudyGroup::route('/create'),
            'view' => Pages\ViewBibleStudyGroup::route('/{record}'),
            'edit' => Pages\EditBibleStudyGroup::route('/{record}/edit'),
        ];
    }
}
