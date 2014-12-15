<?php namespace Jlem\Context\Filters;

use Jlem\ArrayOk\ArrayOk;

class Condition
{
    protected $conditions;
    protected $configuration;

    public function __construct(array $initialConditions, array $initialConfiguration)
    {
        $this->when($initialConditions);
        $this->then($initialConfiguration);
    }

    /**
     * Overrides the intial conditions
     * 
     * @param  array  $conditions
     * @return Condition
     */
    
    public function when(array $conditions)
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
    
    public function andWhen($key, $value)
    {
        $this->conditions[$key] = $value;
        return $this;
    }


    /**
     * Overrides the intial configuration
     * 
     * @param  array  $configuration
     * @return Condition
     */
    
    public function then(array $configuration)
    {
        $this->configuration = $configuration;
        return $this;
    }


    /**
     * Append new data to the existing configuration
     * 
     * @param  array  $configuration
     * @return Condition
     */
    
    public function andThen(array $configuration)
    {
        $this->configuration += $configuration;
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


    /**
     * Returns the configuration data 
     * 
     * @return array
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }
}
