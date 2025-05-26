<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Widgets\AlumnosPorCarreraBarChartWidget;
use App\Filament\Admin\Widgets\BoletasEstadoPieChartWidget;
use App\Filament\Admin\Widgets\PieChartWidget;
use App\Filament\Resources\AlumnoResource;
use App\Filament\Resources\BoletaResource;
use App\Filament\Resources\CarreraResource;
use App\Filament\Resources\ComisionesResource;
use App\Filament\Resources\ConfiguracionResource;
use App\Filament\Resources\MateriaResource;
use App\Filament\Resources\MateriasCorrelativasResource;
use App\Filament\Resources\PlanEstudioResource;
use App\Filament\Resources\PrecioResource;
use App\Filament\Resources\ProfesorResource;
use App\Models\Alumnos;
use App\Models\ComisionMateria;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Pages\Auth\Login;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Leandrocfe\FilamentApexCharts\FilamentApexChartsPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('Panel de Administración')
            ->login(Login::class)
            ->authGuard('web')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->topNavigation()
            ->resources([
                BoletaResource::class,
                PlanEstudioResource::class,
                MateriaResource::class,
                MateriasCorrelativasResource::class,
                CarreraResource::class,
                ConfiguracionResource::class,
                ProfesorResource::class,
                ComisionesResource::class,
                PrecioResource::class,
                AlumnoResource::class,
            ])
            ->navigation(function (NavigationBuilder $builder): NavigationBuilder {
                return $builder->groups([
                    // Dashboard sin grupo visible
                    NavigationGroup::make()
                        ->items([
                            ...Pages\Dashboard::getNavigationItems(),
                        ]),

                    // Grupo Académico
                    NavigationGroup::make('Académico')
                        //->icon('heroicon-o-academic-cap')
                        ->collapsible()
                        ->items([
                            ...PlanEstudioResource::getNavigationItems(),
                            ...MateriaResource::getNavigationItems(),
                            ...MateriasCorrelativasResource::getNavigationItems(),
                            ...ComisionesResource::getNavigationItems(),
                            ...CarreraResource::getNavigationItems(),
                            ...ProfesorResource::getNavigationItems(),
                            ...AlumnoResource::getNavigationItems(),
                        ]),

                    // Grupo Facturación
                    NavigationGroup::make('Facturación')
                        //->icon('heroicon-o-receipt-percent')
                        ->collapsible()
                        ->items([
                            ...BoletaResource::getNavigationItems(),
                            ...PrecioResource::getNavigationItems(),
                        ]),

                    NavigationGroup::make('')
                        //->icon('heroicon-o-receipt-percent')
                        ->collapsible()
                        ->items([
                            ...ConfiguracionResource::getNavigationItems(),
                        ]),
                ]);
            })
            ->pages([
                Pages\Dashboard::class,
                \App\Filament\Pages\EditarCorrelativasMateria::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Admin\Widgets\BoletasEstadoWidget::class,
                
                PieChartWidget::class,
                AlumnosPorCarreraBarChartWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                FilamentApexChartsPlugin::make(),
            ]);
    }
}
