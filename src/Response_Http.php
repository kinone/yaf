<?php
/**
 * Description of Response_Http.php.
 *
 * @package Kinone\Yaf
 * @author zhenhao <phpcandy@163.com>
 */

namespace Kinone\Yaf;

class Response_Http extends Response_Abstract
{
    protected $_sendHeader;

    protected $_responseCode;

    /**
     * @param string $name
     * @param string $value
     * @param bool $replace
     * @param int $responseCode
     * @return $this
     */
    public function setHeader($name, $value, $replace = true, $responseCode = 0)
    {
        if ($responseCode) {
            $this->_responseCode = $responseCode;
        }

        $this->_header[$name] = [$value, $replace];

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
        if ($this->_responseCode) {
            http_response_code($this->_responseCode);
        }

        foreach ($this->_header as $name => $val) {
            header(sprintf('%s:%s', $name, $val[0], $val[1]));
        }

        parent::response();
    }
}
