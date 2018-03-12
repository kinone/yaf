<?php
/**
 * Description of View_Simple.php.
 *
 * @package Kinone\Yaf
 * @author zhenhao <phpcandy@163.com>
 */

namespace Kinone\Yaf;

class View_Simple implements View_Interface
{
    private $_tplDir;
    private $_tplVars;
    private $_options;

    public function __construct($tplDir, $options = [])
    {
        $this->_tplDir = $tplDir;
        $this->_tplVars = $options;
    }

    public function get($name)
    {
        return isset($this->_tplVars[$name]) ? $this->_tplVars : null;
    }

    public function setScriptPath($path)
    {
        $this->_tplDir = $path;

        return $this;
    }

    public function getScriptPath()
    {
        return $this->_tplDir;
    }

    public function clear($name = null)
    {
        if (null === $name) {
            $this->_tplVars = [];
        } else {
            unset($this->_tplVars[$name]);
        }

        return $this;
    }

    public function assign($name, $value = null)
    {
        if (is_array($name)) {
            $this->_tplVars = array_replace($this->_tplVars, $name);
        } else {
            $this->_tplVars[$name] = $value;
        }

        return $this;
    }

    /**
     * @param $string
     * @param array $vars
     * @return string
     * @throws Exception
     */
    public function renderString($string, $vars = [])
    {
        $file = tempnam(sys_get_temp_dir(), 'kinone_tpl_');
        file_put_contents($file, $string);
        $ret = $this->render($file, $vars);
        unlink($file);

        return $ret;
    }

    /**
     * @param string $tpl
     * @param array $tplVars
     * @return string
     * @throws Exception_LoadFailed_View
     */
    public function render($tpl, $tplVars = [])
    {
        $vars = array_replace($this->_tplVars, $tplVars);
        extract($vars);
        ob_start();

        if ($tpl[0] !== DIRECTORY_SEPARATOR) {
            $tpl = $this->getScriptPath() . DIRECTORY_SEPARATOR . $tpl;
        }
        $realpath = realpath($tpl);
        if (!$realpath) {
            throw new Exception_LoadFailed_View(sprintf('Faild opening template %s: no such file or directory', $tpl));
        }

        include $realpath;
        return ob_get_clean();
    }

    /**
     * @param string $tpl
     * @param array $tplVars
     * @throws Exception_LoadFailed_View
     */
    public function dispaly($tpl, $tplVars = [])
    {
        echo $this->render($tpl, $tplVars);
    }

    public function __get($name)
    {
        return $this->get($name);
    }

    public function __set($name, $value)
    {
        return $this->assign($name, $value);
    }
}
