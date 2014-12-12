<?php namespace Jlem\Context\Filters;

use Jlem\ArrayOk\ArrayOk;

class Condition
{
    protected $condition;
    protected $configuration;

    public function __construct($initialKey, $initialValue)
    {
        $this->condition[$initialKey] = $initialValue;
    }

    public function then(array $data)
    {
        $this->configuration = $data;
    }

    public function andWhen($key, $value)
    {
        $this->condition[$key] = $value;
        return $this;
    }

    public function matchesContext(ArrayOk $Context)
    {
        foreach ($this->condition as $key => $value) {
            if (!$Context->exists($key)) {
                return false;
            }

            if (!preg_match("#{$Context[$key]}#", $value)) {
                return false;
            }
        }

        return true;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }
}
