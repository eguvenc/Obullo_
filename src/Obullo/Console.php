<?php

namespace Obullo;

/**
 * Console log helper
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Console
{
    /**
     * Console.log
     *
     * @param string $message debug message
     * @param array  $extra   paylaod
     * @return void
     */
    public static function log($message, $extra = array())
    {
        if (! empty($extra)) {
            $payload = json_encode($extra, JSON_UNESCAPED_UNICODE);
            $message = $message." ".$payload." []";
        } else {
            $message = $message." [] []";
        }
        file_put_contents(
            APP_PATH .'/Data/http.log',
            $message. PHP_EOL,
            FILE_APPEND
        );
    }
}
