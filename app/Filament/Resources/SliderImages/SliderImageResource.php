<?php

namespace App\Filament\Resources\SliderImages;

use App\Filament\Resources\SliderImages\Schemas\SliderImageForm;
use App\Filament\Resources\SliderImages\Tables\SliderImagesTable;
use App\Models\SliderImage;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SliderImageResource extends Resource
{
    protected static ?string $model = SliderImage::class;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static ?string $navigationLabel = 'Slider Images';

    protected static ?string $modelLabel = 'Slider Image';

    protected static ?string $pluralModelLabel = 'Slider Images';

    protected static string|UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 101;

    public static function form(Schema $schema): Schema
    {
        return SliderImageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SliderImagesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSliderImages::route('/'),
            'view' => Pages\ViewSliderImage::route('/{record}'),
            'edit' => Pages\EditSliderImage::route('/{record}/edit'),
        ];
    }
}
