<?php

namespace CoderFleek\SSO;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use CoderFleek\SSO\Middleware\RefreshSsoToken;
use CoderFleek\SSO\Contracts\SsoClient;

// Import Laravel helper functions
use function app_path;
use function base_path;
use function config_path;
use function database_path;

/**
 * CoderFleek SSO Service Provider
 * 
 * This service provider bootstraps the SSO client package by:
 * - Registering the configuration
 * - Setting up the SSO client singleton
 * - Publishing package assets
 * - Registering routes and middleware
 * 
 * @package CoderFleek\SSO
 */
class SsoServiceProvider extends ServiceProvider
{
    /**
     * Register SSO services and bindings
     * 
     * This method:
     * - Merges the package configuration
     * - Binds the SsoClient interface to SsoClientManager implementation
     *
     * @return void
     */    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config/cf-sso.php', 'cf-sso');

        $this->app->singleton(SsoClient::class, function ($app) {
            return new SsoClientManager($app['config']['cf-sso']);
        });
    }

    /**
     * Bootstrap SSO package services
     * 
     * This method:
     * - Publishes configuration files
     * - Publishes database migrations
     * - Publishes controllers and middleware
     * - Loads migrations
     * - Registers SSO routes
     * - Registers SSO middleware
     *
     * @return void
     */
    public function boot()
    {
        if (!$this->app->runningInConsole()) {
            return;
        }        // Publish configuration
        $this->publishes([
            __DIR__ . '/config/cf-sso.php' => config_path('cf-sso.php'),
        ], 'cf-sso-config');        // Publish migrations
        $this->publishes([
            __DIR__ . '/migrations/' => database_path('migrations'),
        ], 'cf-sso-migrations');

        // Publish controllers and middleware
        $this->publishes([
            __DIR__ . '/Http/Controllers/SsoController.php' => app_path('Http/Controllers/SsoController.php'),
            __DIR__ . '/Middleware/RefreshSsoToken.php' => app_path('Http/Middleware/RefreshSsoToken.php'),
        ], 'cf-sso-files');        // Publish all assets for fresh installation
        $this->publishes([
            __DIR__ . '/config/cf-sso.php' => config_path('cf-sso.php'),
            __DIR__ . '/migrations/' => database_path('migrations'),
            __DIR__ . '/Http/Controllers/SsoController.php' => app_path('Http/Controllers/SsoController.php'),
            __DIR__ . '/Middleware/RefreshSsoToken.php' => app_path('Http/Middleware/RefreshSsoToken.php'),
        ], 'cf-sso-install');        // Load migrations automatically
        $this->loadMigrationsFrom(__DIR__ . '/migrations');

        $prefix = Config::get('cf-sso.prefix', 'cf');
        
        // Register SSO authentication routes
        Route::prefix($prefix . '/auth')
            ->namespace('CoderFleek\\SSO\\Http\\Controllers')
            ->group(function ($router) {
                $router->get('login', 'SsoController@login')->name('sso.login');
                $router->get('callback', 'SsoController@callback')->name('sso.callback');
                $router->post('logout', 'SsoController@logout')->name('sso.logout');
            });

        // Register SSO middleware alias
        Route::aliasMiddleware('sso.auth', RefreshSsoToken::class);
    }
}
