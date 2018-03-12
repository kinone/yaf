<?php
/**
 * Description of Config_Simple.php.
 *
 * @package Kinone\Yaf
 * @author zhenhao <phpcandy@163.com>
 */

namespace Kinone\Yaf;

class Config_Simple extends Config_Abstract
{
    public function get($name, $default = null)
    {
        if (!isset($this->_config[$name])) {
            return $default;
        }

        $config = $this->_config[$name];
        if (is_array($this->_config[$name])) {
            $config = new self($this->_config[$name]);
        }
        return $config;
    }

    public function readonly()
    {
        return $this->_readonly;
    }

    public function toArray()
    {
        return $this->_config;
    }
}
