<?php namespace Jlem\Context\Filters;

use Jlem\ArrayOk\ArrayOk;

class CombinationFilter extends Filter
{
    protected $Config;
    protected $Context;

    public function __construct(ArrayOk $Config, ArrayOk $Context)
    {
        $this->validateInput($Config);
        $this->Config = $Config;
        $this->Context = $Context;
    }
    
    public function getData()
    {
        // Re-order the supplied context based on the requested context

        // Compute intersection of requested context with supplied context

        // Search in overrides to find the best canidate for the given get string

        // Return as array
    }
    
    protected function validateOutput($output)
    {
        return (is_array($output) || $output instanceof ArrayOk);
    }

    protected function validateInput(ArrayOk $Config)
    {
        if (!$Config->exists('combinations')) {
            throw new \InvalidArgumentException('The given configuration data does not contain a key called "combinations"');
        }
    }
}
