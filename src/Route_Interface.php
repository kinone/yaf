<?php
/**
 * Description of Route_Interface.php.
 *
 * @package Kinone\Yaf
 */

namespace Kinone\Yaf;

interface Route_Interface
{
    /**
     * @param Request_Abstract $request
     * @return bool
     */
    public function route(Request_Abstract $request);
    public function assemble(array $info, array $query = []);
}
