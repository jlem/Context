<?php namespace Jlem\Context\Filters;

use Jlem\ArrayOk\ArrayOk;

class ConditionFilter extends Filter
{
    protected $conditions;

    public function __construct(ArrayOk $Config)
    {
        parent::__construct($Config);
        $this->initializeConditions();
    }


    /**
     * Appends a new condition to the list of conditions 
     *
     * @param string $name
     * @param Condition $Condition
     * @access public
     * @return Condition
    */

    public function addCondition($name, Condition $Condition)
    {
        $this->conditions->append($Condition, $name);
        return $Condition;
    }
    

    /**
     * Returns the condition by name 
     *
     * @param string $name
     * @access public
     * @return mixed null|Condition
    */

    public function getCondition($name)
    {
        return $this->conditions[$name];
    }


    /**
     * Filters and returns the configuration data based on the given context 
     *
     * @access public
     * @return mixed ArrayOk|array
    */

    public function getData()
    {
        foreach ($this->conditions->reverse() as $condition) {
            if($condition->matchesContext($this->getContextSequence())) {
                return $condition->getConfiguration();
            }
        }

        return array();
    }


    /**
     * Conditions can be set in the configuration, so it's important to add these to the conditions array first, before appending new conditions to it, so as to maintain appropriate order 
     *
     * @access protected
     * @return void
    */

    protected function initializeConditions()
    {
        $this->conditions = new ArrayOk;

        if ($this->configIsValid('conditions')) {
            $this->conditions = $this->Config['conditions'];
        }
    }
}
