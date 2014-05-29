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
     * Modify attributes.
     * 
     * @param Element $element
     * @param array   $attr
     * @return array
     */
    public function applyToAttr($element, $attr)
    {
        return $attr;
    }
    
    /**
     * Modify options.
     * 
     * @param Element $element
     * @param array   $options
     * @return array
     */
    public function applyToOptions($element, $options)
    {
        return $options;
    }
    
    
    /**
     * Check if element or group is valid.
     * 
     * @param Node    $node
     * @param boolean $valid
     * @return boolean
     */
    public function isValid($node, $valid)
    {
        return $valid;
    }
    
    
    /**
     * Render to HTML
     * 
     * @param Node   $node
     * @param string $html  Original rendered html
     * @return string
     */
    public function render($node, $html)
    {
        return $html;
    }
    
    /**
     * Render a label bound to the element.
     * 
     * @param Element $element
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
     * @param Element $element
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
     * @param Element $element
     * @param string  $html     Original rendered html
     * @return string
     */
    public function renderControl($element, $html)
    {
        return $html;
    }
    
    /**
     * Render the element container to HTML.
     * 
     * @param Element $element
     * @param string  $html     Original rendered html
     * @param string  $label    HTML of the label
     * @param string  $control  HTML of the control
     * @return string
     */
    public function renderContainer($element, $html, $label, $control)
    {
        return $html;
    }
}
