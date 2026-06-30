<?php

namespace App\Providers;

use App\Filament\Pages\Dashboard;
use App\Filament\Resources\Members\Widgets\MemberGrowthChart;
use App\Filament\Widgets\AttendanceTrendWidget;
use App\Filament\Widgets\ChurchStatsWidget;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Enums\ThemeMode;
use Filament\FontProviders\GoogleFontProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->spa()
            ->profile()
            ->globalSearch(false)
            ->colors([
                'danger' => Color::Rose,
                'gray' => Color::Slate,
                'info' => Color::Sky,
                'primary' => Color::Indigo,
                'success' => Color::Emerald,
                'warning' => Color::Amber,
            ])
            ->font('Inter', provider: GoogleFontProvider::class)
            ->brandName(fn () => config('app.name'))
            ->favicon(asset('favicon.ico'))
            ->defaultThemeMode(ThemeMode::Light)
            // ->viteTheme('resources/css/filament/admin/theme.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->readOnlyRelationManagersOnResourceViewPagesByDefault(false)
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                ChurchStatsWidget::class,
                MemberGrowthChart::class,
                AttendanceTrendWidget::class,
            ])
            ->middleware([
                VerifyCsrfToken::class,
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ShareErrorsFromSession::class,
                SubstituteBindings::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentShieldPlugin::make()
                    ->navigationGroup('Administration'),
            ]);
    }
}
