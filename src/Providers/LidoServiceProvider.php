<?php
/*
 * This file is part of Laravel Lido.
 *
 * (c) Goran Krgovic <gorankrgovic1@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);


namespace Gox\Laravel\Lido\Providers;

use Gox\Contracts\Lido\Listen\Models\Listen as ListenContract;
use Gox\Contracts\Lido\ListenCounter\Models\ListenCounter as ListenCounterContract;
use Gox\Contracts\Lido\Listenable\Services\ListenableService as ListenableServiceContract;
use Gox\Laravel\Lido\Listen\Models\Listen;
use Gox\Laravel\Lido\Listen\Observers\ListenObserver;
use Gox\Laravel\Lido\Listenable\Services\ListenableService;
use Gox\Contracts\Lido\ListenCounter\Models\ListenCounter;
use Illuminate\Support\ServiceProvider;


class LidoServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerObservers();
        $this->registerPublishes();
        $this->registerMigrations();
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerContracts();
    }

    /**
     * Register models observers.
     *
     * @return void
     */
    protected function registerObservers()
    {
        $this->app->make(ListenContract::class)->observe(ListenObserver::class);
    }


    /**
     * Register classes in the container.
     *
     * @return void
     */
    protected function registerContracts()
    {
        $this->app->bind(ListenContract::class, Listen::class);
        $this->app->bind(ListenCounterContract::class, ListenCounter::class);
        $this->app->singleton(ListenableServiceContract::class, ListenableService::class);
    }

    /**
     * Setup the resource publishing groups.
     *
     * @return void
     */
    protected function registerPublishes()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../../database/migrations' => database_path('migrations'),
            ], 'migrations');
        }
    }

    /**
     * Register the migrations.
     *
     * @return void
     */
    protected function registerMigrations()
    {
        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        }
    }
}