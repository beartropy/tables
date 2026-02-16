<?php

namespace Tests;

use Beartropy\Tables\YATProvider;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Cache::flush();
        \Beartropy\Tables\Classes\Columns\Column::resetStaticKeys();
    }

    protected function tearDown(): void
    {
        \Illuminate\Support\Facades\Schema::dropIfExists('users');
        parent::tearDown();
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            \BladeUI\Icons\BladeIconsServiceProvider::class,
            \BladeUI\Heroicons\BladeHeroiconsServiceProvider::class,
            \Beartropy\Ui\BeartropyUiServiceProvider::class,
            YATProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        $app['config']->set('view.paths', [
            __DIR__.'/../resources/views',
            resource_path('views'),
        ]);

        $app['config']->set('app.key', 'base64:Hupx3yAySikrM2/edkZQNQHslgDWYfiBfCuSThJ5SK8=');

        // Schema setup
        \Illuminate\Support\Facades\Schema::create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->timestamps();
        });
    }
}
