<?php

namespace Jasny\FormBuilder\Decorator;

use Jasny\FormBuilder\Decorator;
use Jasny\FormBuilder\Element;
use Jasny\FormBuilder as FB;

/**
 * Render element for use with Bootstrap.
 * Optionaly use features from Jasny Bootstrap.
 * 
 * @link http://getbootstrap.com
 * @link http://jasny.github.io/bootstrap
 * 
 * @option int version  Which major Bootstrap version is used
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
        // Add boostrap style class
        if (static::isButton($element)) {
            $element->addClass('btn btn-' . ($element->getOption('btn-style') ?: 'default'));
        } elseif (
            $element instanceof FB\Input && !(in_array($element->getType(), ['checkbox', 'radio'])) ||
            $element instanceof FB\Textarea ||
            $element instanceof FB\Select
        ) {
            $element->addClass('form-control');
        }
    }
    
    
    /**
     * Render prepend or append HTML.
     * 
     * @param Element $element
     * @param string  $html     Original rendered html
     * @return string
     */
    protected function renderAddon(Element $element, $html)
    {
        if (empty($html)) return $html;
        
        if ($element->hasClass('btn-labeled')) {
            $html = '<span class="btn-label">' . $html . '</span>';
        } elseif ($element instanceof FB\Input && !static::isButton($element)) {
            $html = '<span class="input-group-addon">' . $html . '</span>';
        }
        
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
        return $this->renderAddon($element, $html);
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
        return $this->renderAddon($element, $html);
    }
    
    
    /**
     * Render a label bound to the control.
     * 
     * @param Element $element
     * @param string  $html     Original rendered html
     * @return string
     */
    public function renderLabel(Element $element, $html)
    {
        if ($element instanceof FB\Input && $element->attr['type'] === 'hidden') return $html;
        
        $class = $element->getOption('container') ? 'control-label' : '';

        $grid = $element->getOption('grid');
        if ($grid && $element->getOption('container')) {
            $class = ltrim($class . " " . $grid['label']);
        }
        
        $required = $element instanceof FB\Control && $element->getAttr('required') ?
            $element->getOption('required-suffix') : '';
        
        if ($class) $class = 'class="' . $class . '"';
        return "<label {$class} for=\"" . $element->getId() . "\">"
            . $element->getDescription()
            . $required
            . "</label>";
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
        if ($element->hasClass('btn-labeled')) {
            $html = $element->getPrepend() . $html . $element->getAppend();
        }
        
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
        if ($element->hasClass('btn-labeled')) $html = $el;
        
        // Input group for prepend/append
        $useInputGroup = $element instanceof FB\Input && !self::isButton($element) &&
            ($element->getOption('prepend') != '' || $element->getOption('append') != '') &&
            $element->getOption('label') !== 'inside';
        
        if ($useInputGroup) $html = '<div class="input-group">' . "\n" . $html . "\n</div>";
        
        // Grid for horizontal form
        $grid = $element->getOption('grid');
        if ($grid && $element->getOption('container') && !$element instanceof FB\Group) {
            $class = $grid['control'];
            if ($element->getOption('label') !== true) {
                $class .= " " . preg_replace('/-(\d+)\b/', '-offset-$1', $grid['label']);
            }
            $html = '<div class="' . $class . '">' . "\n" . $html . "\n</div>";
        }
        
        return $html;
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
    public function renderContainer(Element $element, $html, $label, $field)
    {
        if (!$element->getOption('container')) return $html;
        
        $html = ($label ? $label . "\n" : '') . $field;
        
        // Add error
        $error = $element instanceof FB\Control ? $element->getError() : null;
        if ($error) $html .= "\n<span class=\"help-block error\">{$error}</span>";
        
        // Put everything in a container
        if ($element->getOption('container')) {
            $class = "form-group" . ($error ? " has-error" : "");
            $html = "<div class=\"$class\">\n{$html}\n</div>";
        }
        
        return $html;
    }
    
    
    /**
     * Check if element is a button
     * 
     * @param Element $element
     * @return boolean
     */
    protected static function isButton($element)
    {
        return
            $element instanceof FB\Button ||
            ($element instanceof FB\Input && in_array($element->attr['type'], ['button', 'submit', 'reset'])) ||
            $element->hasClass('btn') ||
            $element->getOption('btn-style');
    }
}
