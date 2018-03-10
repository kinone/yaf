<?php
/**
 * Description of Response_Http.php.
 *
 * @package Kinone\Yaf
 */

namespace Kinone\Yaf;

class Response_Http extends Response_Abstract
{
    protected $_sendHeader;

    protected $_responseCode;

    public function setHeader($name, $value, $rep, $responseCode)
    {
        return $this;
    }

    public function setAllHeaders(array $headers)
    {
        $this->_header = $headers;

        return $this;
    }

    public function getHeader($name)
    {
        return isset($this->_header[$name]) ? $this->_header[$name] : null;
    }

    public function clearHeaders()
    {
        $this->_header = [];

        return $this;
    }

    public function response()
    {
        parent::response();
    }
}
