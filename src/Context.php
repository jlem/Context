<?php namespace Jlem\Context;

use Jlem\Context\Filters\Filter;
use Jlem\ArrayOk\ArrayOk;

class Context
{
    protected $filters = [];

    /**
     * Adds a new filter to the filter stack
     * 
     * @param string $name
     * @param Filter $Filter
     * @return  void
     */
    
    public function addFilter($name, Filter $Filter)
    {
        $this->filters[$name] = $Filter;
    }


    /**
     * Returns a filter by name
     * 
     * @param  string $name
     * @return Filter
     */
    
    public function filter($name)
    {
        return $this->filters[$name];
    }


    /**
     * Gets all configs, or a specific config by key. 
     * The first time this runs, it gets cashed so it doesn't have to re-merge all filters on each call.
     * To force a re-merge in the event you add a new filter to the stack or modify an existing filter, pass in "true" as the second parameter.
     *
     * @param mixed string|null $key
     * @access public
     * @return mixed
    */

	public function get($key = null, $forceMerge = false)
	{
        if ($forceMerge || empty($this->merged)) {
            $this->mergeFilters();
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


    /**
     * Runs array_merge on the filter stack, in the order the filters were added
     * 
     * @return ArrayOk
     */
    
    protected function mergeFilters()
    {
        $toMerge = [];

        foreach ($this->filters as $filter) {
            $toMerge[] = $filter->getData();
        }

        return $this->merged = new ArrayOk(call_user_func_array('array_merge', $toMerge));
    }
}
