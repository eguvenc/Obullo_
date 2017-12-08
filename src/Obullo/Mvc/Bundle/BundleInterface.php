<?php

namespace Obullo\Mvc\Bundle;

/**
 * Bundle Interface
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface BundleInterface
{
    /**
     * Set app object
     *
     * @param object $app application
     *
     * @return void
     */
    public function setApplication($app);

    /**
     * Add service providers
     *
     * @return void
     */
    public function addServiceProviders();

    /**
     * Add middlewares
     *
     * @return void
     */
    public function addMiddlewares();

    /**
     * Returns to bundle name
     *
     * @return string
     */
    public function getName();
}
