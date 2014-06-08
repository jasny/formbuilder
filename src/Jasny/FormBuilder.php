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
        'container' => true,           // Place each form element in a container
        'label' => true,               // Add a label for each form element
        'checkbox-hidden' => true,     // Add a hidden for a checkbox to always send a value
        
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
            UPLOAD_ERR_FORM_SIZE =>
                "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.",
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
        'button' => ['Jasny\FormBuilder\Button'],
        
        'choice' => ['Jasny\FormBuilder\Choice'],
        'multi' => ['Jasny\FormBuilder\Choice', 'attr' => ['multiple'=>true]],
        'input' => ['Jasny\FormBuilder\Input'],
        'select' => ['Jasny\FormBuilder\Select'],
        'textarea' => ['Jasny\FormBuilder\Textarea'],

        'text' => ['Jasny\FormBuilder\Input', 'attr' => ['type'=>'text']],
        'hidden' => ['Jasny\FormBuilder\Input', 'attr' => ['type'=>'hidden']],
        'file' => ['Jasny\FormBuilder\Input', 'attr' => ['type'=>'file']],
        'color' => ['Jasny\FormBuilder\Input', 'attr' => ['type'=>'color']],
        'number' => ['Jasny\FormBuilder\Input', 'attr' => ['type'=>'number']],
        'decimal' => ['Jasny\FormBuilder\Input', 'attr' => ['type'=>'text', 'pattern'=>'-?\d+(\.\d+)?']],
        'range' => ['Jasny\FormBuilder\Input', 'attr' => ['type'=>'range']],
        'date' => ['Jasny\FormBuilder\Input', 'attr' => ['type'=>'date']],
        'datetime' => ['Jasny\FormBuilder\Input', 'attr' => ['type'=>'datetime-local']],
        'time' => ['Jasny\FormBuilder\Input', 'attr' => ['type'=>'time']],
        'month' => ['Jasny\FormBuilder\Input', 'attr' => ['type'=>'month']],
        'week' => ['Jasny\FormBuilder\Input', 'attr' => ['type'=>'week']],
        'url' => ['Jasny\FormBuilder\Input', 'attr' => ['type'=>'url']],
        'email' => ['Jasny\FormBuilder\Input', 'attr' => ['type'=>'email']],
        
        'bootstrap/fileinput' => ['Jasny\FormBuilder\Bootstrap\Fileinput'],
        'bootstrap/imageinput' => ['Jasny\FormBuilder\Bootstrap\Imageinput'],
    ];

    /**
     * Named decorators
     * @var array
     */
    public static $decorators = [
        'tidy' => ['Jasny\FormBuilder\Decorator\Tidy'],
        'indent' => ['Jasny\FormBuilder\Decorator\Dindent'],
        'bootstrap' => ['Jasny\FormBuilder\Decorator\Bootstrap'],
    ];
    
    
    /**
     * Build an object using named arguments
     * 
     * @param string $class
     * @param array  $args
     * @param array  $defaults  Always named
     * @return object
     */
    protected static function constructWithNamedArgs($class, array $args, array $defaults=[])
    {
        $refl = new \ReflectionClass($class);
        $params = $refl->getMethod('__construct')->getParameters();
        
        $construct = []; // arguments for constructor
        
        foreach ($params as $param) {
            $pn = $param->getName();
            $pp = $param->getPosition();
            
            $value = isset($args[$pn]) ? $args[$pn] : (isset($args[$pp]) ? $args[$pp] : null);
            
            if (!isset($value)) {
                $construct[$pp] = isset($defaults[$pn]) ? $defaults[$pn] : $param->getDefaultValue();
            } elseif (isset($defaults[$pn]) && is_array($defaults[$pn]) && is_array($value)) {
                $construct[$pp] = $value + $defaults[$pn];
            } else {
                $construct[$pp] = $value;
            }
        }
        
        return $refl->newInstanceArgs($construct);
    }
    
    
    /**
     * General factory method
     * 
     * @param string $type  'element' or 'decorator'
     * @param string $name  Element / Decorator name
     * @param array  $args  Arguments passed to the constructor
     * @return FormBuilder\Element|FormBuilder\Decorator
     */
    protected static function build($type, $name, $args=null)
    {
        switch ($type) {
            case 'element': $items = static::$elements; break;
            case 'decorator': $items = static::$decorators; break;
            default: throw new \InvalidArgumentException("Type should be 'element' or 'decorator', not '$type'.");
        }
        
        if (!isset($items[$name])) throw new \InvalidArgumentException("Unknown $type type '$name'.");
        
        $defaults = $items[$name];
        $class = array_shift($defaults);
        
        return static::constructWithNamedArgs($class, $args, $defaults);
    }
    
    /**
     * Create a form element
     * 
     * @param string $name  Element name
     * @param array  $args  Arguments passed to the constructor
     * @return FormBuilder\Element
     */
    public static function buildElement($name, $args=null)
    {
        return static::build('element', $name, $args);
    }
    
    /**
     * Create a form decorator
     * 
     * @param string $name  Decorator name
     * @param array  $args  Arguments passed to the constructor
     * @return FormBuilder\Decorator
     */
    public static function buildDecorator($name, $args=null)
    {
        return static::build('decorator', $name, $args);
    }
}
