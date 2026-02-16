<?php

namespace Beartropy\Tables;

use Beartropy\Tables\Console\Commands\MakeComponent;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\Compilers\BladeCompiler;
use Livewire\Livewire;

/**
 * Service Provider for Beartropy Tables.
 *
 * This provider registers the package's services, commands, views,
 * translations, and component aliases.
 */
class BeartropyTableServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * This method loads views, translations, and publishes assets
     * (migrations and language files) when running in the console.
     *
     * @return void
     */
    public function boot()
    {

        $this->loadViewsFrom(__DIR__.'/resources/views', 'yat');

        $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'yat');

        if ($this->app->runningInConsole()) {
            // Export the migration
            if (! class_exists('create_yat_user_table_config')) {
                $this->publishes([
                    __DIR__.'/database/migrations/create_yat_user_table_config.stub' => database_path('migrations/'.date('Y_m_d_His', time()).'_create_yat_user_table_config.php'),
                ], 'migrations');
            }

            $this->publishes([
                __DIR__.'/resources/lang' => $this->app->langPath('vendor/'.'yat'),
            ], 'lang');
        }

        if (class_exists(\Laravel\Boost\BoostServiceProvider::class)) {
            $this->registerBoostTools();
        }
    }

    /**
     * Register MCP tools with Laravel Boost when available.
     */
    protected function registerBoostTools(): void
    {
        $include = config('boost.mcp.tools.include', []);
        $include[] = \Beartropy\Tables\Mcp\Tools\ComponentDocs::class;
        $include[] = \Beartropy\Tables\Mcp\Tools\ListComponents::class;
        $include[] = \Beartropy\Tables\Mcp\Tools\ProjectContext::class;
        config(['boost.mcp.tools.include' => $include]);
    }

    /**
     * Register any application services.
     *
     * Registers console commands and defines the Livewire components.
     *
     * @return void
     */
    public function register()
    {

        $this->commands([
            MakeComponent::class,
        ]);

        // Backward compatibility: alias the old class name
        if (! class_exists(YATBaseTable::class, false)) {
            class_alias(BeartropyTable::class, YATBaseTable::class);
        }

        $this->callAfterResolving(BladeCompiler::class, function () {
            Livewire::component('BeartropyTable', BeartropyTable::class);
            Livewire::component('YATBaseTable', BeartropyTable::class);
        });
    }
}
