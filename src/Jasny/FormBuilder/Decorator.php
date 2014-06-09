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
    public function connect(Element $element)
    {
    }

    /**
     * Apply modifications.
     * 
     * @param Element $element
     */
    public function apply(Element $element)
    {
    }
    
    /**
     * Modify options.
     * 
     * @param Element $element
     * @param array   $options
     * @return array
     */
    public function applyToOptions(Element $element, $options)
    {
        return $options;
    }
    
    
    /**
     * Check if element or group is valid.
     * 
     * @param Element $element
     * @param boolean $valid
     * @return boolean
     */
    public function isValid(Element $element, $valid)
    {
        return $valid;
    }
    
    
    /**
     * Render to HTML
     * 
     * @param Element $element
     * @param string  $html     Original rendered html
     * @return string
     */
    public function render(Element $element, $html)
    {
        return $html;
    }
    
    /**
     * Render prepend HTML.
     * 
     * @param Element $element
     * @param string  $html     Original rendered html
     * @return string
     */
    public function renderPrepend(Element $element, $html)
    {
        return $html;
    }
    
    /**
     * Render append HTML.
     * 
     * @param Element $element
     * @param string  $html     Original rendered html
     * @return string
     */
    public function renderAppend(Element $element, $html)
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
    public function renderLabel(Element $element, $html)
    {
        return $html;
    }
    
    /**
     * Render the content of the element control to HTML.
     * 
     * @param Element $element
     * @param string  $html     Original rendered html
     * @return string
     */
    public function renderContent(Element $element, $html)
    {
        return $html;
    }
    
    /**
     * Render the element control to HTML.
     * 
     * @param Element $element
     * @param string  $html     Original rendered html
     * @param string  $el       HTML element
     * @return string
     */
    public function renderControl(Element $element, $html, $el)
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
    public function renderContainer(Element $element, $html, $label, $control)
    {
        return $html;
    }
}
