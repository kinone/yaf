<?php
/**
 * Description of Request_Simple.php.
 *
 * @package Kinone\Yaf
 */

namespace Kinone\Yaf;

class Request_Simple extends Request_Abstract
{
    public function __construct()
    {
        $this->method = 'CLI';
    }

    public function get($name, $default = null)
    {
        return $default;
    }

    public function getQuery($name, $default = null)
    {
        return $default;
    }

    public function getPost($name, $default = null)
    {
        return $default;
    }

    public function getRequest($name, $default = null)
    {
        return $default;
    }

    public function getCookie($name, $default = null)
    {
        return $default;
    }

    public function getFiles($name, $default = null)
    {
        return $default;
    }

    public function isXmlHttpRequest($name, $default = null)
    {
        return false;
    }
}
