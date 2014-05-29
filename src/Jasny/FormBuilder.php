<?php

namespace Jasny;

/**
 * FormBuilder factory
 */
class FormBuilder
{
    /**
     * Default options
     * @var array
     */
    public static $options = [
        'render' => true,              // Render element
        'basic-validation' => true,    // Server-side validation
        'validation-script' => true,   // Include <script> for validation that isn't supported by HTML5
        'add-hidden' => true,          // Add hidden input for checkbox inputs
        'required-suffix' => ' *',     // Suffix label for required controls
        
        'error:required' => "Please fill out this field",
        'error:type' => "Please enter a {{type}}",
        'error:min' => "Value must be greater or equal to {{min}}",
        'error:max' => "Value must be less or equal to {{max}}",
        'error:minlength' => "Please use {{minlength}} characters or more for this text",
        'error:maxlength' => "Please shorten this text to {{maxlength}} characters or less",
        'error:pattern' => "Please match the requested format",
        'error:same' => "Please match the value of {{other}}",
        'error:upload' => [
            UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive in php.ini.",
            UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.",
            UPLOAD_ERR_PARTIAL => "The uploaded file was only partially uploaded.",
            UPLOAD_ERR_NO_FILE => "No file was uploaded.",
            UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder.",
            UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk.",
            UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload.",
        ]
    ];

    /**
     * Named element types
     * @var array
     */
    public static $elements = [
        'form' => ['Jasny\FormBuilder\Form'],
        'fieldset' => ['Jasny\FormBuilder\Fieldset'],
        'link' => ['Jasny\FormBuilder\Link'],
        
        'choice' => ['Jasny\FormBuilder\Choice'],
        'input' => ['Jasny\FormBuilder\Input'],
        'select' => ['Jasny\FormBuilder\Select'],
        'textarea' => ['Jasny\FormBuilder\TextArea'],

        'file' => ['Jasny\FormBuilder\Input', null, null, ['type'=>'file']],
        'color' => ['Jasny\FormBuilder\Input', null, null, ['type'=>'color']],
        'number' => ['Jasny\FormBuilder\Input', null, null, ['type'=>'number']],
        'range' => ['Jasny\FormBuilder\Input', null, null, ['type'=>'range']],
        'date' => ['Jasny\FormBuilder\Input', null, null, ['type'=>'date']],
        'datetime' => ['Jasny\FormBuilder\Input', null, null, ['type'=>'datetime-local']],
        'time' => ['Jasny\FormBuilder\Input', null, null, ['type'=>'time']],
        'month' => ['Jasny\FormBuilder\Input', null, null, ['type'=>'month']],
        'week' => ['Jasny\FormBuilder\Input', null, null, ['type'=>'week']],
        'url' => ['Jasny\FormBuilder\Input', null, null, ['type'=>'url']],
        'email' => ['Jasny\FormBuilder\Input', null, null, ['type'=>'email']],
        
        'bootstrap/fileinput' => ['Jasny\FormBuilder\Bootstrap\Fileinput'],
        'bootstrap/imageinput' => ['Jasny\FormBuilder\Bootstrap\Imageinput'],
    ];

    /**
     * Named decorators
     * @var array
     */
    public static $decorators = [
        'tidy' => 'Jasny\FormBuilder\Tidy',
        'bootstrap' => 'Jasny\FormBuilder\Bootstrap\Decorator'
    ];
    
    
    /**
     * General factory method
     * 
     * @param string $type       'node' or 'decorator'
     * @param array  $arguments  Arguments passes to the factory method
     * @return FormBuilder\Element|FormBuilder\Decorator
     */
    public static function build($type, $arguments)
    {
        return call_user_func_array([get_called_class(), $type], $arguments);
    }
    
    
    /**
     * Create a form node
     * 
     * @param string $name
     * @param mixed  $...   Additional arguments are passed to the constructor
     * @return FormBuilder\Node
     */
    public static function node($name)
    {
        if (!isset(self::$elements[$name])) throw new \Exception("Unable to build a $name node.");
        
        $defaults = self::$elements[$name];
        
        $class = array_shift($defaults);
        $args = self::mergeArguments($defaults, array_slice(func_get_args(), 1));
        
        $refl = new \ReflectionClass($class);
        return $refl->newInstanceArgs($args);
    }
    
    /**
     * Create a form decorator
     * 
     * @param string $name
     * @param mixed  $...   Additional arguments are passed to the constructor
     * @return FormBuilder\Decorator
     */
    public static function decorator($name)
    {
        if (!isset(self::$elements[$name])) throw new \Exception("Unable to build a $name decorator.");
        
        $class = self::$elements[$name];
        $args = array_slice(func_get_args(), 1);
        
        $refl = new \ReflectionClass($class);
        return $refl->newInstanceArgs($args);
    }
    
    
    /**
     * Merge function arguments
     * 
     * @param array $defaults
     * @param array $args
     * @return array
     */
    protected static function mergeArguments($defaults, $args)
    {
        for ($i=0; $i < count($defaults); $i++) {
            if (!isset($args[$i])) {
                $args[$i] = $defaults[$i];
            } elseif (is_array($defaults[$i]) || $defaults[$i] instanceof \stdClass) {
                if (is_array($args[$i])) {
                    $args[$i] = array_merge((array)$defaults[$i], $args[$i]);
                } elseif ($args[$i] instanceof \stdClass) {
                    $args[$i] = (object)array_merge((array)$defaults[$i], (array)$args[$i]);
                }
            }
        }
        
        return $args;
    }
}
