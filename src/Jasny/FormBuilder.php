<?php

namespace Jasny;

use Jasny\FormBuilder\Element;
use Jasny\FormBuilder\Group;

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
        'validate' => true,            // Server-side validation
        'validation-script' => true,   // Include <script> for validation that isn't supported by HTML5
        'add-hidden' => true,          // Add hidden input for checkbox inputs
        'required-suffix' => ' *',     // Suffix label for required controls
        'container' => 'div',          // Place each form element in a container
        'label' => true,               // Add a label for each form element
        
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
     * Element types
     * @var array
     */
    public static $elements = [
        'div' =>      ['Jasny\FormBuilder\Div', ['tagname' => 'div']],
        'form' =>     ['Jasny\FormBuilder\Form'],
        'fieldset' => ['Jasny\FormBuilder\Fieldset'],
        'group' =>    ['Jasny\FormBuilder\Group'],
        
        'span' =>     ['Jasny\FormBuilder\Span'],
        'label' =>    ['Jasny\FormBuilder\Label'],
        'legend' =>   ['Jasny\FormBuilder\Legend'],
        
        'button' =>   ['Jasny\FormBuilder\Button'],
        'link' =>     ['Jasny\FormBuilder\Hyperlink'],
        
        'choice' =>   ['Jasny\FormBuilder\ChoiceList'],
        'multi' =>    ['Jasny\FormBuilder\ChoiceList', ['multiple'=>true]],
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
     * Decorator types
     * @var array
     */
    public static $decorators = [
        'filter' =>     'Jasny\FormBuilder\Decorator\SimpleFilter',
        'validation' => 'Jasny\FormBuilder\Decorator\SimpleValidation',
        
        'tidy' =>   'Jasny\FormBuilder\Decorator\Tidy',
        'indent' => 'Jasny\FormBuilder\Decorator\Dindent',
    ];
    
    /**
     * Create a form element
     * 
     * @param string $type     Element type
     * @param array  $options  Element options
     * @param array  $attr     HTML attributes
     * @return Element|Control
     */
    public static function element($type, array $options=[], array $attr=[])
    {
        if (!isset(static::$elements[$type])) throw new \InvalidArgumentException("Unknown element type '$type'.");
        
        $class = static::$elements[$type][0];
        if (isset(static::$elements[$type][1])) $options += static::$elements[$type][1];
        if (isset(static::$elements[$type][2])) $attr += static::$elements[$type][2];
        
        return new $class($options, $attr);
    }
    
    /**
     * Create a form decorator
     * 
     * @param string $type  Decorator type
     * @param mixed  $_     Additional arguments are passed to the constructor
     * @return FormBuilder\Decorator
     */
    public static function decorator($type)
    {
        if (!isset(static::$decorators[$type])) throw new \InvalidArgumentException("Unknown decorator '$type'.");
        
        $class = static::$decorators[$type];
        $args = array_slice(func_get_args(), 1);
        
        $refl = new \ReflectionClass($class);
        return $refl->newInstanceArgs($args);
    }
    
    
    /**
     * Convert an element to another type.
     * 
     * @param Element $element
     * @param string  $type
     * @return Element
     */
    public static function convert($element, $type)
    {
        $options = $element->getOptions();
        $attr = $element->attr;
        
        $new = $element->build($type, $options);
        
        if ($element instanceof Group && $new instanceof Group) {
            $children = $element->getChildren();
            foreach ($children as $child) {
                $new->add($child);
            }
        }
        
        foreach ($element->getDecorators() as $decorator) {
            $new->addDecorator($decorator);
        }
        
        return $new;
    }
}
