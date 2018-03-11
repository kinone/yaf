<?php
/**
 * Description of View_Interface.php.
 *
 * @package Kinone\Yaf
 */

namespace Kinone\Yaf;

interface View_Interface
{
    /**
     * @param string $path
     */
    public function setScriptPath($path);

    /**
     * @return string
     */
    public function getScriptPath();

    /**
     * @param string|array $name
     * @param mixed $value
     */
    public function assign($name, $value = null);

    /**
     * @param string $tpl
     * @param array $tplVars
     * @return string
     */
    public function render($tpl, $tplVars = []);

    /**
     * @param string $tpl
     * @param array $tplVars
     * @return void
     */
    public function dispaly($tpl, $tplVars = []);
}