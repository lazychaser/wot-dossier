<?php namespace Kalnoy\Wot\Dossier;

class TankInfo extends BaseObject {

    /**
     * The contry id.
     *
     * @var  int
     */
    public $country_id;

    /**
     * The tank id.
     *
     * @var  int
     */
    public $tank_id;

    /**
     * The tank type.
     *
     * @var  int
     */
    public $type;

    /**
     * The tank tier.
     *
     * @var  int
     */ 
    public $tier;

    /**
     * The tank title.
     *
     * @var  string
     */
    public $title;

    /**
     * The short tank title.
     *
     * @var  string
     */
    public $short_title;

    /**
     * Whether tank is premium.
     *
     * @var  bool
     */
    public $is_premium;

    /**
     * The icon name for the tank.
     *
     * @var  string
     */
    public $icon;

    /**
     * Fields to export.
     *
     * @var  array
     */
    protected static $fields = array(
        'id', 
        'country_id', 
        'tank_id', 
        'type', 
        'tier', 
        'title', 
        'short_title', 
        'icon',
    );

    /**
     * Lite tank
     */
    const LITE = 1;

    /**
     * Medium tank
     */
    const MEDIUM = 2;

    /**
     * Heavy tank
     */
    const HEAVY = 3;

    /**
     * Tank destroyer
     */
    const TANK_DESTROYER = 4;

    /**
     * Artillery
     */
    const ARTILLERY = 5;

    /**
     * Indicates that this is the info for total results.
     */
    const TOTALS = 255;

    /**
     * Ussr
     */
    const USSR = 0;

    /**
     * Germany
     */
    const GERMANY = 1;

    /**
     * Usa
     */
    const USA = 2;

    /**
     * China
     */
    const CHINA = 3;

    /**
     * France
     */
    const FRANCE = 4;

    /**
     * Britain
     */
    const BRITAIN = 5;

    /**
     * Japan
     */
    const JAPAN = 6;

    /**
     * Get tank unique id.
     *
     * @return  string
     */
    public function getId()
    {
        return "{$this->country_id}_{$this->tank_id}";
    }

    /**
     * Get tank type string.
     *
     * @return  string
     */
    public function getTypeString()
    {
        switch ($this->type)
        {
            case self::LITE: return 'lite';
            case self::MEDIUM: return 'medium';
            case self::HEAVY: return 'heavy';
            case self::TANK_DESTROYER: return 'tank_destroyer';
            case self::ARTILLERY: return 'artillery';
        }

        return 'unknown';
    }

    /**
     * Get contry string.
     *
     * @return  string
     */
    public function getCountryString()
    {
        switch ($this->country_id)
        {
            case self::USSR: return 'ussr';
            case self::GERMANY: return 'germany';
            case self::USA: return 'usa';
            case self::CHINA: return 'china';
            case self::FRANCE: return 'france';
            case self::BRITAIN: return 'britain';
            case self::JAPAN: return 'japan';
        }

        return 'unknown';
    }

    /**
     * Get whether this tank info is for account totals.
     *
     * @return  bool
     */
    public function getIsForTotals()
    {
        return $this->type === self::TOTALS;
    }

    /**
     * Create tank from data exported from json.
     *
     * @param   array  $data
     *
     * @return  Tank
     */
    public static function fromJson(array $data)
    {
        $tank = new static;

        $tank->country_id = $data['countryid'];
        $tank->tank_id = $data['tankid'];
        $tank->type = $data['type'];
        $tank->tier = $data['tier'];
        $tank->title = $data['title'];
        $tank->is_premium = (bool)$data['premium'];
        $tank->icon = $data['icon'];

        if (isset($data['title_short']))
        {
            $tank->short_title = $data['title_short'];
        }

        return $tank;
    }
}