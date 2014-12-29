<?php namespace Jlem\Context\Filters;

use Jlem\ArrayOk\ArrayOk;

abstract class Filter
{
    protected $contextOrder = null;
    protected $clipContext = true;
    protected $hasOverride = false;
    protected $Context;
    protected $Config;

    public function __construct($config)
    {
        $this->setConfig($config);
    }


    /**
     * Sets the configuration data for the filter
     *
     * @param mixed array|ArrayOk $config
     * @access public
     * @return void
    */

    public function setConfig($config)
    {
        $this->Config = $config instanceof ArrayOk ? $config : new ArrayOk($config);
    }



    /**
     * Defines a new context sequence for the filter to use when retrieving its data 
     *
     * @param mixed array|string $contextOrder
     * @access protected
     * @return void
    */

    public function reorderContext($newOrder, $clip = true)
    {
        $this->contextOrder = $newOrder;
        $this->clipContext = $clip;
        $this->hasOverride = true;
    }



    /**
     * Takes the sequence data, and re-orders / intersects it based on what was set in by() 
     *
     * @access public
     * @return ArrayOk
    */

    public function getContextSequence()
    {
        if (empty($this->contextOrder)) {
            return $this->Context; 
        }

        $ContextOverride = $this->Context->order($this->contextOrder, $this->clipContext);

        return $ContextOverride;
    }
    


    /**
     * Reverts context order back to original settings
     *
     * @access public
     * @return void
    */

    public function resetContextOrder()
    {
        $this->contextOrder = null;
        $this->clipContext = true;
        $this->hasOverride = false;
    }
    


    /**
     * Applies the context data to the filter 
     *
     * @param ArrayOk $Context
     * @access public
     * @return void
    */

    public function applyContext(ArrayOk $Context, $contextOrder, $clipContext)
    {
        $this->Context = clone $Context;

        if (!$this->hasOverride) {
            $this->contextOrder = $contextOrder;
            $this->clipContext = $clipContext;
        }

        if (empty($contextOrder)) {
            $this->resetContextOrder();
        }
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
