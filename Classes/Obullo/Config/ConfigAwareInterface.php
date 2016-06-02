<?php

namespace Obullo\Config;

use Obullo\Config\ConfigInterface as Config;

/**
 * Config Aware Interface
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface ConfigAwareInterface
{
    /**
     * Set configuration array or object
     * 
     * @param mixed $config array|object
     *
     * @return object
     */
    public function setConfig(Config $config);

    /**
     * Returns to config object
     * 
     * @return object
     */
    public function getConfig();
}