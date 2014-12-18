<?php namespace Jlem\Context;

use Jlem\Context\Filters\Filter;
use Jlem\ArrayOk\ArrayOk;

class Context
{
    protected $Context;
    protected $filters = [];

    public function __construct(ArrayOk $Context)
    {
        $this->setContext($Context);
    }

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
    
    public function getFilter($name)
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
     * Sets the context data 
     *
     * @param ArrayOk $Context
     * @access public
     * @return void
    */

    public function setContext(ArrayOk $Context)
    {
        $this->Context = $Context;
    }


    /**
     * Gets the context data 
     *
     * @access public
     * @return ArrayOk
    */

    public function getContext()
    {
        return $this->Context;
    }


    /**
     * Runs array_merge on the filter stack, in the order the filters were added
     * 
     * @return ArrayOk
     */
    
    protected function mergeFilters()
    {
        $toMerge = array();

        foreach ($this->filters as $filter) {
            $filter->applyContext($this->Context);
            $toMerge[] = $this->normalizeData($filter->getData());
        }

        return $this->merged = new ArrayOk(call_user_func_array('array_merge', $toMerge));
    }


    /**
     * Normalizes the data returned from the filters as a basic array 
     *
     * @param mixed $data
     * @access protected
     * @return array
    */

    protected function normalizeData($data) 
    {
        if ($data instanceof ArrayOk) {
            return $data->toArray();
        }
        
        if (is_array($data)) {
            return $data;
        }

        return array();
    }
}
