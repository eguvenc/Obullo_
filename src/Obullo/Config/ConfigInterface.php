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
     * @return array if the file was loaded correctly
     */
    public function get($filename);

    /**
     * Set configuration variables
     * 
     * @param string $key  name
     * @param array  $data data
     *
     * @return void
     */
    public function set($key, array $data);

    /**
     * Save array data config file
     *
     * @param string $filename full path of the file
     * @param array  $data     config data
     * 
     * @return void
     */
    public function write($filename, array $data);
}