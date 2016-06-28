<?php

namespace Obullo\Config;

/**
 * ConfigInterface Class
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface ConfigInterface
{
    /**
     * Load configuration file
     *
     * @param string $filename the config file name
     * 
     * @return object config
     */
    public function load($filename, $reader = 'php');
}