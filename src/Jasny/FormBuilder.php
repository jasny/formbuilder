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
    static public $options = [
        'render' => true,              // Render element
        'validation' => true,          // Server-side validation enabled
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
    static public $elements = [
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
     * Named element types, used by the build() method
     * @var array
     */
    static public $decorators = [
        'tidy' => ['Jasny\FormBuilder\Tidy'],
        'bootstrap' => ['Jasny\FormBuilder\Bootstrap\Decorator']
    ];
    
    
    /**
     * Create a form element
     * 
     * @param string $element
     * @param Additional arguments are passed to the constructor
     * @return FormBuilder\Element
     */
    public static function element($element)
    {
        if (!isset(self::$elements[$element])) throw new \Exception("Unable to build a $element.");
        
        $defaults = self::$elements[$element];
        
        $class = array_shift($defaults);
        $args = self::mergeArguments($defaults, array_slice(func_get_args(), 1));
        
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
            } elseif (self::isPair($args[$i]) && self::isPair($defaults[$i])) {
                $is_object = is_object($args[$i]);
                $args[$i] = array_merge($defaults, $args[$i]);
                
                if ($is_object) $args[$i] = (object)$args[$i];
            }
        }
    }
    
    /**
     * Check if variable is an array or object
     * 
     * @param mixed $var
     * @return boolean
     */
    protected static function isPair($var)
    {
        return is_array($var) || $var instanceof stdClass;
    }
}
