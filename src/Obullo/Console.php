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
    public function log($message, $extra = array())
    {
        $text = "[".date("Y-m-d H:i:s")."] console.LOG: ";

        if (! empty($extra)) {
            $payload = json_encode($extra, JSON_UNESCAPED_UNICODE);
            $text.= $message." ".$payload." []";
        } else {
            $text.= $message." [] []";
        }
        file_put_contents(
            APP_PATH .'/Data/http.log',
            $text. PHP_EOL,
            FILE_APPEND
        );
    }
}
