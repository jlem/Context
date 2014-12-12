<?php namespace Jlem\Context;

use Jlem\Context\Filters\Filter;
use Jlem\ArrayOk\ArrayOk;

class Context
{
    protected $filters = [];


    public function addFilter($name, Filter $Filter)
    {
        $this->filters[$name] = $Filter;
    }



    public function filter($name)
    {
        return $this->filters[$name];
    }


    
    /**
     * After running load(), you can retrieve the value for a specific config key, or get all config values if no key is supplied
     *
     * @param mixed string|null $key
     * @access public
     * @return mixed
    */

	public function get($key = null, $forceMerge = false)
	{
        if ($forceMerge || empty($this->merged)) {
            $this->merge();
        }

        if (!$key) {
            return $this->merged;
        }

		return $this->merged[$key];
	}



    /**
     * Changes the context values to be processed when merging 
     *
     * @param ContextSet $Context
     * @access public
     * @return void
    */

    public function changeContext(ContextSet $ContextSet)
    {
        $this->Merger->changeContext($ContextSet);
    }


    protected function merge()
    {
        $toMerge = [];

        foreach ($this->filters as $filter) {
            $toMerge[] = $filter->getData();
        }

        return $this->merged = new ArrayOk(call_user_func_array('array_merge', $toMerge));
    }
}
