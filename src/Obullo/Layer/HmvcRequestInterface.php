<?php

namespace Obullo\Layer;

/**
 * Hmvc Request Interface
 */
interface HmvcRequestInterface
{
    /**
     * GET Request
     * 
     * @param string  $path       uri string
     * @param array   $data       get data
     * @param integer $expiration cache ttl
     * @param string  $folder     folder
     * 
     * @return string
     */
    public function get($path = '/', $data = array(), $expiration = '', $folder = 'Controller');

    /**
     * POST Request
     *
     * @param string  $path       uri string
     * @param array   $data       post data
     * @param integer $expiration cache ttl
     * @param string  $folder     foslder
     * 
     * @return string
     */
    public function post($path = '/', $data = array(), $expiration = '', $folder = 'Controller');

    /**
     * Flush cache
     * 
     * @param string $path uri string
     * @param array  $data params
     * 
     * @return boolean
     */
    public function flush($path, $data = array());

}
