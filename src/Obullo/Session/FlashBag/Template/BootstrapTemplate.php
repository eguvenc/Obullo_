<?php

namespace Obullo\Session\FlashBag\Template;

use Obullo\Session\FlashBag\FlashBagTemplateInterface;

/**
 * FlashBag Template for Bootstrap css
 *
 * @link http://getbootstrap.com
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class BootstrapTemplate implements FlashBagTemplateInterface
{
    /**
     * Success flash message
     *
     * @param string $message notice
     *
     * @return object
     */
    public function success($message)
    {
        return sprintf(
            '<div class="%s">%s %s</div>',
            'alert alert-success',
            '<span class="glyphicon glyphicon-ok-sign"></span>',
            $message
        );
    }

    /**
     * Error flash message
     *
     * @param string $message notice
     *
     * @return object
     */
    public function error($message)
    {
        return sprintf(
            '<div class="%s">%s %s</div>',
            'alert alert-danger',
            '<span class="glyphicon glyphicon-remove-sign"></span>',
            $message
        );
    }

    /**
     * Info flash message
     *
     * @param string $message notice
     *
     * @return object
     */
    public function info($message)
    {
        return sprintf(
            '<div class="%s">%s %s</div>',
            'alert alert-info',
            '<span class="glyphicon glyphicon-info-sign"></span>',
            $message
        );
    }

    /**
     * Warning flash message
     *
     * @param string $message notice
     *
     * @return object
     */
    public function warning($message)
    {
        return sprintf(
            '<div class="%s">%s %s</div>',
            'alert alert-warning',
            '<span class="glyphicon glyphicon-exclamation-sign"></span>',
            $message
        );
    }
}
