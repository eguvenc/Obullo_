<?php

namespace Obullo\Config;

use Obullo\Config\ConfigInterface as Config;

trait ConfigAwareTrait
{
    /**
     * Config
     *
     * @var object
     */
    protected $config;

    /**
     * Set config
     *
     * @param object $config config
     *
     * @return $this
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get config
     *
     * @return object
     */
    public function getConfig()
    {
        return $this->config;
    }
}
