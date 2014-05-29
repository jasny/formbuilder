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
     * @param array   $attr
     * @param array   $options
     * @return string
     */
    public function renderLabel($element, $html, $attr, $options)
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
     * Render the element field to HTML.
     * 
     * @param Element $element
     * @param string  $html     Original rendered html
     * @return string
     */
    public function renderField($element, $html)
    {
        return $html;
    }

    /**
     * Render the element container to HTML.
     * 
     * @param Element $element
     * @param string  $html       Original rendered html
     * @param string  $innerHtml
     * @param array   $attr
     * @param array   $options
     * @return string
     */
    public function renderContainer($element, $html, $innerHtml, $attr, $options)
    {
        return $html;
    }
}
