<?php

namespace Obullo\View;

use LogicException;
use Obullo\Container\ContainerAwareTrait;
use Obullo\Container\ContainerAwareInterface;

/**
 * Default engine
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Native implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * Default path
     * 
     * @var string
     */
    protected $path;

    /**
     * View filename
     * 
     * @var string
     */
    protected $filename;

    /**
     * View folders
     * 
     * @var array
     */
    protected $folders = array();

    /**
     * Constructor
     * 
     * @param stirng $path default
     */
    public function __construct($path)
    {
        $this->path = $path.'/';
    }

    /**
     * Add a new template folder for grouping templates under different namespaces.
     * 
     * @param string $name      name
     * @param string $directory folder
     * 
     * @return Engine
     */
    public function addFolder($name, $directory)
    {
        $this->folders[$name] = $directory;

        return $this;
    }

    /**
     * Remove a template folder.
     * 
     * @param string $name name
     * 
     * @return Engine
     */
    public function removeFolder($name)
    {
        unset($this->folders[$name]);

        return $this;
    }

    /**
     * Get collection of all template folders
     * 
     * @return array
     */
    public function getFolders()
    {
        return $this->folders;
    }

    /**
     * Create a new template and render it.
     * 
     * @param string $name name
     * @param array  $data data
     * 
     * @return string
     */
    public function render($name, array $data = array())
    {
        $name = $this->normalizeFilename($name);

        return $this->make($name, $data);
    }

    /**
     * Make
     * 
     * @param string $name name
     * @param array  $data data
     * 
     * @return string
     */
    public function make($name, $data = array())
    {
        $this->filename = $name; // Skip extract name collisions
        extract($data);
        unset($name);

        ob_start();
        include $this->getDefaultPath() . $this->filename;
        $body = ob_get_clean();
        
        return $body;
    }

    /**
     * Make available controller variables in view files
     * 
     * @param string $key Controller variable name
     * 
     * @return void
     */
    public function __get($key)
    {
        return $this->getContainer()->get($key);
    }

    /**
     * Normalize filename
     * 
     * @param string $name filename
     * 
     * @return string
     */
    protected function normalizeFilename($name)
    {
        if (strpos($name, '::') > 0) {  // Folder support.

            $this->path = '';  // Reset path variable.
            $parts = explode('::', $name);

            return rtrim($this->getFolderPath($parts[0]), '/').'/'.$parts[1];
        }
        return $name;
    }

    /**
     * Returns to folder path
     * 
     * @param string $name filename
     * 
     * @return string path
     */
    protected function getFolderPath($name)
    {
        $folders = $this->getFolders();

        if ( ! isset($folders[$name])) {
            throw new LogicException(
                sprintf(
                    'The template directory "%s" is not defined.',
                    $name
                )
            );
        }
        return $folders[$name];
    }

    /**
     * Returns to default view path
     * 
     * @return string
     */
    protected function getDefaultPath()
    {
        return $this->path;
    }

}