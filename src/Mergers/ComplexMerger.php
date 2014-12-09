<?php namespace Jlem\Context\Mergers;

use Jlem\Context\ContextSet;
use Jlem\Context\ConfigurationSet;

class ComplexMerger implements MergerInterface
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
        // Replace dot notation with an array
        $sequence = $this->normalizeSequence($contextSequence);

        // Reorder the initial contexts by the requested sequence order
        $contexts = $this->Context->reorder($sequence);

        // Get config data using the context as one big complex key for nested lookups
        $overrides = $this->Config->findOverrides($contexts);

        $toMerge[] = $this->Config->getCommon();
        
        foreach($contexts as $context) {
            $toMerge[] = $this->Config->findDefault($context);
        }

        $toMerge[] = $overrides;

        // Merge what we find (if anything), with the config's defaults
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
