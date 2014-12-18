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
        $toMerge = array();

        foreach ($sequence->toArray() as $context) {
            if ($config->exists($context)) {
                $toMerge[] = $config[$context]->toArray();
            }
        }

        return (empty($toMerge)) ?: call_user_func_array('array_merge', $toMerge);
    }
}
