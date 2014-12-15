<?php namespace Jlem\Context\Filters;

use Jlem\ArrayOk\ArrayOk;

class CommonFilter extends Filter
{
    protected $Config;

    public function __construct(ArrayOk $Config)
    {
        $this->Config = $Config;
    }
    
    /**
     * Returns the data found in the config's 'common' array
     * 
     * @return array
     */
    
    public function getData()
    {
        $result = $this->Config['common'];
        return ($result instanceof ArrayOk) ? $result->toArray() : $result;
    }
}