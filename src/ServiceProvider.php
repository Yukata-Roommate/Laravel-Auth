<?php

namespace YukataRm\Laravel\Auth;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

use YukataRm\Laravel\Auth\Commands\DeleteTokensCommand;
use YukataRm\Laravel\Auth\Commands\PublishStubsCommand;

use YukataRm\Laravel\Auth\Macros\RouterMacro;

/**
 * Auth Service Provider
 *
 * @package YukataRm\Laravel\Auth
 */
class ServiceProvider extends BaseServiceProvider
{
    /*----------------------------------------*
     * Boot
     *----------------------------------------*/

    /**
     * boot
     *
     * @return void
     */
    public function boot(): void
    {
        $this->bootCommands();
        $this->bootLangs();
        $this->bootMacros();
        $this->bootMigrations();
        $this->bootRoutes();
        $this->bootViews();
    }

    /**
     * boot commands
     *
     * @return void
     */
    protected function bootCommands(): void
    {
        if (!$this->app->runningInConsole()) return;

        $this->commands([
            DeleteTokensCommand::class,
            PublishStubsCommand::class,
        ]);
    }

    /**
     * boot langs
     *
     * @return void
     */
    protected function bootLangs(): void
    {
        $path = __DIR__ . "/../langs";

        $this->loadTranslationsFrom($path, "yr-auth");

        $this->publishes([
            $path => $this->app->langPath("vendor/yr-auth"),
        ]);
    }

    /**
     * boot macros
     *
     * @return void
     */
    protected function bootMacros(): void
    {
        $macro = new RouterMacro();

        $macroClass   = $macro->class();
        $macroMethods = $macro->methods();

        if (!class_exists($macroClass)) return;

        if (!method_exists($macroClass, "macro")) return;

        foreach ($macroMethods as $name => $closure) {
            $macroClass::macro($name, $closure);
        }
    }

    /**
     * boot migrations
     *
     * @return void
     */
    protected function bootMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . "/../migrations");
    }

    /**
     * boot routes
     *
     * @return void
     */
    protected function bootRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . "/../routes/console.php");
    }

    /**
     * boot views
     *
     * @return void
     */
    protected function bootViews(): void
    {
        $this->loadViewsFrom(__DIR__ . "/../resources/views", "yr-auth");
    }
}
