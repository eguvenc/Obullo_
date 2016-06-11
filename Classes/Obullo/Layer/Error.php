<?php

namespace Obullo\Layer;

/**
 * Layer error handler
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Error
{
    const HEADER = '<div style="
    white-space: pre-wrap;
    white-space: -moz-pre-wrap;
    white-space: -pre-wrap;
    white-space: -o-pre-wrap;
    font-size:12px;
    font-family:Arial,Verdana,sans-serif;
    font-weight:normal;
    word-wrap: break-word; 
    background: #FFFAED;
    border: 1px solid #ddd;
    border-radius: 4px;
    -moz-border-radius: 4px;
    -webkit-border-radius:4px;
    padding:5px 10px;
    color:#E53528;
    font-size:12px;">';
    const FOOTER = '</div>';

    /**
     * Format layer errors
     *
     * @param string $response layer response
     * 
     * @return mixed
     */
    public static function getError($response)
    {
        $errorStr = $response['error'];
        
        $uri   = isset($response['uri']) ? $response['uri'] : false;
        $code  = isset($response['code']) ? $response['code'] : 0;

        if ($code > 0) {
            $errorStr = $code. ': '.$errorStr; 
        }
        if ($uri) {
            $errorStr = $errorStr. ' url : '. $uri;
        }
        return static::HEADER .$errorStr. static::FOOTER;
    }
    
}