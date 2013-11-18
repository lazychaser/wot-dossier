<?php namespace Kalnoy\Wot\Dossier;

use Illuminate\Support\Contracts\ArrayableInterface;
use Illuminate\Support\Contracts\JsonableInterface;

class BaseObject implements ArrayableInterface, JsonableInterface {
    
    /**
     * The fields that will be exported to an array.
     *
     * @var  array
     */
    protected static $fields = array();

    public function __get($field)
    {
        $getter = 'get'.ucfirst($field);

        if (method_exists($this, $getter)) return $this->$getter();

        throw new AttributeException("Attribute {{$field}} not found.");
    }

    public function __set($field, $value)
    {
        $setter = 'set'.ucfirst($field);

        if (method_exists($this, $setter)) $this->$setter($value);

        throw new AttributeException("Attribute {{$field}} is not found.");
    }

    /**
     * Convert object to an array.
     *
     * @return  array
     */
    public function toArray()
    {
        $result = array();

        foreach (static::$fields as $field) 
        {
            $value = $this->$field;

            $result[$field] = $value instanceof ArrayableInterface 
                ? $value->toArray()
                : $value;
        }

        return $result;
    }

    /**
     * Encode the object into a JSON string.
     *
     * @param   int  $options
     *
     * @return  int
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }
}