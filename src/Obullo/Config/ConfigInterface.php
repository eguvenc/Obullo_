<?php

namespace Obullo\Config;

/**
 * Config Interface
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface ConfigInterface
{
    /**
     * Load config file
     *
     * @param oject
     */
    public function load($filename);

    /**
     * Returns to array
     *
     * @return array
     */
    public function getArray();

    /**
     * Returns to Zend config object
     *
     * @return object
     */
    public function getObject();
}
