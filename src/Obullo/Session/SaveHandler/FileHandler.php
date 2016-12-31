<?php

namespace Obullo\Session\SaveHandler;

/**
 * File Save Handler
 *
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class FileSessionHandler
{
    /**
     * Service parameters
     *
     * @var array
     */
    protected $params;

    /**
     * Key name
     *
     * @var string
     */
    protected $key = 'Session_';

    /**
     * Expiration time of current session
     *
     * @var integer
     */
    protected $lifetime = 7200; // two hours

    /**
     * Session Name
     *
     * @var string
     */
    protected $sessionName;

    /**
     * Session Save Path
     *
     * @var string
     */
    private $savePath;

    /**
     * Constructor
     *
     * @param array  $params service parameters
     */
    public function __construct(array $params)
    {
        $this->params   = $params;
        $this->key      = $params['session']['key'];
        $this->lifetime = $params['session']['gc_maxlifetime'];

        ini_set('session.gc_maxlifetime', $this->lifetime);
    }

    /**
    * Php5 session handler interface open function
    *
    * @param string $savePath    save path
    * @param string $sessionName session name
    *
    * @return bool
    */
    public function open($savePath, $sessionName)
    {
        $this->savePath = $savePath;
        $this->sessionName = $sessionName;

        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0777);
        }

        return true;
    }

    /**
     * Close the connection. Called by PHP when the script ends.
     *
     * @return void
     */
    public function close()
    {
        return true;
    }

    /**
     * Read data from the session.
     *
     * @param string $id session id
     *
     * @return mixed
     */
    public function read($id)
    {
        return (string)@file_get_contents("$this->savePath/sess_$id");
    }

    /**
     * Write data to the session.
     *
     * @param string $id   current session id
     * @param mixed  $data mixed data
     *
     * @return bool
     */
    public function write($id, $data)
    {
        return file_put_contents("$this->savePath/sess_$id", $data) === false ? false : true;
    }

    /**
     * Delete data from the session.
     *
     * @param string $id current session id
     *
     * @return bool
     */
    public function destroy($id)
    {
        $file = "$this->savePath/sess_$id";
        if (file_exists($file)) {
            unlink($file);
        }

        return true;
    }

    /**
     * Run garbage collection
     *
     * @param integer $maxLifetime expration time
     *
     * @return bool
     */
    public function gc($maxlifetime)
    {
        foreach (glob("$this->savePath/sess_*") as $file) {
            if (filemtime($file) + $maxlifetime < time() && file_exists($file)) {
                unlink($file);
            }
        }
        return true;
    }
}
