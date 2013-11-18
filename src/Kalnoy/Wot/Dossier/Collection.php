<?php namespace Kalnoy\Wot\Dossier;

use Illuminate\Support\Contracts\ArrayableInterface;

class Collection implements ArrayableInterface, \IteratorAggregate {
    
    /**
     * The data.
     *
     * @var  array
     */
    protected $items = array();

    /**
     * The key that is used for mapping items.
     *
     * @var  string
     */
    protected $key;

    public function __construct(array $items = array(), $key = null)
    {
        $this->key = $key;

        if (!empty($items))
        {
            foreach ($items as $item) $this->add($item);
        }
    }

    /**
     * Get all items.
     *
     * @return  array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Add item.
     *
     * @param  ArrayableInterface  $value
     */
    public function add(ArrayableInterface $value)
    {
        if ($this->key !== null)
        {
            $this->items[$value->{$this->key}] = $value;
        }
        else
        {
            $this->items[] = $value;
        }
    }

    /**
     * Get item by key.
     *
     * @param   mixed  $key
     *
     * @return  ArrayableInterface
     */
    public function get($key)
    {
        return $this->has($key) ? $this->items[$key] : null;
    }

    /**
     * Get whether key is present.
     *
     * @param   mixed   $key
     *
     * @return  boolean
     */
    public function has($key)
    {
        return array_key_exists($key, $this->items);
    }

    public function sort(\Closure $callback)
    {
        if ($this->key !== null)
        {
            uasort($this->items, $callback);
        }
        else
        {
            usort($this->items, $callback);
        }
    }

    public function toArray()
    {
        return array_map(function ($i) { return $i->toArray(); }, $this->items);
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }
}