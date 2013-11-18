<?php namespace Kalnoy\Wot\Dossier\Facades;

use Illuminate\Support\Facades\Facade;

class Dossier extends Facade {

    /**
     * Get tank info collection.
     *
     * @return  \Kalnoy\Wot\Dossier\Collection of \Kalnoy\Wot\Dossier\TankInfo
     */
    public static function tanks()
    {
        return \App::make('wot.tanks');
    }

    /**
     * Get tank info.
     *
     * @param   string  $id
     *
     * @return  \Kalnoy\Wot\Dossier\TankInfo
     */
    public static function tank($id)
    {
        return \App::make('wot.tanks')->get($id);
    }

    /**
     * Extract meta info from dossier filename.
     *
     * @param   string  $path
     *
     * @return  array [server, player]
     */
    public static function meta($path)
    {
        return \Kalnoy\Wot\Dossier\Dossier::meta($path);
    }

    /**
     * Get the registered name of the components.
     *
     * @return  string
     */
    protected static function getFacadeAccessor()
    {
        return 'wot.dossier';
    }
}