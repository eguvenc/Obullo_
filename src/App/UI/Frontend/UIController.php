<?php

namespace App\UI\Frontend;

use Obullo\Controller;
use App\UI\Frontend\Controller\Header;
use App\UI\Frontend\Controller\Footer;

/**
 * UI Controller.
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class UIController extends Controller
{
    /**
     * Constructor
     * 
     * @param container $container container
     */
    public function __construct($container)
    {
        $header = new Header($container);
        $header->create();

        $footer = new Footer($container);
        $footer->create();
    }
}