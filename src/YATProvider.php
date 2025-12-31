<?php

namespace Beartropy\Tables;

use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;
use Beartropy\Tables\YATBaseTable;
use Illuminate\View\Compilers\BladeCompiler;
use Beartropy\Tables\Console\Commands\MakeComponent;

/**
 * Service Provider for Beartropy Tables.
 *
 * This provider registers the package's services, commands, views,
 * translations, and component aliases.
 * 
 * @package Beartropy\Tables
 */
class YATProvider extends ServiceProvider
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

    $this->loadViewsFrom(__DIR__ . '/resources/views', 'yat');

    $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'yat');

    if ($this->app->runningInConsole()) {
      // Export the migration
      if (! class_exists('create_yat_user_table_config')) {
        $this->publishes([
          __DIR__ . '/database/migrations/create_yat_user_table_config.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_yat_user_table_config.php'),
        ], 'migrations');
      }

      $this->publishes([
        __DIR__ . '/resources/lang' => $this->app->langPath('vendor/' . 'yat'),
      ], 'lang');
    }
  }

  /**
   * Register any application services.
   *
   * Registers console commands and defines the 'YATBaseTable' Livewire component.
   *
   * @return void
   */
  public function register()
  {

    $this->commands([
      MakeComponent::class,
    ]);


    $this->callAfterResolving(BladeCompiler::class, function () {
      Livewire::component('YATBaseTable', YATBaseTable::class);
    });
  }
}
