<?php namespace Kalnoy\Wot\Dossier\Stats;

/**
 * Represents statistics for specified battle type (random battles, clan, etc).
 *
 * There is also available several calculated properties.
 *
 * @property float $win_rate
 * @property float $survived_rate
 * @property float $hit_rate
 * @property int   $assisted_damage
 * @property float $average_damage
 * @property float $average_assisted_damage
 * @property float $average_capture_points
 * @property float $average_dropped_capture_points
 * @property float $average_xp
 * @property float $armor_efficiency Pierced received hits to received hits ratio
 * @property float $piercing_efficiency Pierced to hits ratio
 * @property int   $battles_v88 Battles made after v8.8
 * @property int   $xp_v88      Experience earned after v8.8
 */
class Battle extends Base {

    /**
     * Statistics type.
     *
     * @var  int
     */
    protected $type;

    /**
     * The tier.
     *
     * @var  float
     */
    public $tier = 0;

    /////////////
    // BATTLES //
    /////////////

    /**
     * Total battles.
     *
     * @var  int
     */
    public $battles = 0;

    /**
     * Total random battles played before a patch.
     *
     * @since 29 for random battles
     * @since 65 for clan and company battles
     * @var  int
     */
    public $battles_old = 0;

    /**
     * Total battles that player won.
     *
     * @var  int
     */
    public $wins = 0;

    /**
     * Total battles that player lost.
     *
     * @var  int
     */
    public $losses = 0;

    /**
     * Total battles that player survived.
     *
     * @var  int
     */
    public $survived = 0;

    /**
     * Total battles that player won and survived.
     *
     * @var  int
     */
    public $won_and_survived = 0;

    //////////////
    // SHOOTING //
    //////////////

    /**
     * Total shots made.
     *
     * @var  int
     */
    public $shots = 0;

    /**
     * Total hits.
     *
     * @var  int
     */
    public $hits = 0;

    /**
     * Number of high explosive hits.
     * 
     * @since 29
     * @var  int
     */
    public $he_hits = 0;

    /**
     * Total hits that penetrated the target.
     *
     * @since 29
     * @var  int
     */
    public $pierced_hits = 0;

    /**
     * Total recieved hits.
     *
     * @since 29
     * @var  int
     */
    public $received_hits = 0;

    /**
     * Number of received high explosive hits.
     *
     * @since 29
     * @var  integer
     */
    public $received_he_hits = 0;

    /**
     * Total recieved hits that penetrated player.
     *
     * @since 29
     * @var  int
     */
    public $received_pierced_hits = 0;

    ////////////
    // DAMAGE //
    ////////////

    /**
     * Total damage dealt by the player.
     *
     * @var  int
     */
    public $damage_dealt = 0;

    /**
     * Total damage that other players dealed when player spotted the target.
     *
     * @var  int
     */
    public $damage_assisted_track = 0;

    /**
     * Total damage that other players dealed while player tracked the target.
     *
     * @since 29
     * @var  int
     */
    public $damage_assisted_radio = 0;

    /**
     * Total recieved damage.
     *
     * @var  int
     */
    public $damage_received = 0;

    ////////////////
    // EXPERIENCE //
    ////////////////

    /**
     * Total experience. 
     *
     * @var  int
     */
    public $xp = 0;

    /**
     * Total experience earned before a patch.
     *
     * @since 29 for random battles
     * @since 65 for clan and company battles
     * @var  int
     */
    public $xp_old = 0;

    /**
     * Total "clean" experience (doesn't include premium, bonuses, etc).
     *
     * @since 29 for random battles
     * @since 65 for clan and company battles
     * @var  int
     */
    public $xp_clean = 0;

    ///////////
    // OTHER //
    ///////////
    
    /**
     * Total capture points.
     *
     * @var  int
     */
    public $capture_points = 0;

    /**
     * Total dropped capture points.
     *
     * @var  int
     */
    public $dropped_capture_points = 0;

    /**
     * Total spotted tanks.
     *
     * @var  int
     */
    public $spotted = 0;

    /**
     * Total frags.
     *
     * @var  int
     */
    public $frags = 0;

    /**
     * Random battles statistics.
     */
    const RANDOM = 0;

    /**
     * Clan battles statistics.
     *
     * @since 20
     */
    const CLAN = 1;

    /**
     * Company battles statistics.
     * 
     * @since 20
     */
    const COMPANY = 2;

