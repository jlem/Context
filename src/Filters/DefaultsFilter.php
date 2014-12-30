<?php namespace Jlem\Context\Filters;

use Jlem\ArrayOk\ArrayOk;

class DefaultsFilter extends Filter
{
    /**
     * Returns the data found in the config's 'common' array
     * 
     * @return ArrayOk
     */
    
    public function getData()
    {
        if ($this->configIsValid('defaults')) {
            return $this->mergeDefaults($this->getContextSequence(), $this->Config['defaults']);
        }
    }


    /**
     * Merges all of the arrays found in the defaults configuration in the order defined by the context 
     *
     * @param ArrayOk $sequence
     * @param ArrayOk $config
     * @access protected
     * @return array
    */

    protected function mergeDefaults(ArrayOk $sequence, ArrayOk $config)
    {
        /*$items = $config->items;
        $sequence = $sequence->toArray();

        if (is_null($items)) {
            return;
        }

        $initialConfig = array_shift($items);
        $initialSequence = array_shift($sequence);

        foreach ($sequence as $context) {
            if ($config->exists($context)) {
                $initialConfig->replaceRecursiveNumeric($config[$context]);
            }
        }*/

        return array_reduce($sequence->toArray(), function($carry, $item) use ($config) {
                var_dump($config[$item]);
            if ($config->exists($item)) {
                $carry->replaceRecursiveNumeric($config[$item]);
                return $carry;
            }
        }, $config[$sequence->first()]);
    }
}
