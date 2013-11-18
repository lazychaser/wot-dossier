<?php namespace Kalnoy\Wot\Dossier\Efficiency;

use Kalnoy\Wot\Dossier\Aggregated\Item;

interface FormulaInterface {

    const VERY_POOR = 0;
    const POOR = 1;
    const AVERAGE = 2;
    const GOOD = 3;
    const VERY_GOOD = 4;
    const EXCELENT = 5;

    /**
     * Calculate efficiency of specified type of specified tank.
     *
     * @param  Item $item
     *
     * @return int
     */
    function compute(Item $item);

    /**
     * Get the quality for the given rating.
     *
     * @param   int  $value
     *
     * @return  int
     */
    function getQuality($value);

    /**
     * Get formula identifier.
     *
     * @return  string
     */
    function getId();
}