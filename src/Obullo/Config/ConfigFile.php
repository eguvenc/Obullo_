<?php

namespace Obullo\Config;

use Zend\Config\Reader\Ini;
use Zend\Config\Config as ZendConfig;

/**
 * App ini config loader
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class ConfigFile extends Ini
{
	/**
	 * Env value
	 * 
	 * @var string
	 */
	protected $env;

	/**
	 * Constructor
	 * 
	 * @param string $filename filename
	 */
	public function __construct($filename)
	{
		$this->filename = $filename;
	}

	/**
	 * Set env
	 *
	 * @return void
	 */
	public function setEnv()
	{
		$env = getenv("APPLICATION_ENV");

		$this->env = ($env) ? $env . "." : "dev.";
	}

	/**
	 * Returns to config object
	 * 
	 * @return object
	 */
	public function getObject()
	{
		return new ZendConfig($this->fromFile(APP . 'Config/'. $this->env . $this->filename), true);
	}

}