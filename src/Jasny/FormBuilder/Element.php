<?php

namespace Jasny\FormBuilder;

use Jasny\FormBuilder;

/**
 * Base class for HTML elements.
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
     * @param array $options  Element options
     * @param array $attr     HTML attributes
     */
    public function __construct(array $options=[], array $attr=[])
    {
        if (isset($options['id'])) $attr['id'] = $options['id'];
        if (isset($options['name'])) $attr['name'] = $options['name'];
        unset($options['id'], $options['name']);
        
        $this->attr = new Attr($attr + $this->attr);
        $this->options = array_merge($this->options, $options);
    }
    
    
    /**
     * Add a decorator to the element.
     * 
     * @param Decorator|string $decorator  Decorator object or name
     * @param array            $options
     * @return Element  $this
     */
    public function addDecorator($decorator, array $options=[])
    {
        if (!$decorator instanceof Decorator) {
            $decorator = FormBuilder::decorator($decorator, $options);
        }
        
        $decorator->connect($this);
        $this->decorators[] = $decorator;
        
        return $this;
    }

    /**
     * Apply modifications by decorators
     */
    protected function applyDecorators()
    {
        foreach ($this->getDecorators() as $decorator) {
            $decorator->apply($this);
        }
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
     * Get the form to wich this element is added.
     * 
     * @return Form
     */
    public function getForm()
    {
        $parent = $this->getParent();
        while ($parent && !$parent instanceof Form) {
            $parent = $parent->getParent();
        }
        
        return $parent;
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
     * @return Group
     */
    final public function end()
    {
        return $this->getParent();
    }
    
    
    /**
     * Get element id.
     * 
     * @return string
     */
    public function getId()
    {
        if (!isset($this->attr['id'])) {
            $form = $this->getForm();
            
            if ($form) {
                $name = $this->getName();
                $id = $this->getForm()->getId() . '-' . ($name ?
                    preg_replace('/[^\w\-]/', '', str_replace('[', '-', $name)) :
                    base_convert(uniqid(), 16, 32));
            } else {
                $id =  base_convert(uniqid(), 16, 32);
            }

            $this->attr['id'] = $id;
        }
        
        return $this->attr['id'];
    }
    
    /**
     * Set the name of the element.
     * 
     * @param string $name
     * @return Control $this
     */
    public function setName($name)
    {
        return $this->setOption('name', $name);
    }
    
    /**
     * Return the name of the control.
     * 
     * @return string
     */
    public function getName()
    {
        return $this->getOption('name');
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
    final public function hasClass($class)
    {
        return $this->attr->hasClass($class);
    }
    
    /**
     * Add a class
     * 
     * @param string|array $class  Multiple classes may be specified as array or using a space
     * @return Element $this
     */
    final public function addClass($class)
    {
        $this->attr->addClass($class);
        return $this;
    }
    
    /**
     * Remove a class
     * 
     * @param string|array $class  Multiple classes may be specified as array or using a space
     * @return Element $this
     */
    public function removeClass($class)
    {
        $this->attr->removeClass($class);
        return $this;
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
     * @param string $option
     * @return mixed
     */
    public function getOption($option)
    {
        if (isset($this->options[$option])) return $this->options[$option];
        
        if (isset($this->parent)) return $this->parent->getOption($option, true);
        if (isset(FormBuilder::$options[$option])) return FormBuilder::$options[$option];
        
        return null; // not found
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
        $this->applyDecorators();
        
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
        
        if ($value instanceof Control) return $value->getValue();
        if ($value instanceof \DateTime) return strftime('%x', $value->getTimestamp());
        return (string)$value;
    }
}
