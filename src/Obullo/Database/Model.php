<?php

namespace Obullo\Database;

use Obullo\Mvc\Controller;

/**
 * Model Class ( Default Model )
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Model
{
    /**
     * Returns to service object
     *
     * @param string $key
     *
     * @return object
     */
    public function __get($key)
    {
        $object = $this->getContainer()->get($key);

        if (is_object($object)) {
            return $object;
        }
        return;
    }

    /**
     * Returns to container
     *
     * @return object
     */
    public function getContainer()
    {
        return Controller::$instance->container;
    }
}
