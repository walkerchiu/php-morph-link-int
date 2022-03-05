<?php

namespace WalkerChiu\MorphLink;

use Illuminate\Support\ServiceProvider;

class MorphLinkServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config files
        $this->publishes([
           __DIR__ .'/config/morph-link.php' => config_path('wk-morph-link.php'),
        ], 'config');

        // Publish migration files
        $from = __DIR__ .'/database/migrations/';
        $to   = database_path('migrations') .'/';
        $this->publishes([
            $from .'create_wk_morph_link_table.php'
                => $to .date('Y_m_d_His', time()) .'_create_wk_morph_link_table.php'
        ], 'migrations');

        $this->loadTranslationsFrom(__DIR__.'/translations', 'php-morph-link');
        $this->publishes([
            __DIR__.'/translations' => resource_path('lang/vendor/php-morph-link'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                config('wk-morph-link.command.cleaner')
            ]);
        }

        config('wk-core.class.morph-link.link')::observe(config('wk-core.class.morph-link.linkObserver'));
        config('wk-core.class.morph-link.linkLang')::observe(config('wk-core.class.morph-link.linkLangObserver'));
    }

    /**
     * Register the blade directives
     *
     * @return void
     */
    private function bladeDirectives()
    {
    }

    /**
     * Merges user's and package's configs.
     *
     * @return void
     */
    private function mergeConfig()
    {
        if (!config()->has('wk-morph-link')) {
            $this->mergeConfigFrom(
                __DIR__ .'/config/morph-link.php', 'wk-morph-link'
            );
        }

        $this->mergeConfigFrom(
            __DIR__ .'/config/morph-link.php', 'morph-link'
        );
    }

    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param String  $path
     * @param String  $key
     * @return void
     */
    protected function mergeConfigFrom($path, $key)
    {
        if (
            !(
                $this->app instanceof CachesConfiguration
                && $this->app->configurationIsCached()
            )
        ) {
            $config = $this->app->make('config');
            $content = $config->get($key, []);

            $config->set($key, array_merge(
                require $path, $content
            ));
        }
    }
}
