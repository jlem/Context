<?php namespace Jlem\Context;

use Jlem\Context\Exceptions\UndefinedContextException;
use Jlem\ArrayOk\ArrayOk;

class ContextSet extends ArrayOk
{
	protected $contexts;

	public function __construct(array $contexts)
	{
        $this->contexts = $contexts;
	}

    public function reorder(array $sequence)
    {
        $flipped = array_flip($sequence);
        $contexts = array_replace($flipped, $this->contexts);

        return $this->contexts = array_intersect_key($contexts, $flipped);
    }

	public function addContext($contextGroup, $contextValue)
	{
		$this->contexts[$contextGroup] = $contextValue;
	}

	public function forgetContext($contextGroup)
	{
		if ($this->exists($contextGroup)) {
			unset($this->contexts[$contextGroup]); return;
		}

		throw new UndefinedContextException($contextGroup);
	}

	public function getContexts()
	{
		return $this->contexts;
	}

	public function getContext($contextGroup)
	{
		if ($this->exists($contextGroup)) {
			return $this->contexts[$contextGroup];
		}

		throw new UndefinedContextException($contextGroup);
	}

	public function exists($contextGroup)
	{
		return (isset($this->contexts[$contextGroup]));
	}
}
