<?php

namespace Obullo\Container;

/**
 * Container configuration interface
 */
interface ConfigurationInterface
{
    /**
     * Get the container
     *
     * @return void
     */
    public function addServiceProviders();
}
