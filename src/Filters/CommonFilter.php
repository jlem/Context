<?php namespace Jlem\Context\Filters;

use Jlem\ArrayOk\ArrayOk;

class CommonFilter extends Filter
{
    /**
     * Returns the data found in the config's 'common' array
     * 
     * @return ArrayOk
     */
    
    public function getData()
    {
        if ($this->configIsValid('common')) {
            return $this->Config['common'];    
        }
    }
}
