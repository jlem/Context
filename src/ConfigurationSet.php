<?php namespace Jlem\Context;

use Jlem\ArrayOk\ArrayOk;

class ConfigurationSet extends ArrayOk
{

	public function setConfig($items)
	{
        $this->construcRecursively($items);
	}



    /**
     * Find a specific default 
     *
     * @param mixed $key
     * @access public
     * @return void
    */

    public function findDefault($key)
    {
        return $this['defaults'][$key] ?: array();
    }



    /**
     * Looks up a specific nested tree of overrides 
     *
     * @param array $context
     * @access public
     * @return array
    */

    public function findOverrides(array $context)
    {
        return $this['overrides']->get($context)->toArray();
    }



    /**
     * Loops up a specific tree branch by the given key 
     *
     * @param mixed $data
     * @param mixed $value
     * @access protected
     * @return void
    */

    protected function lookup($branch, $key) 
    {
        if (isset($branch[$key])) {
            return $branch[$key];
        }

        return $branch;
    }



    /**
     * Validates the configuration and checks for root level branches called 'defaults' and 'overrides'. Makes sure they are both at least arrays 
     *
     * @param mixed $config
     * @access protected
     * @return void
    */

    protected function validate($tree) 
    {
        $branches = ['defaults', 'overrides', 'common'];
        foreach ($branches as $branch) {
            $this->validateBranch($tree, $branch);
        }
    }



    /**
     * Looks up a specific branch to make sure it exists, and is an array 
     *
     * @param array $config
     * @param string $key
     * @access protected
     * @return void
     * @throws InvalidArgumentException
    */

    protected function validateBranch($tree, $branch) 
    {
        if (!isset($tree[$branch]) || !is_array($tree[$branch])) {
            throw new \InvalidArgumentException("Your configs are missing a $branch array");
        }
    }
}
