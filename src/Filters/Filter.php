<?php namespace Jlem\Context\Filters;

use Jlem\ArrayOk\ArrayOk;

abstract class Filter
{
    protected $givenSequence;

    public function by($givenSequence)
    {
        $this->givenSequence = $this->normalizeSequence($givenSequence);
    }

    protected function normalizeSequence($sequence) 
    {
        return is_array($sequence) ? $sequence : explode('.', $sequence);
    }

    public function getContextSequence()
    {
        if (empty($this->givenSequence)) {
            return $this->Context; 
        }

        return new ArrayOk($this->Context->orderByAndGetIntersection($this->givenSequence));
    }

    protected function makeSequenceIntersection($original, $given)
    {
        return array_intersect_key($original, array_flip($given));
    }

    protected function configIsValid($item)
    {
        return ($this->Config->itemIsAOk($item));
    }

    abstract public function getData();
}
