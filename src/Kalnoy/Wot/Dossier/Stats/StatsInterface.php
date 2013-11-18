<?php namespace Kalnoy\Wot\Dossier\Stats;

use Illuminate\Support\Contracts\ArrayableInterface;
use Illuminate\Support\Contracts\JsonableInterface;

interface StatsInterface extends ArrayableInterface, JsonableInterface {
    
    /**
     * Currently supported version.
     */
    const VERSION = 65;

    /**
     * Normalize stats values based on version.
     *
     * @return  StatsInterface
     */
    function normalize();

    /**
     * Merge stats.
     *
     * @param   StatsInterface  $other
     *
     * @return  StatsInterface
     */
    function merge(StatsInterface $other);

    /**
     * Get stats version.
     *
     * @return  int
     */
    function getVersion();

    /**
     * Get the number of merged elements.
     *
     * @return  int
     */
    function getElementCount();
}