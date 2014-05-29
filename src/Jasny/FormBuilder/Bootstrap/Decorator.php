<?php

namespace Jasny\FormBuilder\Bootstrap;

use Jasny\FormBuilder\Decorator as Base;

/**
 * Render element for use with Bootstrap
 * 
 * @todo Add classes for horizontal forms
 */
class Decorator extends Base
{
    /**
     * Class constructor
     * 
     * @param int $version  Which major version of Boostrap is used
     */
    public function __construct($version)
    {
        if ($version != 3) throw new \Exception("Only Boostrap version 3 is supported");
    }
    
    /**
     * Wether or not to apply the decorator to all descendants.
     * 
     * @return boolean
     */
    public function isDeep()
    {
        return true;
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
        if (!isset($attr['class'])) {
            $attr['class'] = "form-control";
        } elseif (!in_array('form-control', explode(' ', $attr['class']))) {
            $attr['class'] . " form-control";
        }
        
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
        if ($element instanceof Form) {
            $form_inline = $element->hasClass('form-inline') || $element->hasClass('form-search');

            return $options + [
                'container' => !$form_inline,
                'label' => !$form_inline
            ];
        }
        
        return $options;
    }
    
    
    /**
     * Render a label bound to the control.
     * 
     * @param Element $element
     * @param string  $html     Original rendered html
     * @return string
     */
    public function renderLabel($element, $html)
    {
        $class = $element->getOption('container') ? ' class="control-label"' : '';
        
        return "<label{$class} for=\"" . $element->getId() . "\">"
            . $element->getDescription()
            . ($element->getAttr('required') ? $element->getOption('required-suffix') : '') . "\n"
            . "</label>\n";
    }
    
    /**
     * Render the element container to HTML.
     * 
     * @param Element $element
     * @param string  $html     Original rendered html
     * @param string  $label    HTML of the label
     * @param string  $field    HTML of the control
     * @return string
     */
    public function renderContainer($element, $html, $label, $field)
    {
        $html = ($label ? $label . "\n" : '') . $field;
        
        // Add error
        $error = $element->getError();
        if ($error) $html .= "\n<span class=\"help-block error\">{$error}</span>";
        
        // Put everything in a container
        if ($element->getOption('container')) {
            $class = "form-group" . ($error ? " has-error" : "");
            $html = "<div class=\"$class\">\n{$html}\n</div>";
        }
        
        return $html;
    }
}
