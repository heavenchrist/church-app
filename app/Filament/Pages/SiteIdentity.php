<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

class SiteIdentity extends Page implements HasForms
{
    use InteractsWithForms;

    protected static \BackedEnum|string|null $navigationIcon = Heroicon::OutlinedPaintBrush;

    protected static ?string $navigationLabel = 'Site Identity';

    protected static ?string $title = 'Site Identity';

    protected static string|UnitEnum|null $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 99;

    protected string $view = 'filament.pages.site-identity';

    protected static ?string $slug = 'site-identity';

    public array $data = [];

    public function mount(): void
    {
        $this->form->fill($this->getSettings());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->schema([
                Section::make('Logo & Branding')
                    ->description('Upload your site logo and favicon')
                    ->schema([
                        Grid::make(2)->schema([
                            FileUpload::make('logo')
                                ->label('Site Logo')
                                ->image()
                                ->disk('public')
                                ->directory('settings'),
                            FileUpload::make('favicon')
                                ->label('Favicon')
                                ->image()
                                ->disk('public')
                                ->directory('settings'),
                        ]),
                    ]),

                Section::make('Site Identity')
                    ->description('Basic information about your church')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('app_name')
                                ->label('Site Name')
                                ->placeholder('Glory Assembly')
                                ->maxLength(255),
                            TextInput::make('tagline')
                                ->label('Tagline')
                                ->placeholder('A place where lives are transformed')
                                ->maxLength(255),
                        ]),
                        Textarea::make('description')
                            ->label('Site Description')
                            ->placeholder('Brief description of your church...')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),

                Section::make('Contact Information')
                    ->description('How visitors can reach you')
                    ->schema([
                        Grid::make(2)->schema([
                            Textarea::make('address')
                                ->label('Address')
                                ->placeholder('Near Ritz Hotel')
                                ->rows(2),
                            TextInput::make('city')
                                ->label('City')
                                ->placeholder('Accra')
                                ->maxLength(255),
                            TextInput::make('phone')
                                ->label('Phone Number')
                                ->tel()
                                ->placeholder('0244000000'),
                            TextInput::make('email')
                                ->label('Email Address')
                                ->email()
                                ->placeholder('info@church.com'),
                        ]),
                    ]),

                Section::make('Social Media')
                    ->description('Your social media links')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('facebook_url')
                                ->label('Facebook URL')
                                ->url()
                                ->placeholder('https://facebook.com/yourpage'),
                            TextInput::make('twitter_url')
                                ->label('Twitter URL')
                                ->url()
                                ->placeholder('https://twitter.com/yourpage'),
                            TextInput::make('instagram_url')
                                ->label('Instagram URL')
                                ->url()
                                ->placeholder('https://instagram.com/yourpage'),
                            TextInput::make('youtube_url')
                                ->label('YouTube URL')
                                ->url()
                                ->placeholder('https://youtube.com/yourchannel'),
                        ]),
                    ]),

                Section::make('Service Times')
                    ->description('When your services are held')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('sunday_school_time')
                                ->label('Sunday School Time')
                                ->placeholder('8:00 AM'),
                            TextInput::make('morning_worship_time')
                                ->label('Morning Worship Time')
                                ->placeholder('9:30 AM'),
                            TextInput::make('afternoon_service_time')
                                ->label('Afternoon Service Time')
                                ->placeholder('5:00 PM'),
                            TextInput::make('bible_study_time')
                                ->label('Bible Study Time')
                                ->placeholder('6:30 PM'),
                            TextInput::make('bible_study_day')
                                ->label('Bible Study Day')
                                ->placeholder('Wednesday'),
                        ]),
                    ]),
            ]);
    }

    protected function getFormActions(): array
    {
        return [];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(
                ['key' => $key],
                ['key' => $key, 'value' => $value, 'type' => is_array($value) ? 'image' : 'text']
            );
        }

        Notification::make()
            ->title('Settings saved successfully')
            ->success()
            ->send();
    }

    protected function getSettings(): array
    {
        $settings = Setting::pluck('value', 'key')->toArray();

        return [
            'logo' => $settings['logo'] ?? null,
            'favicon' => $settings['favicon'] ?? null,
            'app_name' => $settings['app_name'] ?? 'Glory Assembly',
            'tagline' => $settings['tagline'] ?? 'A place where lives are transformed',
            'description' => $settings['description'] ?? null,
            'address' => $settings['address'] ?? null,
            'city' => $settings['city'] ?? 'Accra',
            'phone' => $settings['phone'] ?? null,
            'email' => $settings['email'] ?? null,
            'facebook_url' => $settings['facebook_url'] ?? null,
            'twitter_url' => $settings['twitter_url'] ?? null,
            'instagram_url' => $settings['instagram_url'] ?? null,
            'youtube_url' => $settings['youtube_url'] ?? null,
            'sunday_school_time' => $settings['sunday_school_time'] ?? '8:00 AM',
            'morning_worship_time' => $settings['morning_worship_time'] ?? '9:30 AM',
            'afternoon_service_time' => $settings['afternoon_service_time'] ?? '5:00 PM',
            'bible_study_time' => $settings['bible_study_time'] ?? '6:30 PM',
            'bible_study_day' => $settings['bible_study_day'] ?? 'Wednesday',
        ];
    }
}
