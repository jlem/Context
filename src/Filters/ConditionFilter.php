<?php namespace Jlem\Context\Filters;

use Jlem\ArrayOk\ArrayOk;

class ConditionFilter extends Filter
{
    protected $Config;
    protected $Context;
    protected $conditions = [];

    public function __construct(ArrayOk $Config, ArrayOk $Context)
    {
        $this->Config = $Config;
        $this->Context = $Context;
    }
    
    public function when($initialKey, $initialValue)
    {
        return $this->conditions[] = new Condition($initialKey, $initialValue);
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
