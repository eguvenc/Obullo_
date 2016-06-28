<?php

namespace Obullo\View;

/**
 * View Interface
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
interface ViewInterface
{
    /**
     * Set the template file extension.
     * 
     * @param string|null $fileExtension Pass null to manually set it.+
     * 
     * @return Engine
     */
    public function setFileExtension($fileExtension);

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
    public function render($filename, $data = array());

    /**
     * Get nested view files as string from current module /view folder
     * 
     * @param string $filename filename
     * @param mixed  $data     array data
     * 
     * @return string
     */
    public function get($filename, $data = array());
   
}