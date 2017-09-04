<?php

/**
 * This file is part of the JantaoDevSitemapBundle package
 */

namespace JantaoDev\SitemapBundle\Sitemap;

use JantaoDev\SitemapBundle\Sitemap\HostInterface;

/**
 * Host object
 * 
 * @author Sergey Hayevoy <jantao.dev@gmail.com>
 */
class Host implements HostInterface
{
    
    const SCHEME_HTTP = 0;
    const SCHEME_HTTPS = 1;
    const SCHEME_ONLY_HTTPS = 2;
    
    protected $host;
    protected $hostWithScheme;
    protected $hostOptionalWithScheme;

    /**
     * @param string $host Host name
     * @param int $scheme Scheme mode (SCHEME_HTTP, SCHEME_HTTPS, SCHEME_ONLY_HTTPS)
     * @param int $port Port
     */
    public function __construct($host, $scheme = 0, $port = null)
    {
        $defaultPort = ($scheme == self::SCHEME_HTTP ? 80 : 433);
        if ($port == $defaultPort) {
            $port = null;
        }
        if ($port) {
            $host .= ":$port";
        }
        $this->host = $host;
        $this->hostWithScheme = ($scheme == self::SCHEME_HTTP ? 'http://' : 'https://').$host;
        $this->hostOptionalWithScheme = ($scheme == self::SCHEME_ONLY_HTTPS ? 'https://' : '').$host;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getHost()
    {
        return $this->host;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getHostWithScheme()
    {
        return $this->hostWithScheme;
    }
    
    /**
     * Get host optionaly with scheme
     * This method uses for robots.txt host parameter. 
     * The scheme will be added if the host is available only for the scheme.
     * Scheme will be added only in SCHEME_ONLY_HTTPS mode.
     * 
     * @return string
     */
    public function getHostOptionalWithScheme()
    {
        return $this->hostOptionalWithScheme;
    }
    
}
