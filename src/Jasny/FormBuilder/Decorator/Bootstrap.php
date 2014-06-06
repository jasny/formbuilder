<?php

namespace Jasny\FormBuilder\Decorator;

use Jasny\FormBuilder\Decorator;
use Jasny\FormBuilder\Node;
use Jasny\FormBuilder\Element;
use Jasny\FormBuilder\Button;
use Jasny\FormBuilder\Input;

/**
 * Render element for use with Bootstrap
 * 
 * @todo Add classes for horizontal forms
 */
class Bootstrap extends Decorator
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
     * Apply default modifications.
     * 
     * @param Node $node
     */
    public function apply($node)
    {
        if ($node instanceof Element) $node->addClass('form-control');
        
        $isButton = $node instanceof Button ||
            ($node instanceof Input && in_array($node->attr['type'], ['button', 'submit', 'reset']));
        if ($isButton && !$node->hasClass('btn')) $node->addClass('btn btn-default');
    }
    
    /**
     * Modify options.
     * 
     * @param Node  $element
     * @param array $options
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
        $error = $element instanceof Element ? $element->getError() : null;
        if ($error) $html .= "\n<span class=\"help-block error\">{$error}</span>";
        
        // Put everything in a container
        if ($element->getOption('container')) {
            $class = "form-group" . ($error ? " has-error" : "");
            $html = "<div class=\"$class\">\n{$html}\n</div>";
        }
        
        return $html;
    }
}
