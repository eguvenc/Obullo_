<?php

namespace Obullo\Connectors;

/**
 * Connector Interface
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface ConnectorInterface
{
    /**
     * Returns to connection
     *
     * @return object
     */
    public function getConnection();
}
