<?php namespace Kalnoy\Wot\Dossier\Stats;

use Kalnoy\Wot\Dossier\TankInfo;
use Kalnoy\Wot\Dossier\Stats\Battle as BattleStats;
use Kalnoy\Wot\Dossier\Stats\StatsInterface;
use Kalnoy\Wot\Dossier\Collection;

class TankBattles extends Base {

    /**
     * Battles stats collection.
     *
     * @var  Collection
     */
    protected $battles;

    /**
     * The tank stats.
     *
     * @var  Tank
     */
    protected $tank;

    /**
     * The fields to be exported.
     *
     * @var  array
     */
    protected static $fields = array(
        'tank',
        'battles',
    );

    /**
     * Initialize tank stats.
     *
     * @param  int       $version
     * @param  TankInfo  $tankInfo
     * @param  array     $battleStats
     */
    public function __construct($version, Tank $tank, array $battles = array())
    {
        parent::__construct($version);

        if (empty($battles))
        {
            $battles[] = new BattleStats(
                StatsInterface::VERSION, 
                BattleStats::RANDOM, 
                $tank->getInfo()->tier
            );
        }

        $this->battles = new Collection($battles, 'type');
        $this->tank = $tank;
    }

    /**
     * Get battle stats collection.
     *
     * @return  Collection
     */
    public function getBattles()
    {
        return $this->battles;
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
}