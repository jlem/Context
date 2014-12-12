<?php namespace Jlem\Context\Filters;

use Jlem\ArrayOk\ArrayOk;

abstract class Filter
{
    protected $contextSequence;

    public function by($contextSequence)
    {
        $this->contextSequence = $this->normalizeSequence($contextSequence);
    }

    protected function normalizeSequence($sequence) 
    {
        return is_array($sequence) ? $sequence : explode('.', $sequence);
    }

    public function getContextSequence()
    {
        $originalSequence = $this->Context->toArray();
        return (empty($this->contextSequence)) ? $originalSequence : $this->$contextSequence;
    }

    abstract public function getData();
}
