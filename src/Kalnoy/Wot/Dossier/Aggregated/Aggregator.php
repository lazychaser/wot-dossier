<?php namespace Kalnoy\Wot\Dossier\Aggregated;

use Kalnoy\Wot\Dossier\BaseObject;
use Kalnoy\Wot\Dossier\Dossier as BaseDossier;
use Kalnoy\Wot\Dossier\TankInfo;
use Kalnoy\Wot\Dossier\Collection;
use Kalnoy\Wot\Dossier\Stats\Battle;
use Kalnoy\Wot\Dossier\Stats\TankBattles;
use Kalnoy\Wot\Dossier\Stats\Tank;
use Kalnoy\Wot\Dossier\Stats\StatsInterface;

/**
 * This class is made for aggregating multiple tank statistics into a single one.
 *
 * $statTypes = array(Battle::RANDOM, Battle::B7_42);
 * $aggregated = $aggregator->aggregate($dossier, $statTypes);
 *
 * You can then aggregate totals and compute efficiency.
 *
 * $aggregated->aggregateTotals()->computeEfficiency($formula);
 *
 * echo $aggregated->getTotals()->efficiency;
 */
class Aggregator extends BaseObject {

    /**
     * Aggregate given dossier to have only selected stat types.
     *
     * @param   Dossier  $dossier
     * @param   array    $statTypes
     *
     * @return  Dossier
     */
    public function aggregate(BaseDossier $dossier, array $statTypes)
    {
        $items = $this->aggregateTanks($dossier->getTanks(), $statTypes);

        return new Dossier($dossier, $items);
    }

    /**
     * Aggregate tanks.
     *
     * @param   Collection  $tanks
     * @param   array       $statTypes
     *
     * @return  array of Item
     */
    protected function aggregateTanks(Collection $tanks, array $statTypes)
    {
        $result = array();

        foreach ($tanks as $tank)
        {
            $result[] = $this->aggregateTank($tank, $statTypes);
        }

        return $result;
    }

    /**
     * Aggregate stats for given tank.
     *
     * @param   TankBattles  $tankBattles
     * @param   array       $statTypes
     *
     * @return  Item
     */
    protected function aggregateTank(TankBattles $tankBattles, array $statTypes)
    {
        $itemsToMerge = array_map(function ($item) use ($tankBattles) {

            return $tankBattles->getBattles()->get($item);

        }, $statTypes);

        $merged = new Battle(StatsInterface::VERSION);

        Battle::mergeAll($merged, $itemsToMerge);

        return new Item($merged, $tankBattles->getTank());
    }
}