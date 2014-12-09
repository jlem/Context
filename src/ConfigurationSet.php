<?php namespace Jlem\Context;

class ConfigurationSet
{
	protected $config;

	public function __construct(array $config)
	{
		$this->setConfig($config);
	}

    /**
     * Valiates the given array to make sure 'defaults' key is present, and sets it if true
     *
     * @param array $config
     * @access public
     * @return void
    */

	public function setConfig($config)
	{
        $this->validate($config);
        $this->config = $config;
	}



    /**
     * Returns all of the common settings/configurations 
     *
     * @access public
     * @return array
    */

    public function getCommon()
    {
        return $this->config['common'];
    }



    /**
     * Returns the default configurations 
     *
     * @access public
     * @return array
    */

	public function getDefaults()
	{
		return $this->config['defaults'];
	}



    /**
     * Returns the configuration overrides 
     *
     * @access public
     * @return array
    */

    public function getOverrides()
    {
        return $this->config['overrides'];
    }



    public function findDefault($key)
    {
        $defaults = $this->getDefaults();

        if (isset($defaults[$key])) {
            return $defaults[$key];
        }

        return array();
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
        $branch = $this->getOverrides();

        foreach ($context as $key) {
            $branch = $this->lookup($branch, $key);
        }
        
        return $branch;
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
