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

    public function get($name)
    {

    }

    public function set($name, $val)
    {

    }

    public function hase($name)
    {

    }

    public function del($name)
    {

    }
}
