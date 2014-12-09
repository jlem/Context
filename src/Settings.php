<?php namespace Jlem\Context;

class Settings
{
	protected $Context;
	protected $Config;
	protected $merged;

	public function __construct(ContextSet $Context, ConfigurationSet $Config)
	{
		$this->Context = $Context;
		$this->Config = $Config;
	}

	public function load($contextSequence)
	{
		$sequence = $this->normalizeSequence($contextSequence);
		return $this->merged = $this->merge($sequence);
	}

	public function get($key)
	{
		return $this->merged[$key];
	}

	protected function normalizeSequence($contextSequence)
	{
		if (is_string($contextSequence)) {
			return explode('.', $contextSequence);
		}
		
		return $contextSequence;
	}

	protected function merge($sequence)
	{
		$toMerge[] = $this->Config->getDefaultConfig();

		foreach ($sequence as $contextGroup) {
			$contextValue = $this->Context->getContext($contextGroup);
			$this->addMergeArray($toMerge, $contextGroup, $contextValue);
		}

		return call_user_func_array('array_merge', $toMerge);
	}

	protected function addMergeArray(array &$toMerge, $contextGroup, $contextValue) 
	{
		if ($this->Config->exists($contextGroup, $contextValue)) {
			$toMerge[] = $this->Config->getContextConfig($contextGroup, $contextValue);
		}
	}
}