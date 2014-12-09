<?php namespace Jlem\Context\Mergers;

use Jlem\Context\ContextSet;
use Jlem\Context\ConfigurationSet;

class SimpleMerger implements MergerInterface
{
	protected $Context;
	protected $Config;

    public function __construct(ContextSet $Context, ConfigurationSet $Config)
    {
		$this->Context = $Context;
		$this->Config = $Config;
    }



    /**
     * Uses array_merge to cascade override configs in the order given when running `load($configOrder)`
     * The order gets overridden from left to right in the sequence, and fall back from right to left
     *
     * @param mixed $sequence
     * @access protected
     * @return array
    */

	public function mergeContexts($contextSequence)
	{
        $sequence = $this->normalizeSequence($contextSequence);

		$toMerge[] = $this->Config->getDefaultConfig();

		foreach ($sequence as $contextGroup) {
			$contextValue = $this->Context->getContext($contextGroup);
			$this->addMergeArray($toMerge, $contextGroup, $contextValue);
		}

		return call_user_func_array('array_merge', $toMerge);
	}
    


    /**
     * Changes the context values to be processed when merging 
     *
     * @param ContextSet $Context
     * @access public
     * @return void
    */

    public function changeContext(ContextSet $Context)
    {
        $this->Context = $Context;
    }
    


    /**
     * Appends a new configuration array to the set to be merged
     *
     * @param array $toMerge
     * @param string $contextGroup
     * @param string $contextValue
     * @access protected
     * @return void
    */

	protected function addMergeArray(array &$toMerge, $contextGroup, $contextValue) 
	{
		if ($this->Config->exists($contextGroup, $contextValue)) {
			$toMerge[] = $this->Config->getContextConfig($contextGroup, $contextValue);
		}
	}
    

    /**
     * Converts the context sequence to an array if dot notation string, or passes through if already array
     *
     * @param mixed string|array $contextSequence
     * @access protected
     * @return array
    */

	protected function normalizeSequence($contextSequence)
	{
		if (is_string($contextSequence)) {
			return explode('.', $contextSequence);
		}
		
		return $contextSequence;
	}
}
