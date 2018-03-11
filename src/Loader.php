<?php
/**
 * Description of Loader.php.
 *
 * @package Kinone\Yaf
 */

namespace Kinone\Yaf;

final class Loader
{
    private $localLibrary;
    private $globalLibrary;
    private $localNamespace = [];

    private static $ins;

    private function __construct($localLibrary, $gloablLibaray)
    {
        if (!$localLibrary) {
            $localLibrary = Application::app()->getAppDirectory() . '/library';
        }

        if (!$gloablLibaray) {
            $gloablLibaray = $localLibrary;
        }

        $this->localLibrary = $localLibrary;
        $this->globalLibrary = $gloablLibaray;
    }

    public static function autoload($name)
    {
        $appDirectory = Application::app()->getAppDirectory();
        if ($name == 'Bootstrap') {
            $file = $appDirectory . '/Bootstrap.php';
        } else if (substr($name, -6) === 'Plugin') {
            $file = $appDirectory . '/plugins/' . substr($name, 0, -6) . '.php';
        } else if (substr($name, -10) === 'Controller') {
            $file = $appDirectory . '/controllers/' . substr($name, 0, -10) . '.php';
        } else if (substr($name, -5) === 'Model') {
            $file = $appDirectory . '/models/' . substr($name, 0, -5) . '.php';
        } else {
            return false;
        }

        self::import($file);
        return true;
    }

    public static function getInstance($localLibrary, $globalLibrary = null)
    {
        if (null == self::$ins) {
            self::$ins = new self($localLibrary, $globalLibrary);
            spl_autoload_register([self::class, 'autoload'], false, true);
        }

        return self::$ins;
    }

    public function registerLocalNamespace($prefix)
    {
        $prefix = (array)$prefix;
        $this->localNamespace = array_merge($this->localNamespace, $prefix);
    }

    public function getLocalNamespace()
    {
        return $this->localNamespace;
    }

    public function clearLocalNameapce()
    {
        $this->localNamespace = [];
    }

    public function isLocalName($name)
    {

    }

    public function setLibraryPath($path, $global = false)
    {
        if ($global) {
            $this->globalLibrary = $path;
        } else {
            $this->localLibrary = $path;
        }

        return $this;
    }

    public function getLibraryPath($global = false)
    {
        if ($global) {
            return $this->globalLibrary;
        }

        return $this->localLibrary;
    }

    public static function import($file)
    {
        if (is_file($file)) {
            include $file;
            return true;
        }

        return false;
    }
}
