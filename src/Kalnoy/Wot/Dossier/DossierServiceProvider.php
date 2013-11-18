<?php namespace Kalnoy\Wot\Dossier;

use Illuminate\Support\ServiceProvider;
use Kalnoy\Wot\Dossier\Converters\PythonConverter;

class DossierServiceProvider extends ServiceProvider {
    
    /**
     * This provider is loaded on demand.
     *
     * @var  boolean
     */
    protected $defer = true;

    /**
     * Register services.
     *
     * @return  void
     */
    public function register()
    {
        $this->app['wot.tanks'] = $this->app->share(function ($app) {
            $path = $app['config']->get('wot-dossier::converters.python.root');

            return Tanks::fromJson(base_path($path.'/tanks.json'));
        });

        $this->app['wot.dossier'] = $this->app->share(function ($app) {
            $path = $app['config']->get('wot-dossier::converters.python.root');

            $converter = new PythonConverter(base_path($path.'/wotdc2j.py'), $app['wot.tanks']);

            return new Environment($converter);
        });
    }

    /**
     * Boot the service provider.
     *
     * @return  void
     */
    public function boot()
    {
        $this->package('kalnoy/wot-dossier', 'wot-dossier', realpath(__DIR__.'/../../..'));
    }

    /**
     * Get the services provided by service provider.
     *
     * @return  array
     */
    public function provides()
    {
        return array('wot.dossier', 'wot.tanks');
    }
}