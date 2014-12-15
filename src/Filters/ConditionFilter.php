<?php namespace Jlem\Context\Filters;

use Jlem\ArrayOk\ArrayOk;

class ConditionFilter extends Filter
{
    protected $Context;
    protected $conditions = [];

    public function __construct(ArrayOk $Config, ArrayOk $Context)
    {
        $this->addConditionsFromConfig($Config);
        $this->Context = $Context;
    }

    protected function addConditionsFromConfig(ArrayOk $Config)
    {
        $Config['conditions']->isEmpty() ?: $this->conditions = $Config['conditions'];
    }
    
    public function when(array $initialConditions)
    {
        return $this->conditions[] = new Condition($initialConditions, array());
    }

    public function getData()
    {
        $conditions = array_reverse($this->conditions);

        foreach ($conditions as $condition) {
            if($condition->matchesContext($this->Context)) {
                return $condition->getConfiguration();
            }
        }

        return array();
    }
}
