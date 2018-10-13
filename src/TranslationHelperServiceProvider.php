<?php

namespace JsonTranslationHelper;

use Illuminate\Support\ServiceProvider;

class TranslationHelperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \JsonTranslationHelper\Command\TranslationHelperCommand::class,
            ]);
        }

        $this->mergeConfigFrom(__DIR__ . '/config/translation-helper.php', 'translation-helper');

        $this->publishes([
            __DIR__ . '/config/translation-helper.php' => base_path('config/translation-helper.php'),
        ], 'config');
    }
}
