<?php

namespace Tirjok\CrudGenerator;

use Illuminate\Support\ServiceProvider;

class CrudGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/crudgenerator.php' => config_path('crudgenerator.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../publish/views/app.blade.php' => base_path('resources/views/layouts/app.blade.php'),
        ], 'view');

        $this->publishes([
            __DIR__ . '/../publish/Repositories/' => app_path('Repositories'),
        ]);

        $this->publishes([
            __DIR__ . '/../publish/Services/' => app_path('Services'),
        ]);

        $this->publishes([
            __DIR__ . '/stubs/' => base_path('resources/crud-generator/'),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(
            'Tirjok\CrudGenerator\Commands\CrudCommand',
            'Tirjok\CrudGenerator\Commands\CrudControllerCommand',
            'Tirjok\CrudGenerator\Commands\CrudModelCommand',
            'Tirjok\CrudGenerator\Commands\CrudMigrationCommand',
            'Tirjok\CrudGenerator\Commands\CrudViewCommand',
            'Tirjok\CrudGenerator\Commands\CrudLangCommand',
            'Tirjok\CrudGenerator\Commands\CrudServiceCommand',
            'Tirjok\CrudGenerator\Commands\CrudRepositoryCommand'
        );
    }
}
