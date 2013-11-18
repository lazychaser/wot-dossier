<?php namespace Kalnoy\Wot\Dossier\Aggregated;

use Kalnoy\Wot\Dossier\Dossier as OriginalDossier;
use Kalnoy\Wot\Dossier\Collection;
use Kalnoy\Wot\Dossier\BaseObject;
use Kalnoy\Wot\Dossier\TankInfo;
use Kalnoy\Wot\Dossier\Stats\Tank;
use Kalnoy\Wot\Dossier\Stats\Battle;
use Kalnoy\Wot\Dossier\Stats\StatsInterface;
use Kalnoy\Wot\Dossier\Efficiency\FormulaInterface;

class Dossier extends BaseObject implements \IteratorAggregate {

    /**
     * The total statisitcs.
     *
     * @var  Item
     */
    protected $totals;

    /**
     * Collection of aggregated items.
     *
     * @var  Collection of Item
     */
    protected $tanks;

    /**
     * Original dossier
     *
     * @var  OriginalDossier
     */
    protected $original;

    protected static $fields = array('total', 'tanks');

    /**
     * Initialize aggregated dossier.
     *
     * @param  OriginalDossier  $original
     * @param  Item         $total
     * @param  array        $tanks
     */
    public function __construct(OriginalDossier $original, array $tanks)
    {
        $this->original = $original;
        $this->tanks = new Collection($tanks);
    }

    /**
     * Aggregate tank items to compute total.
     *
     * @return  Dossier
     */
    public function aggregateTotals()
    {
        $tankInfo = new TankInfo();
        $tankInfo->type = TankInfo::TOTALS;

        $tank = new Tank(StatsInterface::VERSION, $tankInfo);
        $battle = new Battle(StatsInterface::VERSION, Battle::AGGREGATED);
        $this->totals = new Item($battle, $tank);

        Battle::mergeAll($this->totals, $this->tanks->getItems());

        return $this;
    }

    /**
     * Compute efficiency for each tank and total.
     *
     * @param   FormulaInterface  $formula
     *
     * @return  Dossier
     */
    public function computeEfficiency(FormulaInterface $formula)
    {
        $items = $this->tanks->getItems();

        if ($this->totals) array_push($items, $this->totals);

        if (empty($items)) return $this;

        array_walk(
            $items, 
            function ($i, $k, $formula) { $i->computeEfficiency($formula); }, 
            $formula
        );

        return $this;
    }

    /**
     * Get aggregated tanks collection.
     *
     * @return  Collection of Item
     */
    public function getTanks()
    {
        return $this->tanks;
    }

    /**
     * Get total results.
     *
     * @return  Item
     */
    public function getTotals()
    {
        return $this->totals;
    }

    /**
     * Get the original dossier.
     *
     * @return  BaseDossier
     */
    public function getOriginal()
    {
        return $this->original;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->tanks->getItems());
    }
}