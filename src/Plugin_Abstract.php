<?php
/**
 * Description of Plugin_Abstract.php.
 *
 * @package Kinone\Yaf
 * @author zhenhao <phpcandy@163.com>
 */

namespace Kinone\Yaf;

abstract class Plugin_Abstract
{
    abstract public function routerStartup(Request_Abstract $request, Response_Abstract $response);
    abstract public function routerShutdown(Request_Abstract $request, Response_Abstract $response);
    abstract public function dispatchLoopStartup(Request_Abstract $request, Response_Abstract $response);
    abstract public function preDispatch(Request_Abstract $request, Response_Abstract $response);
    abstract public function postDispatch(Request_Abstract $request, Response_Abstract $response);
    abstract public function dispatchLoopShutdown(Request_Abstract $request, Response_Abstract $response);
}
