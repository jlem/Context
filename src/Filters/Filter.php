<?php namespace Jlem\Context\Filters;

use Jlem\ArrayOk\ArrayOk;

abstract class Filter
{
    protected $givenSequence;
    protected $Context;
    protected $Config;

    public function __construct(ArrayOk $Config)
    {
        $this->Config = $Config;
    }


    /**
     * Defines a new context sequence for the filter to use when retrieving its data 
     *
     * @param mixed array|string $givenSequence
     * @access protected
     * @return void
    */

    public function changeContextSequence($givenSequence)
    {
        $this->givenSequence = $this->normalizeSequence($givenSequence);
    }


    /**
     * Takes the sequence data, and re-orders / intersects it based on what was set in by() 
     *
     * @access public
     * @return ArrayOk
    */

    public function getContextSequence()
    {
        if (empty($this->givenSequence)) {
            return $this->Context; 
        }

        return new ArrayOk($this->Context->orderByAndGetIntersection($this->givenSequence));
    }
    

    /**
     * Applies the context data to the filter 
     *
     * @param ArrayOk $Context
     * @access public
     * @return void
    */

    public function applyContext(ArrayOk $Context)
    {
        $this->Context = $Context;
    }


    /**
     * Converts a dot separated string to an array, or passes an array through 
     *
     * @param mixed array|string $sequence
     * @access protected
     * @return array
    */

    protected function normalizeSequence($sequence) 
    {
        return is_array($sequence) ? $sequence : explode('.', $sequence);
    }


    /**
     * Checks to make sure the requested config item is a valid ArrayOk object 
     *
     * @param string $item
     * @access protected
     * @return bool
    */

    protected function configIsValid($item)
    {
        return ($this->Config->itemIsAOk($item));
    }


    /**
     * Filters and returns the configuration data based on the given context 
     *
     * @abstract
     * @access public
     * @return ArrayOk
    */

    abstract public function getData();
}
