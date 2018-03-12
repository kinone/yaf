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

    /**
     * @var self
     */
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
        $name = str_replace(['_', '\\'], DIRECTORY_SEPARATOR, $name);

        $appDirectory = Application::app()->getAppDirectory();
        if ($name == 'Bootstrap') {
            $file = $appDirectory . '/Bootstrap.php';
        } else if (substr($name, -6) === 'Plugin') {
            $filename = substr($name, 0, -6);
            $file = implode(DIRECTORY_SEPARATOR, [$appDirectory, 'plugins', $filename . '.php']);
        } else if (substr($name, -10) === 'Controller') {
            $filename = substr($name, 0, -10);
            $file = implode(DIRECTORY_SEPARATOR, [$appDirectory, 'controllers', $filename . '.php']);
        } else if (substr($name, -5) === 'Model') {
            $filename = substr($name, 0, -5);
            $file = implode(DIRECTORY_SEPARATOR, [$appDirectory, 'models', $filename . '.php']);
        } else {
            if (self::$ins->isLocalName($name)) {
                $file = implode(DIRECTORY_SEPARATOR, [self::$ins->getLibraryPath(), $name . '.php']);
            } else {
                $file = implode(DIRECTORY_SEPARATOR, [self::$ins->getLibraryPath(true), $name . '.php']);
            }
        }

        self::import($file);
        return true;
    }

    public static function getInstance($localLibrary, $globalLibrary = null)
    {
        if (null == self::$ins) {
            self::$ins = new self($localLibrary, $globalLibrary);
            spl_autoload_register([self::class, 'autoload'], false);
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
        foreach ($this->localNamespace as $prefix) {
            $l = strlen($prefix);
            if (substr($name, 0, $l) === $prefix && isset($name[$l+1]) && $name[$l+1] === '/') {
                return true;
            }
        }

        return false;
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
