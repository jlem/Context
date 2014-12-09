<?php namespace Jlem\Context;

use Jlem\Context\Mergers\MergerInterface;

class Context
{
    protected $Merger;
	protected $merged;

	public function __construct(MergerInterface $Merger)
	{
		$this->Merger = $Merger;
	}



    /**
     * Uses the provided context cascade sequence to return the correct config settings
     * Contexts cascade from left to right, meaning the rightmost context will override
     * anything before it, and so on. Contexts that don't exist fall back from right to left
     * 
     * @example: user.country.section or ['user', 'country', 'section']
     * @param mixed string|array $contextSequence
     * @access public
     * @return array
    */

	public function load($contextSequence = [])
	{
		return $this->merged = $this->Merger->mergeContexts($contextSequence);
	}


    
    /**
     * After running load(), you can retrieve the value for a specific config key, or get all config values if no key is supplied
     *
     * @param mixed string|null $key
     * @access public
     * @return mixed
    */

	public function get($key = null)
	{
        if (empty($this->merged)) {
            throw new \UnderflowException('The flattened config is empty and there is nothing to get, please run `load($contextOrder)` first');
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
}
