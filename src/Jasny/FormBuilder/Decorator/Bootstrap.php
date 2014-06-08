<?php

namespace Jasny\FormBuilder\Decorator;

use Jasny\FormBuilder\Decorator;
use Jasny\FormBuilder\Element;
use Jasny\FormBuilder\FormElement;
use Jasny\FormBuilder\Button;
use Jasny\FormBuilder\Input;
use Jasny\FormBuilder\Choice;

/**
 * Render element for use with Bootstrap.
 * Optionaly use features from Jasny Bootstrap.
 * 
 * @link http://getbootstrap.com
 * @link http://jasny.github.io/bootstrap
 * 
 * @option int version  Which major Bootstrap version is used
 * 
 * @todo Add classes for horizontal forms
 */
class Bootstrap extends Decorator
{
    /**
     * Class constructor
     * 
     * @param array $options
     */
    public function __construct(array $options=[])
    {
        if (!isset($options['version'])) {
            trigger_error("You should specify which version of Bootstrap is used.", E_USER_WARNING);
        } else if ((int)$options['version'] !== 3) {
            throw new \Exception("Only Boostrap version 3 is supported");
        }
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
     * @param Element $element
     */
    public function apply($element)
    {
        if ($element instanceof FormElement && !$element instanceof Choice) {
            $element->addClass('form-control');
        }
        
        $isButton = $element instanceof Button ||
            ($element instanceof Input && in_array($element->attr['type'], ['button', 'submit', 'reset']));
        if ($isButton && !$element->hasClass('btn')) $element->addClass('btn btn-default');
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
     * @param FormElement $element
     * @param string  $html     Original rendered html
     * @return string
     */
    public function renderLabel($element, $html)
    {
        if ($element instanceof Input && $element->attr['type'] === 'hidden') return $html;
        
        $class = $element->getOption('container') ? ' class="control-label"' : '';
        
        return "<label{$class} for=\"" . $element->getId() . "\">"
            . $element->getDescription()
            . ($element->getAttr('required') ? $element->getOption('required-suffix') : '')
            . "</label>";
    }
    
    /**
     * Render the element container to HTML.
     * 
     * @param FormElement $element
     * @param string  $html     Original rendered html
     * @param string  $label    HTML of the label
     * @param string  $field    HTML of the control
     * @return string
     */
    public function renderContainer($element, $html, $label, $field)
    {
        if ($element instanceof Input && $element->attr['type'] === 'hidden') return $html;
        
        $html = ($label ? $label . "\n" : '') . $field;
        
        // Add error
        $error = $element instanceof FormElement ? $element->getError() : null;
        if ($error) $html .= "\n<span class=\"help-block error\">{$error}</span>";
        
        // Put everything in a container
        if ($element->getOption('container')) {
            $class = "form-group" . ($error ? " has-error" : "");
            $html = "<div class=\"$class\">\n{$html}\n</div>";
        }
        
        return $html;
    }
}
