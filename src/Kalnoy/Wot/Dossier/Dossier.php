<?php namespace Kalnoy\Wot\Dossier;

use Kalnoy\Wot\Dossier\Efficiency\FormulaInterface;
use Kalnoy\Wot\Dossier\Stats\StatsInterface;
use Kalnoy\Wot\Dossier\Stats\Battle as BattleStats;
use Kalnoy\Wot\Dossier\Stats\Total as TotalStats;

class Dossier extends BaseObject {

    const VERSION = 4;

    /**
     * Collection of tanks statistics.
     *
     * @var  Collection
     */
    protected $tanks;

    /**
     * The dossier version.
     *
     * @var  int
     */
    protected $version;

    /**
     * The time that this dossier was created/updated.
     *
     * @var  int
     */     
    public $created_at;

    /**
     * The player name.
     *
     * @var  string
     */
    public $player;

    /**
     * The address of the server that player has been playing on.
     *
     * @var  string
     */
    public $server;

    protected static $fields = array('version', 'server', 'created_at', 'player', 'tanks');

    public function __construct(array $tanks)
    {
        $this->tanks = new Collection($tanks);
        $this->created_at = time();
        $this->version = self::VERSION;
    }

    /**
     * Apply meta data from dossier filename.   
     *
     * @param   string  $path
     *
     * @return  Dossier
     */
    public function applyMeta($path)
    {
        list($this->server, $this->player) = self::meta($path);

        return $this;
    }

    /**
     * Get tanks collection.
     *
     * @return  Collection
     */
    public function getTanks()
    {
        return $this->tanks;
    }

    /**
     * Get the dossier version.
     *
     * @return  int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Check wheter this dossier is outdated.
     *
     * @return  boolean
     */
    public function isOutdated()
    {
        return self::VERSION > $this->version;
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
        $decoded = base32_decode(pathinfo($path, PATHINFO_FILENAME));

        if (!preg_match('/;[a-zA-Z0-9_]+$/', $decoded)) return array(null, null);

        return explode(';', $decoded);
    }
}