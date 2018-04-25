<?php
/**
 * Description of Registry.php.
 *
 * @package Kinone\Yaf
 * @author zhenhao <phpcandy@163.com>
 */

namespace Kinone\Yaf;

final class Registry
{
    /**
     * @var self
     */
    private static $_instance;

    /**
     * @var array
     */
    private $_entries;

    private function __construct()
    {
        $this->_entries = [];
    }

    public static function getInstance()
    {
        if (self::$_instance == null) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     * @param $name
     * @return mixed
     */
    public static function get($name)
    {
        return isset(self::$_instance[$name]) ? self::$_instance[$name] : null;
    }

    public static function set($name, $val)
    {
        self::$_instance[$name] = $val;
    }

    public static function hase($name)
    {
        return isset(self::$_instance[$name]);
    }

    public static function del($name)
    {
        unset(self::$_instance[$name]);
    }
}
