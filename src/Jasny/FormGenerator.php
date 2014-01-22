<?php
/**
 * Jasny Form Builder - Get full featured forms fast.
 * 
 * @author  Arnold Daniels <arnold@jasny.net>
 * @license https://raw.github.com/jasny/formbuilder/master/LICENSE MIT
 * @link    https://jasny.github.io/formbuilder
 */
/** */
namespace Jasny;

/**
 * Generate Form classes.
 * 
 * <code>
 *   $db = new mysqli();
 *   if (getenv('APPLICATION_ENV') == 'prod')
 *     set_include_path(get_include_path() . PATH_SEPARATOR . __DIR__ . '/cache/forms');
 *   Jasny\FormGenerator::enable($db, __DIR__ . '/cache/forms');
 * </code>
 */
abstract class FormGenerator
{
    /**
     * Source object
     * @var mixed
     */
    protected static $source;
    
    /**
     * Path to cache
     * @var string
     */
    protected static $cachePath;
    
    /**
     * Namespace for the Form classes
     * @var string
     */
    public static $namespace;
    
    /**
     * Namespace for base classes
     * @var string
     */
    public static $baseNamespace = 'Base';
    
    
    /**
     * Split full class in class name and namespase
     * 
     * @param string $class
     * @param string $ns     Replace namespace
     * @return array (class name, namespace, full class)
     */
    protected static function splitClass($class, $ns=null)
    {
        $parts = explode('\\', $class);
        $classname = array_pop($parts);

        if (!isset($ns)) $ns = join('\\', $parts);
        return array($classname, $ns, ($ns ? $ns . '\\' : '') . $class);
    }
    
    /**
     * See if there is a valid file in cache and include it.
     * 
     * @param string $class
     * @param string $checksum  Checksum to verify
     * @return boolean
     */
    protected static function loadFromCache($class, $checksum=null)
    {
        if (!isset(self::$cachePath)) return false;
        
        $filename = self::$cachePath . '/' . strtr($class, '\\_', '//') . '.php';
        if (!file_exists($filename)) return false;
        
        if (isset($checksum)) {
            $code = file_get_contents($filename);
            if (!strpos($code, "@checksum $checksum")) return false;
        }
        
        include $filename;
        return true;
    }
    
    /**
     * Save the generated code to cache and include it
     * 
     * @param string $class
     * @param string $code
     * @return boolean
     */
    protected static function cacheAndLoad($class, $code)
    {
        if (!isset(self::$cachePath)) return false;
        
        $filename = self::$cachePath . '/' . strtr($class, '\\_', '//') . '.php';

        if (!file_exists(dirname($filename))) mkdir(dirname($filename), 0777, true);
        if (!file_put_contents($filename, $code)) return false;
        
        include $filename;
        return true;
    }
    
    /**
     * Enable the generator.
     * 
     * @param object  $source      Source object to use
     * @param string  $cache_path  Directory to save the cache files
     */
    public static function enable($source, $cache_path=null)
    {
        static::$source = $source;
        static::$cachePath = $cache_path;

        $class = get_called_class() === __CLASS__? static::getDriver($source) : get_called_class();
        spl_autoload_register($class, 'autoload');
    }

    /**
     * Get generator class for the source.
     * 
     * @param mixed $source
     * @return string
     */
    protected function getDriver($source)
    {
        if ($source instanceof \mysqli) return __CLASS__ . '//MySQL';
        
        $type = is_object(self::$source) ? get_class(self::$source) . " object" : gettype(self::$source);
        throw new \Exception("Sorry I don't know how to query a $type");
    }
    
    
    /**
     * Automatically create classes for table gateways and records
     * 
     * @param string $class
     */
    abstract protected static function autoload($class);
}
