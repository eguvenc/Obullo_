<?php

class Group {

    protected $count  = 0;
    protected $groups = array();

    /**
     * Queue group
     * 
     * @param string   $pattern  pattern
     * @param callable $callable callable
     * 
     * @return void
     */
    public function enqueue($pattern, $callable)
    {   
        ++$this->count;
        $this->groups[$this->count] = [
            'pattern' => $pattern,
            'callable' => $callable,
            'middlewares' => array()
        ];
    }

    /**
     * Add middleware
     *
     * @return object group
     */
    public function add()
    {
        $args = func_get_args();
        $name = $args[0];
        unset($args[0]);

        $this->groups[$this->count]['middlewares'][] = array('name' => $name, 'params' => $args);
        return $this;
    }

    /**
     * Dequeue the group array
     * 
     * @return array|null
     */
    public function dequeue()
    {
        return array_shift($this->groups);
    }

    /**
     * Returns to true if we have no group
     * 
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->groups);
    }

}
