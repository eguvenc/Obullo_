<?php

use Obullo\Controller;

class Welcome extends Controller
{
    /**
     * Index
     * 
     * @return void
     */
    public function index()
    {
    	// $this->logger->debug('Cookie Class Initialized');

        $this->view->load('views::welcome');
    }
}
