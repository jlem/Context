<?php namespace Jlem\Context\Filters;

use Jlem\ArrayOk\ArrayOk;

class ConditionFilter extends Filter
{
    protected $Context;
    protected $Config;
    protected $conditions;

    public function __construct(ArrayOk $Config, ArrayOk $Context)
    {
        $this->Context = $Context;
        $this->Config = $Config;
        $this->conditions = new ArrayOk;
        $this->initializeConditions();
    }

    protected function initializeConditions()
    {
        if ($this->configIsValid('conditions')) {
            $this->conditions = $this->Config['conditions'];
        }
    }
    
    public function when($name, array $initialConditions)
    {
        return $this->conditions->append(new Condition($initialConditions, array()), $name);
    }

    public function update($name)
    {
        return $this->conditions[$name];
    }

    public function getData()
    {
        foreach ($this->conditions->reverse() as $condition) {
            if($condition->matchesContext($this->getContextSequence())) {
                return $condition->getConfiguration();
            }
        }

        return array();
    }
}
