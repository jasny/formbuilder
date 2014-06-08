<?php

namespace Jasny\FormBuilder;

use Jasny\FormBuilder;

/**
 * Base class for HTML nodes.
 */
abstract class Element
{
    /**
     * Decorators
     * @var Decorator[]
     */
    protected $decorators = [];
    
    /**
     * Disabled decorators
     * @var array
     */
    protected $disabledDecorators = [];
    
    /**
     * Parent element
     * @var Group
     */
    protected $parent;
    
    /**
     * HTML attributes
     * @var Attr
     */
    public $attr = [];
    
    /**
     * Element options
     * @var array
     */
    protected $options = [];
    
    
    /**
     * Class constructor.
     * 
     * @param array $attr     HTML attributes
     * @param array $options  Element options
     */
    public function __construct($attr=[], array $options=[])
    {
        $this->attr = new Attr(array_merge($this->attr, $attr));
        $this->options = array_merge($this->options, $options);
    }
    
    
    /**
     * Add a decorator to the element.
     * 
     * @param Decorator|string $decorator  Decorator object or name
     * @param mixed            $...        Additional arguments are passed to the constructor
     * @return Element  $this
     */
    public function addDecorator($decorator)
    {
        if (!$decorator instanceof Decorator) {
            $decorator = FormBuilder::buildDecorator($decorator, array_slice(func_get_args(), 1));
        }
        
        $decorator->connect($this);
        $this->decorators[] = $decorator;
        
        $this->applyDecorator($decorator);
        return $this;
    }

    /**
     * Apply modifications by decorator
     * 
     * @param Decorator $decorator
     */
    protected function applyDecorator(Decorator $decorator)
    {
        $decorator->apply($this);
    }
    
    /**
     * Add a decorator to the node.
     * 
     * @param Decorator|string $decorator  Decorator object or name
     * @return Element  $this
     */
    public function disableDecorator($decorator)
    {
        $this->disabledDecorators[] = $decorator;
    }
    
    /**
     * Get all decorators
     * 
     * @return Decorator[]
     */
    public function getDecorators()
    {
        // Get deep decorators from parent
        $deepDecorators = [];
        if ($this->getParent()) {
            foreach ($this->getParent()->getDecorators() as $decorator) {
                if ($decorator->isDeep()) $deepDecorators[] = $decorator;
            }
        }
        
        // Add our decorators
        $decorators = array_merge($deepDecorators, $this->decorators);
        
        // Remove disabled decorators
        foreach ($this->disabledDecorators as $decorator) {
            if (is_string($decorator)) $class = FormBuilder::$decorators[$decorator][0];
        
            foreach ($decorators as $i => $cur) {
                if (isset($class) ? is_a($cur, $class) : $cur === $decorator) {
                    unset($decorators[$i]);
                }
            }
        }
        
        return $decorators;
    }
    
    
    /**
     * Return parent object.
     * 
     * @return Group
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Alias of Element::getParent()
     * 
     * @return Element
     */
    final public function end()
    {
        return $this->getParent();
    }
    
    
    /**
     * Set HTML attribute(s).
     * 
     * @param string|array $attr   Attribute name or assoc array with attributes
     * @param mixed        $value
     * @return Element $this
     */
    final public function setAttr($attr, $value=null)
    {
        $this->attr->set($attr, $value);
        return $this;
    }
    
    /**
     * Get an HTML attribute(s).
     * All attributes will be cased to their string representation.
     * 
     * @param string  $attr  Attribute name, omit to get all attributes
     * @return mixed
     */
    final public function getAttr($attr = null)
    {
        return $this->attr->get($attr);
    }
    
    
    /**
     * Check if class is present
     * 
     * @param string $class
     * @return boolean
     */
    public function hasClass($class)
    {
        return isset($this->attr['class']) && in_array($class, explode(' ', $this->attr['class']));
    }
    
