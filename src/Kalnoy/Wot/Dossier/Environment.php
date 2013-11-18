<?php namespace Kalnoy\Wot\Dossier;

use Kalnoy\Wot\Dossier\Converters\ConverterInterface;

class Environment {

    /**
     * The converter used to convert dossiers.
     *
     * @var  ConverterInterface
     */
    protected $converter;

    /**
     * Initialize the environment.
     *
     * @param  ConverterInterface  $converter
     */ 
    public function __construct(ConverterInterface $converter)
    {
        $this->converter = $converter;
    }

    /**
     * Convert dossier cache file.
     *
     * @param   string  $path
     * @param   string  $original The original dossier filename to extract
     *                            server and player names.
     *
     * @return  Dossier
     */
    public function convert($path, $original = null)
    {
        $dossier = $this->converter->convert($path);

        if ($original !== null) $dossier->applyMeta($original);

        return $dossier;
    }
}