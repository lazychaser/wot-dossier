<?php namespace Kalnoy\Wot\Dossier\Stats;

use Kalnoy\Wot\Dossier\BaseObject;

abstract class Base extends BaseObject implements StatsInterface {

    /**
     * The version.
     *
     * @var  int
     */
    protected $version;

    /**
     * The number of elements that this statistics is based on.
     *
     * @var  int
     */
    protected $elements = 0;

    /**
     * Calculated values cache.
     *
     * @var  array
     */
    protected $calculated = array();

    /**
     * The fields that will be merged.
     *
     * @var  array
     */
    protected static $mergeFields = array();

    /**
     * Override default getter to support calculated properties.
     *
     * @param   string  $field
     *
     * @return  mixed
     */
    public function __get($field)
    {
        if (array_key_exists($field, $this->calculated))
        {
            return $this->calculated[$field];
        }

        $method = 'calc'.studly_case($field);

        if (method_exists($this, $method))
        {
            return $this->calculated[$field] = $this->$method();
        }

        return parent::__get($field);
    }

    /**
     * Initialize stats.
     *
     * @param  int  $version
     */
    public function __construct($version)
    {
        $this->version = $version;
    }

    /**
     * Reset calculated properties cache.
     *
     * @return  void
     */
    public function reset()
    {
        $this->calculated = array();

        return $this;
    }

    /**
     * Make shure that data is valid for every version.
     *
     * @return  StatsInterface
     */
    public function normalize()
    {
        return $this;
    }

    /**
     * Merge current statistics with other.
     *
     * @param   Stats   $other
     *
     * @return  Stats
     */
    public function merge(StatsInterface $other)
    {
        if (!$other instanceof static)
        {
            throw new \Exception("Only statistics of the same class can be merged.");
        }

        $this->version = min($this->version, $other->version);
        $this->elements += $other->getElementCount();

        foreach (static::$mergeFields as $field)
        {
            $this->$field += $other->$field;
        }

        return $this->reset();
    }

    /**
     * Merge $item with all $items.
     *
     * @param   StatsInterface  $item
     * @param   array           $items
     *
     * @return  StatsInterface
     */
    public static function mergeAll(StatsInterface $item, array $items)
    {
        return array_reduce($items, function ($a, $b) {
            if ($b !== null) $a->merge($b);
            
            return $a;

        }, $item);
    }

    /**
     * Get the number of elements that were merged.
     *
     * @return  int
     */
    public function getElementCount()
    {
        return $this->elements === 0 ? 1 : $this->elements;
    }

    /**
     * Get the version.
     *
     * @return  int
     */
    public function getVersion()
    {
        return $this->version;
    }
}