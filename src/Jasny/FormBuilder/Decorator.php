<?php

namespace Jasny\FormBuilder;

/**
 * Decorator base class
 */
abstract class Decorator
{
    /**
     * Wether or not to indent each individual child node.
     * @var boolean
     */
    protected $deep = false;
    
    /**
     * Wether or not to apply the decorator to all descendants.
     * 
     * @return boolean
     */
    public function isDeep()
    {
        return $this->deep;
    }
    
    
    /**
     * Apply modifications.
     * 
     * @param Element $element
     * @param boolean $deep     The decorator of a parent is applied to a child
     */
    public function apply(Element $element, $deep)
    {
    }
    
    /**
     * Validate the element
     * 
     * @param Element $element
     * @param boolean $valid    Result of FormBuilder validation
     * @return boolean
     */
    public function validate(Element $element, $valid)
    {
        return $valid;
    }
    
    /**
     * Modify the value
     * 
     * @param Element $element
     * @param mixed   $value
     * @return mixed
     */
    public function filter(Element $element, $value)
    {
        return $value;
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
     * Render the element content to HTML
     * 
     * @param Element $element
     * @param string  $html     Original rendered html
     * @return string
     */
    public function renderContent(Element $element, $html)
    {
        return $html;
    }
}
