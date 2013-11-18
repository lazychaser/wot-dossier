<?php namespace Kalnoy\Wot\Dossier\Converters;

interface ConverterInterface {

    /**
     * Convert dossier file into common format.
     *
     * @param   string  $filename
     *
     * @return  array
     */
    function convert($filename);
}