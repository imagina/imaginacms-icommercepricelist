<?php

namespace Modules\Icommercepricelist\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Core\Traits\CanPublishConfiguration;

class IcommercepricelistServiceProvider extends ServiceProvider
{
    use CanPublishConfiguration;
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path('Icommercepricelist', 'Database/Migrations'));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->registerBindings();
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishConfig('icommercepricelist', 'config');
        $this->publishConfig('icommercepricelist', 'crud-fields');
        $this->mergeConfigFrom($this->getModuleConfigFilePath('icommercepricelist', 'permissions'), "asgard.icommercepricelist.permissions");
    }

    private function registerBindings(){
        $this->app->bind(
            'Modules\Icommercepricelist\Repositories\PriceListRepository',
            function () {
                $repository = new \Modules\Icommercepricelist\Repositories\Eloquent\EloquentPriceListRepository(new \Modules\Icommercepricelist\Entities\PriceList());

                if (!config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Icommercepricelist\Repositories\Cache\CachePriceListDecorator($repository);
            }
        );
        $this->app->bind(
            'Modules\Icommercepricelist\Repositories\ProductListRepository',
            function () {
                $repository = new \Modules\Icommercepricelist\Repositories\Eloquent\EloquentProductListRepository(new \Modules\Icommercepricelist\Entities\ProductList());

                if (!config('app.cache')) {
                    return $repository;
                }

                return new \Modules\Icommercepricelist\Repositories\Cache\CacheProductListDecorator($repository);
            }
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/icommercepricelist');

        $sourcePath = module_path('Icommercepricelist', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/icommercepricelist';
        }, \Config::get('view.paths')), [$sourcePath]), 'icommercepricelist');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/icommercepricelist');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'icommercepricelist');
        } else {
            $this->loadTranslationsFrom(module_path('Icommercepricelist', 'Resources/lang'), 'icommercepricelist');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
