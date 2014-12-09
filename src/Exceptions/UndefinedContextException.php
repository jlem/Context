<?php namespace Jlem\Context\Exceptions;

class UndefinedContextException extends \Exception
{
	public function __construct($context) 
	{
		parent::__construct("The context '$context' is unknown or has not been defined in the context set");
	}
}