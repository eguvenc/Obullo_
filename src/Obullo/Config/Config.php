<?php

namespace Obullo\Config;

use Zend\Config\Config as ZendConfig;
use Interop\Container\ContainerInterface as Container;

/**
 * Config Class
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Config extends ZendConfig
{
    /**
     * Loaded files
     * 
     * @var array
     */
    protected $files = array();

    /**
     * Load configuration file
     *
     * @param string $filename the config file name
     * 
     * @return object config
     */
    public function load($filename, $reader = 'php')
    {
        if (! isset($this->files[$filename])) {
            $this->files[$filename] = new Self(include APP . 'Config/' . $filename . '.php');
        }
        return $this->files[$filename];
    }

}