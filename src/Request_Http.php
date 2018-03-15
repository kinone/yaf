<?php
/**
 * Description of Request_Http.php.
 *
 * @package Kinone\Yaf
 * @author zhenhao <phpcandy@163.com>
 */

namespace Kinone\Yaf;

class Request_Http extends Request_Abstract
{
    private $_baseUrl;

    public function __construct($uri = null, $baseUri = null)
    {
        if (null === $uri) {
            $this->_uri = $this->prepareRequestUri();
        }

        if (null === $baseUri) {
            $this->_baseUri = $this->prepareBaseUri();
        }

        if ($this->isCli()) {
            $this->method = 'Cli';
        }
    }

    public function getPathinfo()
    {
        if (null == $this->_pathinfo) {
            $this->_pathinfo = $this->preparePathinfo();
        }

        return $this->_pathinfo;
    }

    private function prepareBaseUri()
    {
        $filename = basename($this->getServer('SCRIPT_FILENAME'));
        if (basename($this->getServer('SCRIPT_NAME')) === $filename) {
            $baseUrl = $this->getServer('SCRIPT_NAME');
        } else if (basename($this->getServer('PHP_SELF')) === $filename) {
            $baseUrl = $this->getServer('PHP_SELF');
        } else {
            $path = $this->getServer('PHP_SELF', '');
            $file = $this->getServer('SCRIPT_FILENAME', '');
            $segs = explode('/', trim($file, '/'));
            $segs = array_reverse($segs);
            $index = 0;
            $last = count($segs);
            $baseUrl = '';
            do {
                $seg = $segs[$index];
                $baseUrl = '/'.$seg.$baseUrl;
                ++$index;
            } while ($last > $index && (false !== $pos = strpos($path, $baseUrl)) && 0 != $pos);
        }

        $this->_baseUrl = $baseUrl;
        return dirname($baseUrl);
    }

    private function prepareRequestUri()
    {
        $uri = null;
        if ($uri = $this->getServer('HTTP_X_REWRITE_URL')) {
            // do nothing
        } else if ($this->getServer('IIS_WasUrlRewritten') && ($uri = $this->getServer('UNENCODED_URL'))) {
            // do nothing
        } else if ($uri = $this->getServer('PATH_INFO')) {
            // do nothing
        } else if ($uri = $this->getServer('REQUEST_URI')) {
            if (strncasecmp($uri, 'http', 4) == 0) {
                $info = parse_url($uri);
                if (isset($info['path'])) {
                    $uri = $info['path'];
                }
            } else if (($pos = strpos($uri, '?')) !== false){
                $uri = substr($uri, $pos);
            }
        } else if ($uri = $this->getServer('ORIG_PATH_INFO')) {
            // do nothing
        }

        return '/' . trim($uri, '/');
    }

    private function preparePathinfo()
    {
        if (null === ($uri = $this->getRequestUri())) {
            return '/';
        }

        if (false != ($pos = strpos($uri, '?'))) {
            $uri = substr($uri, 0, $pos);
        }

        if ('' !== $uri && $uri[0] !== '/') {
            $uri = '/' . $uri;
        }

        if (null === ($baseUrl = $this->_baseUrl)) {
            return $uri;
        }

        if(substr($uri, 0, ($pos = strlen($baseUrl))) == $baseUrl) {
            $pathinfo = substr($uri, $pos);
        } else {
            $pathinfo = $uri;
        }

        if (false === $pathinfo || '' === $pathinfo) {
            return '/';
        }

        return $pathinfo;
    }

    public function get($name, $default = null)
    {
        if (isset($this->params[$name])) {
            return $this->params[$name];
        }

        foreach([$_POST, $_GET, $_COOKIE, $_SERVER] as $item) {
            if (isset($item[$name])) {
                return $item[$name];
            }
        }

        return $default;
    }

    public function getQuery($name, $default = null)
    {
        return isset($_GET[$name]) ? $_GET[$name] : $default;
    }

    public function getPost($name, $default = null)
    {
        return isset($_POST[$name]) ? $_POST[$name] : $default;
    }

    public function getRequest($name, $default = null)
    {
        return isset($_REQUEST[$name]) ? $_REQUEST[$name] : $default;
    }

    public function getCookie($name, $default = null)
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default;
    }

    public function getFiles($name, $default = null)
    {
        return (isset($_FILES[$name])) ? $_FILES[$name] : $default;
    }

    public function isXmlHttpRequest()
    {
        $str = $this->getServer('HTTP_X_REQUESTED_WITH', '');

        if (strncasecmp($str, 'XMLHttpRequest', 14) == 0) {
            return true;
        }

        return false;
    }
}
