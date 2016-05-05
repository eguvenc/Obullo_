<?php

namespace Obullo\View\Plates;

use LogicException;
use Obullo\Container\ContainerAwareTrait;
use Obullo\Container\ContainerAwareInterface;
use League\Plates\Template\Template as AbstractTemplate;

/**
 * Container which holds template data and provides access to template functions.
 */
class Template extends AbstractTemplate implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Make available controller variables in view files
     * 
     * @param string $key Controller variable name
     * 
     * @return void
     */
    public function __get($key)
    {
        return $this->getContainer()->get($key);
    }
}
