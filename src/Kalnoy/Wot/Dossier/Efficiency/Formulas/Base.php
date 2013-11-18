<?php namespace Kalnoy\Wot\Dossier\Efficiency\Formulas;

use Kalnoy\Wot\Dossier\Stats\Battle;
use Kalnoy\Wot\Dossier\Aggregated\Item;
use Kalnoy\Wot\Dossier\Efficiency\FormulaInterface;

abstract class Base implements FormulaInterface {

    protected $terms = array();

    public function compute(Item $item)
    {
        $battle = $item->getBattle();

        return $battle->battles == 0 
            ? 0 
            : $this->computeForBattle($battle, $item->getIsTotals());
    }

    public abstract function computeForBattle(Battle $battle, $isTotals);

    public function getQuality($value)
    {
        return FormulaInterface::AVERAGE;
    }
}