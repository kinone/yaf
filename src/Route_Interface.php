<?php
/**
 * Description of Route_Interface.php.
 *
 * @package Kinone\Yaf
 * @author zhenhao <phpcandy@163.com>
 */

namespace Kinone\Yaf;

interface Route_Interface
{
    /**
     * @param Request_Abstract $request
     * @return bool
     */
    public function route(Request_Abstract $request);

    /**
     * @param array $info
     * @param array $query
     * @return string
     */
    public function assemble(array $info, array $query = []);
}
