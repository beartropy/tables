<?php

namespace Beartropy\Tables;

use Livewire\Livewire;
use Illuminate\Support\ServiceProvider;
use Beartropy\Tables\YATBaseTable;
use Illuminate\View\Compilers\BladeCompiler;
use Beartropy\Tables\Console\Commands\MakeComponent;

class YATProvider extends ServiceProvider
{

    public function boot()
    {

      $this->loadViewsFrom(__DIR__.'/resources/views', 'YATPackage');

      $this->loadTranslationsFrom(__DIR__.'/resources/lang', 'yat');

      if ($this->app->runningInConsole()) {
        // Export the migration
        if (! class_exists('create_yat_user_table_config')) {
          $this->publishes([
            __DIR__ . '/database/migrations/create_yat_user_table_config.stub' => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_yat_user_table_config.php'),
          ], 'migrations');
        }

        $this->publishes([
          __DIR__.'/resources/lang' => $this->app->langPath('vendor/'.'yat'),
        ], 'lang');

      }

      

    }

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