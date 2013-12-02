<?php namespace Kalnoy\Wot\Dossier\Stats;

use Kalnoy\Wot\Dossier\Collection;
use Kalnoy\Wot\Dossier\TankInfo;

class Tank extends Base {

    /**
     * The tank data.
     *
     * @var  Kalnoy\Wot\Dossier\TankInfo
     */
    protected $info;

    /**
     * Total battles tank played.
     *
     * @var  int
     */
    public $battles = 0;

    /**
     * Total number of battles played before 8.8.
     *
     * @since 29
     * @var  int
     */
    public $battles_old = 0;

    /**
     * The time when user created a tank.
     *
     * @var  int
     */
    public $created_at = 0;

    /**
     * The total duration of battles in minutes.
     *
     * @var int
     */
    public $battle_duration;

    /**
     * The last time when user updated a tank.
     *
     * @var  int
     */
    public $updated_at = 0;

    /**
     * The last time when user played.
     *
     * @var  int
     */
    public $last_played_at = 0;

    /**
     * The mileage.
     *
     * @since 29
     * @var  int
     */
    public $mileage = 0;

    /**
     * The total number of cut trees.
     *
     * @since 28
     * @var  int
     */
    public $trees_cut = 0;

    protected static $fields = array(
        'info',
        'created_at',
        'updated_at',
        'battle_duration',
        'last_played_at',
        'battles',
        'battles_old',
        'mileage',
        'trees_cut',
    );

    protected static $mergeFields = array(
        'battles',
        'battles_old',
        'battle_duration',
        'mileage',
        'trees_cut',
    );

    public function __construct($version, TankInfo $info)
    {
        parent::__construct($version);

        $this->info = $info;
    }

    public function calcAverageBattleDuration()
    {
        return $this->battles > 0 
            ? (float)$this->battle_duration / $this->battles 
            : null;
    }

    /**
     * Calc average mileage per battle.
     *
     * @since 29
     * @return  float
     */
    public function calcAverageMileage()
    {
        $battles = $this->battles - $this->battles_old;
        
        return $battles > 0 
            ? (float)$this->mileage / $battles 
            : null;
    }

    /**
     * Calculate average trees cut per battle.
     *
     * @since 28
     * @return  float
     */
    public function calcAverageTreesCut()
    {
        return $this->battles > 0 
            ? (float)$this->trees_cut / $this->battles 
            : null;
    }

    /**
     * Merge stats.
     *
     * @param   StatsInterface  $stats
     *
     * @return  StatsInterface
     */
    public function merge(StatsInterface $stats)
    {
        parent::merge($stats);

        $this->created_at = max($this->created_at, $stats->created_at);
        $this->updated_at = max($this->updated_at, $stats->updated_at);
        $this->last_played_at = max($this->last_played_at, $stats->last_played_at);

        return $this;
    }

    /**
     * Get tank.
     *
     * @return  Kalnoy\Wot\Dossier\Tank
     */
    public function getInfo()
    {
        return $this->info;
    }
}