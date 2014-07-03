<?php

namespace Jasny\FormBuilder;

use Jasny\FormBuilder;

/**
 * Base class for HTML elements.
 */
abstract class Element
{
    /**
     * Overwrite element types for factory method
     * @var array
     */
    protected static $customTypes = [];
    
    
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
        unset($options['id']);
        
        $this->options = $options + $this->options + ['decorate'=>true];
        foreach ($this->options as $key=>$value) {
            if (!isset($value)) unset($this->options[$key]);
        }
        
        $this->attr = new Attr($attr + $this->attr);
    }
    
    
    
    /**
     * Add a decorator to the element.
     * 
     * @param Decorator|string $decorator  Decorator object or name
     * @param mixed            $_          Additional arguments are passed to the constructor
     * @return Element  $this
     */
    public function addDecorator($decorator)
    {
        if (!$decorator instanceof Decorator) {
            $args = func_get_args();
            $decorator = call_user_func_array(['\Jasny\FormBuilder', 'decorator'], $args);
        }
        
        $decorator->apply($this, false);
        $this->decorators[] = $decorator;
        
        return $this;
    }

    /**
     * Apply decorators from parent
     * 
     * @param Group $parent
     */
    protected function applyDeepDecorators($parent)
    {
        if ($this->getOption('decorate') === false) return;
        
        foreach ($parent->getDecorators() as $decorator) {
            if ($decorator->isDeep()) $decorator->apply($this, true);
        }
    }
    
    /**
     * Get all decorators
     * 
     * @return Decorator[]
     */
    public function getDecorators()
    {
        $decorators = $this->decorators;

        if ($this->getOption('decorate') !== false && $this->getParent()) {
            foreach ($this->getParent()->getDecorators() as $decorator) {
                if ($decorator->isDeep()) $decorators[] = $decorator;
            }
        }
        
        return $decorators;
    }
    
    
    /**
     * Factory method
     * 
     * @param string $type     Element type
     * @param array  $options  Element options
     * @param array  $attr     HTML attributes
     * @return Element|Control
     */
    public function build($type, array $options=[], array $attr=[])
    {
        if (isset(static::$customTypes[$type])) {
            $custom = static::$customTypes[$type];
            $type = $custom[0];
            if (isset($custom[1])) $options = $custom[1] + $options;
            if (isset($custom[2])) $attr = $custom[2] + $attr;
        }
        
        if ($this->parent) return $this->parent->build($type, $options, $attr);
        
        if (is_string($type) && $type[0] === ':') {
            $method = 'build' . str_replace(' ', '', ucwords(preg_replace('/[^a-zA-Z0-9]/', ' ', substr($type, 1))));
            if (!method_exists($this, $method)) throw new \Exception("Unknown field '" . substr($type, 1) . "'");
            return $this->$method(null, $options, $attr);
        }
        
        return FormBuilder::element($type, $options, $attr);
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
        if ($option === 'decorate' && $this->getParent()) {
            $name = $this->getName();
            trigger_error("You should set the 'decorate' option before adding "
                . ($name ? "element '$name'" : "an element") . " to a form or group", E_USER_WARNING);
        }
        
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
    protected function validate()
    {
        return true;
    }
    
    
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
        
        if ($value instanceof Control) return $value->getValue();
        if ($value instanceof \DateTime) return strftime('%x', $value->getTimestamp());
        return (string)$value;
    }
}
