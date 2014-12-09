<?php namespace Jlem\Context\Mergers;

use Jlem\Context\ContextSet;

interface MergerInterface
{
    public function mergeContexts($contextSequence);
    public function changeContext(ContextSet $Context);
}
