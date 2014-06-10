<?php
/**
 * Jasny Form Builder - Get full featured forms fast.
 * 
 * @author  Arnold Daniels <arnold@jasny.net>
 * @license https://raw.github.com/jasny/formbuilder/master/LICENSE MIT
 * @link    https://jasny.github.io/formbuilder
 */
/** */
namespace Jasny\FormGenerator;

use Jasny\FormGenerator;

/**
 * Generate FormBuilder\Form classes for MySQL tables.
 */
class MySQL extends FormGenerator
{
    /**
     * PHP type for each MySQL field type.
     * @var array
     */
    public static $types = [
        'id' => 'hidden',
        'bit' => 'number',
        'bit(1)' => 'boolean',
        'bool' => 'boolean',
        'boolean' => 'boolean',
        'tinyint(1)' => 'boolean',
        'tinyint' => 'number',
        'smallint' => 'number',
        'mediumint' => 'number',
        'int' => 'number',
        'integer' => 'number',
        'bigint' => 'number',
        'decimal' => 'decimal',
        'dec' => 'decimal',
        'numeric' => 'decimal',
        'fixed' => 'decimal',
        'float' => 'decimal',
        'double' => 'decimal',
        'double precision' => 'decimal',
        'real' => 'decimal',
        'date' => 'date',
        'datetime' => 'datetime',
        'timestamp' => 'datetime',
        'time' => 'time',
        'year' => 'number',
        'char' => 'text',
        'varchar' => 'text',
        'binary' => 'textarea',
        'varbinary' => 'textarea',
        'tinyblob' => 'textarea',
        'tinytext' => 'textarea',
        'blob ' => 'textarea',
        'text' => 'textarea',
        'mediumblob' => 'textarea',
        'mediumtext' => 'textarea',
        'longblob' => 'textarea',
        'longtext' => 'textarea',
        'enum' => 'choice',
        'set' => 'multi'
    ];
    
    /**
     * Get information about the table.
     * 
     * @param string $table
     * @return array
     */
    protected static function getInfo($table)
    {
        $fields = static::query("DESCRIBE `$table`");

        return (object)[
            'name' => $table,
            'fields' => $fields
        ];
    }
    
    /**
     * Get info about the field on building the form element
     * 
     * @param array $field
     * @return array
     */
    protected function getFieldInfo($field)
    {
        $options = ['name' => $field['Field']];
        
        if (strpos($field['Extra'], 'auto_increment') !== false) {
            $options['type'] = static::$types['id'];
        } elseif (isset(static::$types[$field['Type']])) {
            $options['type'] = static::$types[$field['Type']];
        } else {
            $dbtype = trim(substr($field['Type'], 0, strpos($field['Type'], '(')));
            $dbopt = trim(substr($field['Type'], strpos($field['Type'], '(')+1, -1));

            $options['type'] = array_key_exists($dbtype, static::$types) ? static::$types[$dbtype] : 'text';
            if ($dbtype === 'enum' || $dbtype === 'set') {
                $options['items'] = [];
                $items = explode(',', $dbopt);
                foreach ($items as $i=>$item) {
                    $item = stripcslashes(trim(trim($item), "'"));
                    $key = $dbtype === 'set' ? pow(2, $i) : $item;
                    $options['items'][$key] = $item;
                }
            } else {
                if (ctype_digit($dbopt)) $options['maxlength'] = (int)$dbopt;
            }
        }

        if ($options['type'] !== 'hidden' && $field['Null'] === 'No') $options['required'] = true;

        if (!isset($options['type'])) return null;
        return $options;
    }
    
    
    /**
     * Enable the generator.
     * 
     * @param object  $source      Source object to use
     * @param string  $cache_path  Directory to save the cache files
     * @param string  $namespace   Namespace for the Form classes
     */
    public static function enable($source, $cache_path=null, $namespace=null)
    {
        if (!$source instanceof \mysqli && !$source instanceof \PDO) {
            $type = is_object(self::$source) ? get_class(self::$source) . " object" : gettype(self::$source);
            throw new \Exception("Expected a mysqli or PDO object as source, but got a $type");
        } elseif ($source instanceof \PDO && $source->getAttribute(PDO::ATTR_DRIVER_NAME) != 'mysql') {
            throw new \Exception("PDO is connected to a " . $source->getAttribute(PDO::ATTR_DRIVER_NAME). " database");
        }
        
        parent::enable($source, $cache_path, $namespace);
    }
    
    
    /**
     * Perform an SQL query
     * 
     * @param string $sql
     * @return array
     */
    protected static function query($sql)
    {
        if (static::$source instanceof \mysqli) return static::queryMysqli($sql);
        if (static::$source instanceof \PDO) return static::queryPDO($sql);
    }

    /**
     * Query using MySQLi.
     * 
     * @param string $sql
     * @return array
     */
    protected static function queryMysqli($sql)
    {
        $result = static::$source->query($sql);
        if (!$result) throw new \Exception("Query failed: " . static::$source->error);

        // mysqlnd
        if (function_exists('mysqli_fetch_all')) return $result->fetch_all(MYSQLI_ASSOC);

        // no mysqlnd
        $rows = [];
        while ($row = $result->fetch_assoc()) $rows[] = $row;
        return $rows;
    }
    
    /**
     * Query using PDO.
     * 
     * @param string $sql
     * @return array
     */
    protected static function queryPDO($sql)
    {
        $result = static::$source->query($sql);
        if (!$result) {
            $error = static::$source->errorInfo;
            throw new \Exception("Query failed: " . $error[2]);
        }
        
        return $result->fetchAll(PDO::FETCH_ASSOC);
    }
    
    
    /**
     * Automatically create classes for table gateways and records
     * 
     * @param string $class
     */
    protected static function autoload($class)
    {
        list($classname, $ns) = static::splitClass($class);
        
        if (substr($classname, -4) !== 'Form') return;
        if (preg_replace('/(^|\\\\)' . static::$baseNamespace . '/', '', $ns) != static::$namespace) return;
        
        $name = self::uncamelcase(preg_replace('/Form$/i', '', $classname));
        if (!empty($name)) $info = static::getInfo($name);
        
        if (empty($info)) return; // Class doesn't match any table
        if (self::loadFromCache($class, self::getChecksum($info))) return;

        $code = static::generate($info, $classname, $ns);
        
        self::cacheAndLoad($class, "<?php\n" . $code) or eval($code);
    }
}
