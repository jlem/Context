<?php namespace Jlem\Context\Filters;

use Jlem\ArrayOk\ArrayOk;

class DefaultsFilter extends Filter
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
        $config = $this->Config['defaults'];
        $sequence = $this->getContextSequence();
        $toMerge = [];
        foreach ($sequence as $context) {
            if ($config->exists($context)) {
                $toMerge[] = $config[$context]->toArray();
            }
        }

        return call_user_func_array('array_merge', $toMerge);
        
    }
    
    protected function validateOutput($output)
    {
        return (is_array($output) || $output instanceof ArrayOk);
    }

    protected function validateInput(ArrayOk $Config)
    {
        if (!$Config->exists('defaults')) {
            throw new \InvalidArgumentException('The given configuration data does not contain a key called "common"');
        }
    }

    protected function merge()
    {
        $toMerge = [];

        foreach ($this->filters as $filter) {
            $toMerge[] = $filter->getData();
        }

        return $this->merged = new ArrayOk(call_user_func_array('array_merge', $toMerge));
    }
}
