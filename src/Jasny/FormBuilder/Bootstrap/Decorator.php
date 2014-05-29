<?php

namespace Jasny\FormBuilder\Bootstrap;

use Jasny\FormBuilder as Base;

/**
 * Render element for use with Bootstrap
 */
class Decorator extends Base\Decorator
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
     * @param array   $attr
     * @param array   $options
     * @return string
     */
    public function renderLabel($element, $html, $attr, $options)
    {
        if (empty($options['label']) || $options['label'] === 'contain') return null;
        
        $class = ($options['container'] ? ' class="control-label"' : '');
        
        return "<label{$class} for=\"" . $element->getId() . "\">"
            . $element->getDescription()
            . (!empty($attr['required']) ? $options['required-suffix'] : '') . "\n"
            . "</label>\n";
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
     * Render the element to inner HTML.
     * 
     * @param Element $element
     * @param string  $html     Original rendered html
     * @return string
     */
    public function renderInner($element, $html)
    {
        return $html;
    }

    /**
     * Render the control container to HTML.
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
        $html = $innerHtml;
        $error = $element->getError();
        
        if ($options['container']) $html = "<div class=\"controls\">\n{$html}\n</div>";

        $label = $element->renderLabel($attr, $options);
        if ($label) $html = $label . "\n" . $html;

        if ($error) $html .= "\n<div class=\"help-block\">{$error}</div>";
        if (!empty($options['help'])) $html .= "\n<small class=\"help-block\">{$options['help-block']}</small>";
        
        // Build HTML
        if ($options['container']) {
            $html = "<div class=\"control-group" . ($error ? " error" : '') . "\">\n{$html}\n</div>";
        } elseif ($error) {
            $html = "<span class=\"control-group error\">\n{$html}\n</span>";
        }
        
        return $html;
    }
}
