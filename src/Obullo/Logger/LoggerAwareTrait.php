<?php

namespace Obullo\Logger;

use Psr\Log\LoggerInterface as Logger;

trait LoggerAwareTrait
{
    /**
     * Logger
     *
     * @var object
     */
    protected $logger;

    /**
     * Set logger
     *
     * @param object $logger logger
     *
     * @return $this
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;

        return $this;
    }

    /**
     * Get logger
     *
     * @return object
     */
    public function getLogger()
    {
        return $this->logger;
    }
}
