<?php

namespace Obullo\Container;

use Interop\Container\ContainerInterface as InteropContainerInterface;

/**
 * Immutable container aware trait
 */
trait ContainerAwareTrait
{
    /**
     * @var \Interop\Container\ContainerInterface
     */
    protected $container;

    /**
     * Set a container.
     *
     * @param  \Interop\Container\ContainerInterface $container
     * @return $this
     */
    public function setContainer(InteropContainerInterface $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Get the container.
     *
     * @return \League\Container\ImmutableContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
}