    /**
     * Add a class
     * 
     * @param string|array $class  Multiple classes may be specified as array or using a space
     * @return Element $this
     */
    public function addClass($class)
    {
        $attr = $this->attr['class'];
        
        if (!isset($this->attr['class'])) {
            $this->attr['class'] = is_array($class) ? join(' ', $class) : $class;
        } else {
            $classes = is_array($class) ? $class : explode(' ', $class);
            
            foreach ($classes as $class) {
                if (!$this->hasClass($class)) $attr .= ' ' . $class;
            }
        }
        
        return $attr;
    }
    
    /**
     * Remove a class
     * 
     * @param string|array $class  Multiple classes may be specified as array or using a space
     * @return Element $this
     */
    public function removeClass($class)
    {
        if (isset($this->attr['class'])) {
            $current = explode(" ", $this->attr['class']);
            $remove = explode(" ", $class);

            $attribute = join(' ', array_diff($current, $remove));
        }
        
        return $attribute;
    }
    
    
    /**
     * Set an option or array with options
     * 
     * @param sting|array $option  Option name or array with options
     * @param mixed       $value
     * @return Element $this
     */
    public function setOption($option, $value=null)
    {
        if (is_array($option)) {
            foreach ($option as $key=>$value) {
                if (!isset($value)) {
                    unset($this->options[$key]);
                } else {
                    $this->options[$key] = $value;
                }
            }
        } elseif (!isset($value)) {
            unset($this->options[$option]);
        } else {
            $this->options[$option] = $value;
        }
        
        return $this;
    }
    
    /**
     * Get an option.
     * 
     * @return mixed
     */
    final public function getOption($option)
    {
        $options = $this->getOptions();
        return isset($options[$option]) ? $options[$option] : null;
    }
        
    /**
     * Get all options.
     * Bubbles to combine options of parent/ancestors.
     * 
     * @return array
     */
    public function getOptions()
    {
        $defaults = isset($this->parent) ? $this->parent->getOptions() : FormBuilder::$options;
        $options = $this->options + $defaults;

        // Apply changes to optoins
        foreach ($this->getDecorators() as $decorator) {
            $options = $decorator->applyToOptions($this, $options);
        }
        
        return $options;
    }
    
    
    /**
     * Validate the element.
     * 
     * @return boolean
     */
    final public function isValid()
    {
        $valid = $this->validate();
        
        // Apply changes to optoins
        foreach ($this->getDecorators() as $decorator) {
            $valid = $decorator->isValid($this, $valid);
        }
        
        return $valid;
    }

    /**
     * Standard validation for the element
     * 
     * @return boolean
     */
    abstract protected function validate();
    
    
    /**
     * Render the element to HTML (without decoration).
     * 
     * @return string
     */
    abstract protected function render();

    /**
     * Render the element to HTML.
     * 
     * @return string
     */
    final public function toHTML()
    {
        $html = $this->render();
        
        foreach ($this->getDecorators() as $decorator) {
            $html = $decorator->render($this, $html);
        }
        
        return $html;
    }
    
    /**
     * Render the element to HTML.
     * 
     * @return string
     */
    final public function __toString()
    {
        return $this->toHTML();
    }
    
    
    /**
     * Parse a message, inserting values for placeholders.
     * 
     * @param string $message
     * @return string
     */
    public function parse($message)
    {
        return preg_replace_callback('/{{\s*([^}])++\s*}}/', array($this, 'resolvePlaceholder'), $message);
    }
    
    /**
     * Get a value for a placeholder
     * 
     * @param string $var
     * @return string
     */
    protected function resolvePlaceholder($var)
    {
        // preg_replace callback
        if (is_array($var)) $var = $var[1];
        
        $value = $this->getOption($var);
        if (!isset($value)) $value = $this->getAttr($var);
        
        if ($value instanceof FormElement) return $value->getValue();
        if ($value instanceof \DateTime) return strftime('%x', $value->getTimestamp());
        return (string)$value;
    }

    
    /**
     * Factory method
     * 
     * @param string $element
     * @return FormBuilder\Element|FormBuilder\FormElement
     */
    protected function build($element, array $args=[])
    {
        if ($this->parent) return $this->parent->build($element, $args);
        return FormBuilder::buildElement($element, $args);
    }
}
