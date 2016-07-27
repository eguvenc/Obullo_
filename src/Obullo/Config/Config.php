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
class Config extends Ini
{
    /**
     * Env value
     *
     * @var string
     */
    protected $env;

    /**
     * File path
     *
     * @var string
     */
    protected $file;

    /**
     * File data array
     *
     * @var array
     */
    protected $fileArray = array();

    /**
     * File data objects
     *
     * @var array
     */
    protected $fileObject = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->env = getenv("APP_ENV");
    }

    /**
     * Load config file
     *
     * @param oject
     */
    public function load($filename)
    {
        $this->file = ROOT .'config/'.$this->env.'/'.$filename.'.ini';
        return $this;
    }

    /**
     * Returns to array
     *
     * @return array
     */
    public function getArray()
    {
        if (isset($this->fileArray[$this->file])) {
            return $this->fileArray[$this->file];
        }
        return $this->fileArray[$this->file] = $this->fromFile($this->file);
    }

    /**
     * Returns to config object
     *
     * @return object
     */
    public function getObject()
    {
        if (isset($this->fileObject[$this->file])) {
            return $this->fileObject[$this->file];
        }
        return $this->fileObject[$this->file] = new ZendConfig($this->getArray(), true);
    }
}
