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
        'inside-label'=>false,         // Put element inside a label
        
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
        'div' =>      ['Jasny\FormBuilder\Div'],
        'form' =>     ['Jasny\FormBuilder\Form'],
        'fieldset' => ['Jasny\FormBuilder\Fieldset'],
        'group' =>    ['Jasny\FormBuilder\Group'],
        
        'button' =>   ['Jasny\FormBuilder\Button'],
        'link' =>     ['Jasny\FormBuilder\Hyperlink'],
            
        'choice' =>   ['Jasny\FormBuilder\Choice'],
        'multi' =>    ['Jasny\FormBuilder\Choice', ['multiple'=>true]],
        'input' =>    ['Jasny\FormBuilder\Input'],
        'select' =>   ['Jasny\FormBuilder\Select'],
        'textarea' => ['Jasny\FormBuilder\Textarea'],

        'boolean' =>  ['Jasny\FormBuilder\Input', ['type'=>'checkbox']],
        'text' =>     ['Jasny\FormBuilder\Input', ['type'=>'text']],
        'hidden' =>   ['Jasny\FormBuilder\Input', ['type'=>'hidden']],
        'file' =>     ['Jasny\FormBuilder\Input', ['type'=>'file']],
        'color' =>    ['Jasny\FormBuilder\Input', ['type'=>'color']],
        'number' =>   ['Jasny\FormBuilder\Input', ['type'=>'number']],
        'decimal' =>  ['Jasny\FormBuilder\Input', ['type'=>'text'], ['pattern'=>'-?\d+(\.\d+)?']],
        'range' =>    ['Jasny\FormBuilder\Input', ['type'=>'range']],
        'date' =>     ['Jasny\FormBuilder\Input', ['type'=>'date']],
        'datetime' => ['Jasny\FormBuilder\Input', ['type'=>'datetime-local']],
        'time' =>     ['Jasny\FormBuilder\Input', ['type'=>'time']],
        'month' =>    ['Jasny\FormBuilder\Input', ['type'=>'month']],
        'week' =>     ['Jasny\FormBuilder\Input', ['type'=>'week']],
        'url' =>      ['Jasny\FormBuilder\Input', ['type'=>'url']],
        'email' =>    ['Jasny\FormBuilder\Input', ['type'=>'email']],
    ];

    /**
     * Named decorators
     * @var array
     */
    public static $decorators = [
        'tidy' =>   ['Jasny\FormBuilder\Decorator\Tidy'],
        'indent' => ['Jasny\FormBuilder\Decorator\Dindent'],
    ];
    
    /**
     * Create a form element
     * 
     * @param string $name     Element name
     * @param array  $options  Element options
     * @param array  $attr     HTML attributes
     * @return Element|Control
     */
    public static function element($name, array $options=[], array $attr=[])
    {
        if (!isset(static::$elements[$name])) throw new \InvalidArgumentException("Unknown element type '$name'.");
        
        $class = static::$elements[$name][0];
        if (isset(static::$elements[$name][1])) $options += static::$elements[$name][1];
        if (isset(static::$elements[$name][2])) $attr += static::$elements[$name][2];
        
        return new $class($options, $attr);
    }
    
    /**
     * Create a form decorator
     * 
     * @param string $name     Decorator name
     * @param array  $options  Decorator options
     * @return FormBuilder\Decorator
     */
    public static function decorator($name, array $options=[])
    {
        if (!isset(static::$decorators[$name])) throw new \InvalidArgumentException("Unknown decorator '$name'.");
        
        $class = static::$decorators[$name][0];
        if (isset(static::$decorators[$name][1])) $options += static::$decorators[$name][1];
        
        return new $class($options);
    }
}
