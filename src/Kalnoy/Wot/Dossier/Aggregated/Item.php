<?php namespace Kalnoy\Wot\Dossier\Aggregated;

use Kalnoy\Wot\Dossier\Stats\Base as BaseObject;
use Kalnoy\Wot\Dossier\Stats\Battle;
use Kalnoy\Wot\Dossier\Stats\Tank;
use Kalnoy\Wot\Dossier\Stats\StatsInterface;
use Kalnoy\Wot\Dossier\Efficiency\FormulaInterface;

class Item extends BaseObject {

    /**
     * Battle stats.
     *
     * @var  Battle
     */
    protected $battle;

    /**
     * Tank stats.
     *
     * @var  Tank
     */
    protected $tank;

    /**
     * Calculated efficiency.
     *
     * @var  int
     */
    public $efficiency;

    protected static $fields = array('tank', 'battle', 'efficiency');

    /**
     * Initialize item.
     *
     * @param  Battle  $battle
     * @param  Tank    $tank
     */
    public function __construct(Battle $battle, Tank $tank)
    {
        $this->battle = $battle;
        $this->tank = $tank;
    }

    /**
     * Compute efficiency using specified formula.
     *
     * @param   FormulaInterface  $formula
     *
     * @return  Item
     */
    public function computeEfficiency(FormulaInterface $formula)
    {
        $this->efficiency = $formula->compute($this);

        return $this;
    }

    /**
     * Merge this item with other.
     *
     * @param   StatsInterface  $other
     *
     * @return  StatsInterface
     */
    public function merge(StatsInterface $other)
    {
        parent::merge($other);

        $this->battle->merge($other->battle);
        $this->tank->merge($other->tank);

        $this->version = $this->battle->getVersion();

        return $this;
    }

    /**
     * Get battle stats.
     *
     * @return  Battle
     */
    public function getBattle()
    {
        return $this->battle;
    }

    /**
     * Get tank stats.
     *
     * @return  Tank
     */
    public function getTank()
    {
        return $this->tank;
    }

    /**
     * Get whether this is totals.
     *
     * @return  bool
     */
    public function getIsTotals()
    {
        return $this->tank->getInfo()->getIsForTotals();
    }
}