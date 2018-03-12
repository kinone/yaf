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

    public function __construct($tplDir, $tplVars = [])
    {
        $this->_tplDir = $tplDir;
        $this->_tplVars = $tplVars;
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

    public function assign($name, $value = null)
    {
        if (is_array($name)) {
            $this->_tplVars = array_replace($this->_tplVars, $name);
        } else {
            $this->_tplVars[$name] = $value;
        }
    }

    public function render($tpl, $tplVars = [])
    {
        $this->assign($tplVars);
        extract($this->_tplVars);
        ob_start();
        include $this->getScriptPath() . DIRECTORY_SEPARATOR . $tpl;
        return ob_get_clean();
    }

    public function dispaly($tpl, $tplVars = [])
    {
        echo $this->render($tpl, $tplVars);
    }
}
