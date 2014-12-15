<?php namespace Jlem\Context\Filters;

use Jlem\ArrayOk\ArrayOk;

class DefaultsFilter extends Filter
{
    protected $Config;
    protected $Context;

    public function __construct(ArrayOk $Config, ArrayOk $Context)
    {
        $this->Config = $Config;
        $this->Context = $Context;
    }
    
    public function getData()
    {
        if ($this->configIsValid('defaults')) {
            return $this->mergeDefaults($this->getContextSequence(), $this->Config['defaults']);
        }
    }

    protected function mergeDefaults(ArrayOk $sequence, ArrayOk $config)
    {
        $toMerge = array();

        foreach ($sequence->toArray() as $context) {
            if ($config->exists($context)) {
                $toMerge[] = $config[$context]->toArray();
            }
        }

        return (empty($toMerge)) ?: call_user_func_array('array_merge', $toMerge);
    }
}
