<?php
/**
 * Description of Route_Supervar.php.
 *
 * @package Kinone\Yaf
 */

namespace Kinone\Yaf;

final class Route_Supervar implements Route_Interface
{
    /**
     * @var string
     */
    private $varname;

    public function __construct($varname)
    {
        $this->varname = $varname;
    }

    public function route(Request_Abstract $request)
    {
        $var = $request->getQuery($this->varname);
        if (!$var) {
            return false;
        }

        $arr = explode('/', trim($var, '/'));
        $module = array_shift($arr);
        $controller = array_shift($arr);
        $action = array_shift($arr);

        $request->setModuleName($module)
            ->setControllerName($controller)
            ->setActionName($action)
            ->setRouted(true);

        return true;
    }

    public function assemble(array $info, array $query = [])
    {
        // TODO: Implement assemble() method.
    }
}
