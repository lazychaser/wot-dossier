<?php namespace Kalnoy\Wot\Dossier;

class Tanks extends Collection
{
    public function __construct(array $items = array())
    {
        parent::__construct($items, 'id');
    }

    /**
     * Construct collection from json file.
     *
     * @param   string  $filename
     *
     * @return  Tanks
     */
    public static function fromJson($filename)
    {
        $data = json_decode(file_get_contents($filename), true);

        $tanks = array();
        foreach ($data as $item) {
            if ($tank = TankInfo::fromJson($item)) $tanks[] = $tank;
        }

        return new static($tanks);
    }
}