<?php

namespace Jasny\FormBuilder;

/**
 * Decorator base class
 */
abstract class Decorator
{
    /**
     * Wether or not to apply the decorator to all descendants.
     * 
     * @return boolean
     */
    abstract public function isDeep();
    
    
    /**
     * Called when decorator is added to element
     * 
     * @param Element $element
     */
    public function connect($element)
    {
    }

    /**
     * Apply default modifications.
     * 
     * @param Element $element
     */
    public function apply($element)
    {
    }
    
    /**
     * Modify options.
     * 
     * @param Element  $element
     * @param array $options
     * @return array
     */
    public function applyToOptions($element, $options)
    {
        return $options;
    }
    
    
    /**
     * Check if element or group is valid.
     * 
     * @param Element    $element
     * @param boolean $valid
     * @return boolean
     */
    public function isValid($element, $valid)
    {
        return $valid;
    }
    
    
    /**
     * Render to HTML
     * 
     * @param Element   $element
     * @param string $html  Original rendered html
     * @return string
     */
    public function render($element, $html)
    {
        return $html;
    }
    
    /**
     * Render a label bound to the element.
     * 
     * @param FormElement $element
     * @param string  $html     Original rendered html
     * @return string
     */
    public function renderLabel($element, $html)
    {
        return $html;
    }
    
    /**
     * Render the element field to HTML.
     * 
     * @param FormElement $element
     * @param string  $html     Original rendered html
     * @param string  $control  HTML of the control
     * @return string
     */
    public function renderField($element, $html, $control)
    {
        return $html;
    }

    /**
     * Render the element control to HTML.
     * 
     * @param FormElement|Button $element
     * @param string         $html     Original rendered html
     * @return string
     */
    public function renderControl($element, $html)
    {
        return $html;
    }
    
    /**
     * Render the element container to HTML.
     * 
     * @param FormElement|Button $element
     * @param string         $html     Original rendered html
     * @param string         $label    HTML of the label
     * @param string         $control  HTML of the control
     * @return string
     */
    public function renderContainer($element, $html, $label, $control)
    {
        return $html;
    }
}
