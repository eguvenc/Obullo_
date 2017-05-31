<?php

use League\Container\Container;

use Obullo\Cookie\Cookie;

class CookieTest extends PHPUnit_Framework_TestCase
{
    protected $container;

    /**
     * Setup variables
     *
     * @return void
     */
    public function setUp()
    {
        $this->cookie = new Cookie(array('string' => 'ok', 'integer' => 10));
    }

    /**
     * Shared
     *
     * @return void
     */
    public function testSetDefaults()
    {
        $params = [
            'domain' => 'test.com',
            'path'   => '/',
            'secure' => true,
            'httpOnly' => false,
            'expire' => 86400,
        ];

        $this->cookie->setDefaults($params);
        $defaults = $this->cookie->getDefaults();

        $this->assertEquals('test.com', $defaults['domain'], "I expect that the value is equal to 'test.com'.");
    }
}