    /**
     * 7/42 battles statistics.
     *
     * @since 65
     */
    const B7_42 = 3;

    /**
     * Aggregated battle type.
     */
    const AGGREGATED = 255;

    public static $types = array(
        self::RANDOM,
        self::B7_42,
        self::CLAN,
        self::COMPANY,
    );

    protected static $fields = array(
        'type', 
        'battles',
        'battles_old',
        'wins',
        'losses',
        'survived',
        'won_and_survived',
        'shots',
        'hits',
        'he_hits',
        'pierced_hits',
        'received_hits',
        'received_he_hits',
        'received_pierced_hits',
        'damage_dealt',
        'damage_received',
        'damage_assisted_radio',
        'damage_assisted_track',
        'capture_points',
        'dropped_capture_points',
        'spotted',
        'frags',
        'tier',
        'xp',
        'xp_old',
        'xp_clean',
    );

    protected static $mergeFields = array(
        'tier',
        'battles',
        'battles_old',
        'wins',
        'losses',
        'survived',
        'won_and_survived',
        'shots',
        'hits',
        'he_hits',
        'pierced_hits',
        'received_hits',
        'received_he_hits',
        'received_pierced_hits',
        'damage_dealt',
        'damage_received',
        'damage_assisted_track',
        'damage_assisted_radio',
        'capture_points',
        'dropped_capture_points',
        'spotted',
        'frags',
        'xp',
        'xp_old',
        'xp_clean',
    );

    /**
     * Inititalize the stats.
     *
     * @param  int  $version
     * @param  int  $type
     */
    public function __construct($version, $type = null, $tier = 0, $battles = 0)
    {
        parent::__construct($version);

        $this->type = $type;
        $this->tier = $tier * $battles;
        $this->battles = $battles;
    }

    public function calcAverageTier()
    {
        return $this->battles == 0 ? null : (float)$this->tier / $this->battles;
    }

    public function calcWinRate()
    {
        return (float)$this->wins / $this->battles;
    }

    public function calcSurvivedRate()
    {
        return (float)$this->survived / $this->battles;
    }

    public function calcHitRate()
    {
        return $this->shots == 0
            ? null
            : (float)$this->hits / $this->shots;
    }

    public function calcAssistedDamage()
    {
        return $this->damage_assisted_radio + $this->damage_assisted_track;
    }

    public function calcAverageDamage()
    {
        return (float)$this->damage_dealt / $this->battles;
    }

    public function calcAverageDamageRecieved()
    {
        return (float)$this->damage_received / $this->battles;
    }

    public function calcAverageAssistedDamage()
    {
        return (float)$this->assisted_damage / $this->battles;
    }

    public function calcAverageFrags()
    {
        return (float)$this->frags / $this->battles;
    }

    public function calcAverageSpotted()
    {
        return (float)$this->spotted / $this->battles;
    }

    public function calcAverageCapturePoints()
    {
        return (float)$this->capture_points / $this->battles;
    }

    public function calcAverageDroppedCapturePoints()
    {
        return (float)$this->dropped_capture_points / $this->battles;
    }

    public function calcAverageXp()
    {
        return (float)$this->xp / $this->battles;
    }

    public function calcPiercingEfficiency()
    {
        return $this->hits == 0
            ? null
            : (float)$this->pierced_hits / $this->hits;
    }

    public function calcArmorEfficiency()
    {
        return  $this->received_hits == 0
            ? null
            : 1 - (float)$this->received_pierced_hits / $this->received_hits;
    }

    public function calcBattlesNew()
    {
        return $this->battles - $this->battles_old;
    }

    public function calcXpNew()
    {
        return $this->xp - $this->xp_old;
    }

    /**
     * Make shure that statistics has data no matter what version it has.
     *
     * @return  Stats
     */
    public function normalize()
    {
        if ($this->version < 29 && $this->type == self::RANDOM ||
            $this->version < 65 && (
                $this->type == self::CLAN || $this->type == self::COMPANY
            ))
        {
            $this->battles_old = $this->battles;
            $this->xp_old      = $this->xp;
        }

        return $this;
    }

    /**
     * Merge this battle with other.
     *
     * @param   StatsInterface  $other
     *
     * @return  StatsInterface
     */
    public function merge(StatsInterface $other)
    {
        parent::merge($other);

        if ($this->type === null) $this->type = $other->type;

        return $this;
    }

    /**
     * Get the battle type.
     *
     * @return  int
     */
    public function getType()
    {
        return $this->type;
    }
}