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
     * Alias of "andWhen()" for more semantic chaining 
     *
     * @param string $key
     * @param string $value
     * @access public
     * @return Condition
    */

    public function addCondition($key, $value)
    {
        return $this->andWhen($key, $value);
    }
    

    /**
     * Alias of "when()" for more semantic chaining 
     *
     * @param array $conditions
     * @access public
     * @return Condition
    */

    public function replaceConditions(array $conditions)
    {
        return $this->when($conditions);
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
     * Alias of "andThen()" for more semantic chaining 
     *
     * @param array $configuration
     * @access public
     * @return Condition
    */

    public function addConfiguration(array $configuration)
    {
        return $this->andThen($configuration);
    }
    

    /**
     * Alias of "then()" for more semantic chaining 
     *
     * @param array $configuration
     * @access public
     * @return Condition
    */

    public function replaceConfiguration(array $configuration)
    {
        return $this->then($configuration);
    }


    /**
     * Replaces both the conditions, and the configurations 
     *
     * @param array $conditions
     * @param array $configurations
     * @access public
     * @return Condition
    */

    public function replaceEverything(array $conditions, array $configurations)
    {
        $this->when($conditions);
        $this->then($configurations);
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
