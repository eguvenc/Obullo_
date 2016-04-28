<?php

namespace Obullo\Router;

/**
 * Domain properties
 * 
 * @copyright 2009-2016 Obullo
 * @license   http://opensource.org/licenses/MIT MIT license
 */
class Domain
{
    /**
     * Static server host
     * 
     * @var string
     */
    protected $host;

    /**
     * Current domain name
     * 
     * @var string
     */
    protected $name;

    /**
     * Set immutable domain name
     * 
     * @var string
     */
    protected $immutable;

    /**
     * Matches
     * 
     * @var array
     */
    protected $matches = array();

    /**
     * Set domain address
     * 
     * @param string $name domain
     *
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns to current domain name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set immutable domain address
     * 
     * @param string $domain address
     *
     * @return void
     */
    public function setImmutable($domain)
    {
        $this->immutable = $domain;
    }

    /**
     * Get immutable domain
     * 
     * @return string
     */
    public function getImmutable()
    {
        return $this->immutable;
    }

    /**
     * Set server host
     * 
     * @param string $host address
     *
     * @return void
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * Returns to server host address
     * 
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Returns to root domain of host
     * 
     * @return void
     */
    public function getBaseHost()
    {
        if ($subname = $this->isSub($this->getHost())) {
            return mb_substr($this->getHost(), mb_strlen($subname) + 1);
        }
        return $this->getHost();
    }

    /**
    * Detect static domain
    * 
    * @return void
    */
    public function match()
    {
        $domainName = $this->getName();

        if (empty($domainName)) {
            $domainName = $this->getHost();
        }
        if ($match = $this->hasMatch($domainName)) { // If host matched with group domain assign as it.
            $this->setName($match);
            return true;                // Regex match.
        }
        return false;  // No regex match.
    }

    /**
     * Lazy load for domain names
     * 
     * @param string $domainName domain name
     * 
     * @return array matches
     */
    public function hasMatch($domainName)
    {
        if ($domainName == $this->getBaseHost()) {
            return $domainName;
        }
        if (isset($this->matches[$domainName])) {
            return $this->matches[$domainName];
        }
        if (preg_match('#^'.$domainName.'$#', $this->getHost(), $matches)) {
            return $this->matches[$domainName] = $matches[0];
        }
        return false;
    }

    /**
     * Check domain has sub name
     * 
     * @param string $domain name
     * 
     * @return boolean
     */
    public function isSub($domain)
    {
        if (empty($domain)) {
            return false;
        }
        $subDomain = $this->getSubName($domain);
        return (empty($subDomain)) ? false : $subDomain;
    }

    /**
     * Get sub domain e.g. test.example.com returns to "test".
     * 
     * @param string $domain name
     * 
     * @return boolean
     */
    public function getSubName($domain)
    {
        return mb_strstr($domain, '.', true);
    }

    /**
     * Returns to matched domains
     * 
     * @return array
     */
    public function getMatches()
    {
        return $this->matches;
    }

}