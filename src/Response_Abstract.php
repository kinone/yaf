<?php
/**
 * Description of Response_Abstract.php.
 *
 * @package Kinone\Yaf
 */

namespace Kinone\Yaf;

abstract class Response_Abstract
{
    const DEFAULT_BODY = 'content';

    protected $_header = [];
    protected $_body = [];

    public function __construct()
    {
    }

    /**
     * @param string $body
     * @param string $name
     * @return Response_Abstract
     */
    public function setBody($body, $name = self::DEFAULT_BODY)
    {
        $this->_body[$name] = $body;

        return $this;
    }

    /**
     * @param string $name
     * @return string
     */
    public function getBody($name = self::DEFAULT_BODY)
    {
        return $this->_body[$name];
    }

    /**
     * @param string $body
     * @param string $name
     * @return Response_Abstract
     */
    public function prependBody($body, $name = self::DEFAULT_BODY)
    {
        if (!isset($this->_body[$name])) {
            $this->setBody($body, $name);
        } else {
            $this->_body[$name] = $body . $this->_body[$name];
        }

        return $this;
    }

    /**
     * @param string $body
     * @param string $name
     * @return Response_Abstract
     */
    public function appendBody($body, $name = self::DEFAULT_BODY)
    {
        if (!isset($this->_body[$name])) {
            $this->setBody($body, $name);
        } else {
            $this->_body[$name] .= $body;
        }

        return $this;
    }

    /**
     * @param string $name
     * @return Response_Abstract
     */
    public function clearBody($name = null)
    {
        if ($name) {
            unset($this->_body[$name]);
        } else {
            $this->_body = [];
        }

        return $this;
    }

    public function response()
    {
        echo $this;
        return true;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return implode('', $this->_body);
    }
}
