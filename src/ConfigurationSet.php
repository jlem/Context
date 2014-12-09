<?php namespace Jlem\Context;

class ConfigurationSet
{
	protected $config;

	public function __construct(array $config)
	{
		$this->setConfig($config);
	}

	public function setConfig($config)
	{
		$this->checkDefaultConfigExist($config);
		$this->config = $config;
	}

	public function getDefaultConfig()
	{
		return $this->config['default'];
	}

	public function getContextConfig($contextGroup, $contextValue)
	{
		if (isset($this->config[$contextGroup][$contextValue])) {
			return $this->config[$contextGroup][$contextValue];
		}
	}

	public function exists($contextGroup, $contextValue)
	{
		return (isset($this->config[$contextGroup][$contextValue]));
	}

	protected function checkDefaultConfigExist($config)
	{
		if (!isset($config['default'])) {
			throw new \InvalidArgumentException('Your configs are missing a default array');
		}
	}
}