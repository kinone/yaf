<?php
/**
 * Description of Config_Ini.php.
 *
 * @package Kinone\Yaf
 */

namespace Kinone\Yaf;

class Config_Ini extends Config_Abstract
{
    public function __construct($file, $section = null)
    {
        $this->_keys = [];

        if (is_array($file)) {
            $this->_config = $file;
            self::genIndex($file, $this->_keys);
        } else {
            if (!is_file($file)) {
                throw new Exception(sprintf('Unable to find config file \'%s\'', $file));
            }

            $config = parse_ini_file($file, true);

            if (!$config) {
                throw new Exception(sprintf('Parsing ini file \'%s\' failed', $file));
            }

            if (!$section) {
                $section = ini_get('yaf.environ');
            }

            if ($section && !isset($config[$section])) {
                throw new Exception(sprintf('There is no selection \'%s\' in \'%s\'', $section, $file));
            }

            $this->_config = $section ? $config[$section] : $config;
            $this->explodeConfig();
        }
    }

    public function get($name, $default = false)
    {
        if (!isset($this->_keys[$name])) {
            return $default;
        }

        $arr = explode('.', $name);
        $config = &$this->_config;
        foreach ($arr as $k) {
            $config = &$config[$k];
        }

        return is_array($config) ? new self($config) : $config;
    }

    public function readonly()
    {
        return $this->_readonly;
    }

    public function toArray()
    {
        return $this->_config;
    }

    /**
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->_keys[$offset]);
    }

    private function explodeConfig()
    {
        foreach ($this->_config as $key => $value) {
            $this->_keys[$key] = 1;
            $arr = explode('.', $key);
            $config = &$this->_config;
            $prev = '';
            while (count($arr) > 1) {
                $k = array_shift($arr);
                if (!isset($config[$k])) {
                    $config[$k] = [];
                }
                $config = &$config[$k];
                $prev = $prev ? implode('.', [$prev, $k]) : $k;
                $this->_keys[$prev] = 1;
            }
            $k = array_shift($arr);
            unset($this->_config[$key]);
            $config[$k] = $value;
        }
    }

    private static function genIndex($arr, array &$keys, $prev = '')
    {
        if (!is_array($arr)) {
            return;
        }

        foreach ($arr as $k => $v) {
            $index = $prev ? implode('.', [$prev, $k]) : $k;
            $keys[$index] = 1;
            self::genIndex($v, $keys, $index);
        }
    }
}
