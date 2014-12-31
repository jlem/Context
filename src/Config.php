<?php namespace Jlem\Context;

use Jlem\Context\Filters\Filter;
use Jlem\ArrayOk\ArrayOk;

class Config
{
    protected $Context;
    protected $contextOrder = null;
    protected $clipContext = true;
    protected $result;

    protected $filters = [];
    protected $disabledFilters = [];
    protected $contextIsDisabled = false;

    public function __construct($context)
    {
        $this->setContext($context);
    }



    /**
     * Sets the context data 
     *
     * @param ArrayOk $Context
     * @access public
     * @return void
    */

    public function setContext($context)
    {
        $this->Context = $context instanceof ArrayOk ? $context : new ArrayOk($context);
    }



    /**
     * Disables the use of context data by restricting filter application to just the 'common' filter 
     *
     * @access public
     * @return void
    */

    public function disableContext()
    {
        $this->contextIsDisabled = true;
    }



    /**
     * Re-enables the use of contexts 
     *
     * @access public
     * @return void
    */

    public function enableContext()
    {
        $this->contextIsDisabled = false;
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
     * Disables the use of a filter 
     *
     * @param string $name
     * @access public
     * @return void
    */

    public function disableFilter($name)
    {
        $this->disabledFilters[$name] = null;
    }



    /**
     * Re-enables the use of a filter 
     *
     * @param string $name
     * @access public
     * @return void
    */

    public function enableFilter($name)
    {
        if (isset($this->disabledFilters[$name])) {
            unset($this->disabledFilters[$name]);
        }
    }



    /**
     * Gets all configs, or a specific config by key. 
     * The first time this runs, it gets cashed so it doesn't have to re-merge all filters on each call.
     * To force a re-merge in the event you add a new filter to the stack or modify an existing filter, pass in "true" as the second parameter.
     *
     * @param mixed string|null $key
     * @access public
     * @return ArrayOk
    */

	public function load()
	{
        if ($this->result) {
            return $this->result;
        }

        return $this->result = $this->mergeFilters();
	}



    /**
     * Gets a fresh instance of the merged configs, rather than a registry cache 
     *
     * @access public
     * @return ArrayOk
    */

    public function refresh()
    {
        return $this->mergeFilters();
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
     * Appends a new key/value pair to the existing context 
     *
     * @param string $key
     * @param mixed $value
     * @access public
     * @return void
    */

    public function appendContext($key, $value)
    {
        $this->Context->append($value, $key);
    }



    /**
     * Globally applies a new context order to all filters
     *
     * @param mixed array|string $contextOrder
     * @param boolean $clip intersects the given order with the context to reduce keys
     * @param boolean $resetNewContextAfterGet tells the filters to reset the new context after get
     * @access protected
     * @return void
    */

    public function reorderContext($newOrder, $clip = true)
    {
        $this->contextOrder = $newOrder;
        $this->clipContext = $clip;
    }



    /**
     * Reorders the context of a specific filter 
     *
     * @param string $filterName
     * @param mixed array|string $newOrder
     * @param boolean $clip
     * @access public
     * @return void
    */

    public function reorderFilterContext($filterName, $newOrder, $clip = true)
    {
        $this->getFilter($filterName)->reorderContext($newOrder, $clip);
    }



    /**
     * Reverts context order back to original settings
     *
     * @access public
     * @return void
    */

    public function resetContextOrder()
    {
        $this->contextOrder = null;
        $this->clipContext = true;
    }



    /**
     * Runs array_merge on the filter stack, in the order the filters were added
     * 
     * @return ArrayOk
     */
    
    protected function mergeFilters()
    {
        $toMerge = array();

        foreach ($this->getActiveFilters() as $filterName => $filter) {
            
            // Apply the current context and global context order to each filter
            $filter->applyContext($this->Context, $this->contextOrder, $this->clipContext);

            // Build an array of data from each filter that will eventually be merged
            $toMerge[] = $this->normalizeData($filter->getData());
        }

        // Merge the filter data
        return new ArrayOk(call_user_func_array('array_merge', $toMerge));
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



    /**
     * Returns an array of only the active filters 
     *
     * @access protected
     * @return array
    */

    protected function getActiveFilters()
    {
        if ($this->contextIsDisabled) {
            return array('common' => $this->filters['common']);
        }

        return array_diff_key($this->filters, $this->disabledFilters);
    }

    public function lazyAssemble()
    {
        // optional function for lazy assembly
    }
}
