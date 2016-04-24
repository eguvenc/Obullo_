<?php

namespace View;

/**
 * View Interface
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface ViewInterface
{
    /**
     * Register view folder
     * 
     * @param string $name folder name
     * @param string $path folder path
     *
     * @return void
     */
    public function addFolder($name, $path = null);

    /**
     * Check folders & returns to array if yes.
     *
     * @return boolean
     */
    public function getFolders();

    /**
     * Include nested view files from current module /view folder
     * 
     * @param string $filename filename
     * @param mixed  $data     array data
     * 
     * @return string                      
     */
    public function load($filename, $data = array());

    /**
     * Get nested view files as string from current module /view folder
     * 
     * @param string $filename filename
     * @param mixed  $data     array data
     * 
     * @return string
     */
    public function get($filename, $data = array());
    
    /**
     * Set variables
     * 
     * @param mixed $key key
     * @param mixed $val mixed
     * 
     * @return void
     */
    public function assign($key, $val = null);

    /**
     * Render view
     * 
     * @param string $filename filename
     * @param string $path     path
     * @param array  $data     data
     * 
     * @return string
     */
    public function render($filename, $path, $data = array());
   
}