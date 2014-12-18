<?php namespace Jlem\Context\Filters;

use Jlem\ArrayOk\ArrayOk;

class Condition
{
    protected $conditions = array();
    protected $configurations = array();

    public function __construct(array $initialConditions, array $initialConfigurations)
    {
        $this->setConditions($initialConditions);
        $this->setConfigurations($initialConfigurations);
    }


    /**
     * Overrides the intial conditions
     * 
     * @param  array  $conditions
     * @return Condition
     */
    
    public function setConditions(array $conditions)
    {
        $this->conditions = $conditions;
        return $this;
    }


    /**
     * Appends a new condition
     * 
     * @param  string $key
     * @param  string $value
     * @return Condition
     */
    
    public function addCondition($key, $value)
    {
        $this->conditions[$key] = $value;
        return $this;
    }


    /**
     * Returns the conditions array 
     *
     * @access public
     * @return array
    */

    public function getConditions()
    {
        return $this->conditions;
    }


    /**
     * Overrides the intial configuration
     * 
     * @param  array  $configuration
     * @return Condition
     */
    
    public function setConfigurations(array $configuration)
    {
        $this->configurations = $configuration;
        return $this;
    }


    /**
     * Append new data to the existing configuration
     * 
     * @param  array  $configuration
     * @return Condition
     */
    
    public function addConfiguration(array $configuration)
    {
        $this->configurations += $configuration;
        return $this;
    }


    /**
     * Returns the configuration data 
     * 
     * @return array
     */

    public function getConfiguration()
    {
        return $this->configurations;
    }


    /**
     * sets both the conditions, and the configurations 
     *
     * @param array $conditions
     * @param array $configurations
     * @access public
     * @return Condition
    */

    public function setEverything(array $conditions, array $configurations)
    {
        $this->setConditions($conditions);
        $this->setConfigurations($configurations);
        return $this;
    }


    /**
     * Checks to see if the given context matches any of the current conditions
     * 
     * @param  ArrayOk $Context
     * @return bool
     */
    
    public function matchesContext(ArrayOk $Context)
    {
        if (empty($this->conditions)) {
            return false;
        }

        foreach ($this->conditions as $key => $value) {

            // Bug out if the key isn't even set
            if (!$Context->exists($key)) {
                return false;
            }

            // Bug out if the key is set, but the value doesn't match
            if (!preg_match("#{$Context[$key]}#", $value)) {
                return false;
            }
        }

        return true;
    }
}
