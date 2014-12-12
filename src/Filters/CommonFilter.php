<?php namespace Jlem\Context\Filters;

use Jlem\ArrayOk\ArrayOk;

class CommonFilter extends Filter
{
    protected $Config;

    public function __construct(ArrayOk $Config)
    {
        $this->validateInput($Config);
        $this->Config = $Config;
    }
    
    public function getData()
    {
        $result = $this->Config['common'];
        return ($this->validateOutput($result)) ? $result->toArray() : array();
    }
    
    protected function validateOutput($output)
    {
        return (is_array($output) || $output instanceof ArrayOk);
    }

    protected function validateInput(ArrayOk $Config)
    {
        if (!$Config->exists('common')) {
            throw new \InvalidArgumentException('The given configuration data does not contain a key called "common"');
        }
    }
}
