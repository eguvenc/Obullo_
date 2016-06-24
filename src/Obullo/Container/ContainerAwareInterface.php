<?php

namespace Obullo\Container;

use Interop\Container\ContainerInterface as InteropContainerInterface;

/**
 * Immutable container aware interface
 */
interface ContainerAwareInterface
{
    /**
     * Set a container
     *
     * @param \Interop\Container\ContainerInterface $container
     */
    public function setContainer(InteropContainerInterface $container);

    /**
     * Get the container
     *
     * @return \League\Container\ImmutableContainerInterface
     */
    public function getContainer();
}
