<?php

namespace Jasny\FormBuilder;
use Jasny\FormBuilder;

/**
 * Base class for HTML nodes.
 */
abstract class Node
{
    /**
     * Decorators
     * @var Decorator[]
     */
    protected $decorators = [];
    
    /**
     * Parent element
     * @var Group
     */
    protected $parent;
    
    /**
     * HTML attributes
     * @var Attr
     */
    protected $attr = [];
    
    /**
     * Node options 
     * @var array
     */
    protected $options = [];
    
    
    /**
     * Class constructor.
     * 
     * @param array|Attr $attr     HTML attributes
     * @param array      $options  Node options
     */
    public function __construct($attr=[], array $options=[])
    {
        $this->attr = new Attr($this->attr, $attr);
        $this->options = array_merge($this->options, $options);
    }
    
    
    /**
     * Add a decorator to the node.
     * 
     * @param Decorator|string $decorator  Decorator object or name
     * @param mixed            $...        Additional arguments are passed to the constructor
     * @return Node  $this
     */
    public function addDecorator($decorator)
    {
        if (!$decorator instanceof Decorator) {
            $decorator = FormBuilder::build('decorator', func_get_args());
        }
        
        $decorator->connect($this);
        $this->decorators[] = $decorator;
        
        return $this;
    }
    
    /**
     * Add a decorator to the node.
     * 
     * @param Decorator|string $decorator  Decorator object or name
     * @return Node  $this
     */
    public function removeDecorator($decorator)
    {
        if (is_string($decorator)) $class = FormBuilder::$decorators[$decorator][0];
        
        foreach ($this->decorators as $i => $cur) {
            if (isset($class) ? is_a($cur, $class) : $cur === $decorator) {
                unset($this->decorators[$i]);
            }
        }
        
        return $this;
    }
    
    /**
     * Get all decorators
     * 
     * @return Decorator[]
     */
    public function getDecorators()
    {
        $decorators = [];
        
        if ($this->getParent()) {
            $parent_decorators = $this->getParent()->getDecorators();
            foreach ($parent_decorators as $decorator) {
                if ($decorator->isDeep()) $decorators = $decorator;
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
     * Alias of Node::getParent()
     * 
     * @return Node
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
     * @return Node $this
     */
    public function setAttr($attr, $value=null)
    {
        $this->attrs->set($attr, $value);
        return $this;
    }
    
    /**
     * Get an HTML attribute(s).
     * All attributes will be cased to their string representation.
     * 
     * @param string  $attr  Attribute name, omit to get all attributes
     * @return mixed
     */
    public function getAttr($attr = null)
    {
        return $this->attrs->get($attr);
    }
    
    
    /**
     * Add a CSS class
     * 
     * @param string|array $class   Multiple classes may be specified as array or using a space
     * @return Node $this
     */
    public function addClass($class)
    {
        if (empty($this->attr->class)) {
            $this->attr->class = is_array($class) ? join(' ', $class) : $class;
        } else {
            $classes = is_array($class) ? $class : explode(' ', $class);
            
            foreach ($classes as $class) {
                if (!$this->hasClass($class)) {
                    $this->attr->class .= ' ' . $class;
                }
            }
        }
        
        return $this;
    }
    
    /**
     * Remove a CSS class
     * 
     * @param string|array $class   Multiple classes may be specified as array or using a space
     * @return Node $this
     */
    public function removeClass($class)
    {
        if (!empty($this->attr->class)) {
            $current = explode(" ", $this->attr->class);
            $remove = explode(" ", $class);

            $this->attr->class = join(' ', array_diff($current, $remove));
        }
        
        return $this;
    }
    
    /**
     * Check if element has a specific CSS class
     * 
     * @param string $class
     * @return boolean
     */
    public function hasClass($class)
    {
        $current = explode(" ", $this->attr->class);
        return in_array($class, $current);
    }
    
    
    /**
     * Set an option or array with options
     * 
     * @param sting|array $option  Option name or array with options
     * @param mixed       $value
     * @return Boostrap/Node $this
     */
    public function setOption($option, $value=null)
    {
        if (is_array($option)) {
            foreach ($option as $key=>$value) {
                if (!isset($value)) unset($this->options[$key]);
                 else $this->options[$key] = $value;
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
        $defaults = isset($this->parent) ? $this->parent->getOptions() : self::$defaults;
        $options = $this->options + $defaults;

        // Apply changes to optoins
        foreach ($this->decorators as $decorator) {
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
    public function __toString()
    {
        $html = $this->render();
        
        foreach ($this->getDecorators() as $decorator) {
            $html = $decorator->render($this, $html);
        }
        
        return $html;
    }
    
    
    /**
     * Factory method
     * 
     * @param string $element
     */
    public function build($element, array $args=[])
    {
        if ($this->parent) return $this->parent->build($element, $args);
        return FormBuilder::element($element, $args);
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
        if (is_array($var)) {
            $var = $var[1];
        }
        
        $value = $this->getOption($var);
        if (!isset($value)) $value = $this->getAttr($var);
        
        if ($value instanceof Element) return $value->getValue();
        if ($value instanceof \DateTime) return strftime('%x', $value->getTimestamp());
        return $value = (string)$value;
    }
}
