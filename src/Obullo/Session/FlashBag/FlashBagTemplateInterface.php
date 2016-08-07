<?php

namespace Obullo\Session\FlashBag;

/**
 * FlashBag Template Interface
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface FlashBagTemplateInterface
{
    /**
     * Success flash message
     *
     * @param string $message notice
     *
     * @return object
     */
    public function success($message);

    /**
     * Error flash message
     *
     * @param string $message notice
     *
     * @return object
     */
    public function error($message);

    /**
     * Info flash message
     *
     * @param string $message notice
     *
     * @return object
     */
    public function info($message);

    /**
     * Warning flash message
     *
     * @param string $message notice
     *
     * @return object
     */
    public function warning($message);
}
