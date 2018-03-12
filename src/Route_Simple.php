<?php
/**
 * Description of Route_Simple.php.
 *
 * @package Kinone\Yaf
 */

namespace Kinone\Yaf;

final class Route_Simple implements Route_Interface
{
    /**
     * @var string
     */
    private $module;

    /**
     * @var string
     */
    private $controller;

    /**
     * @var string
     */
    private $action;

    public function __construct($moduleName, $controllername, $actionName)
    {
        $this->module = $moduleName;
        $this->controller = $controllername;
        $this->action = $actionName;
    }

    public function route(Request_Abstract $request)
    {
        $m = $request->getQuery($this->module);
        $c = $request->getQuery($this->controller);
        $a = $request->getQuery($this->action);

        if (!$m & !$c & !$a) {
            return false;
        }

        $request->setModuleName($m)
            ->setControllerName($c)
            ->setActionName($a)
            ->setRouted(true);

        return true;
    }

    public function assemble(array $info, array $query = [])
    {
        // TODO: Implement assemble() method.
    }
}
