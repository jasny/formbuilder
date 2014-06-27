<?php

namespace Jasny\FormBuilder;

/**
 * Representation of a control with items in a form.
 */
abstract class Choice extends Control
{
    /**
     * @var array
     */
    protected $items = [];
    
    /**
     * @var string
     */
    protected $value;
    
    /**
     * Class constructor.
     * 
     * @param array $options  Element options
     * @param array $attr     HTML attributes
     */
    public function __construct(array $options=[], array $attr=[])
    {
        if (isset($options['name'])) $attr['name'] = $options['name'];
        if (!isset($options['multiple'])) $options['multiple'] = isset($attr['multiple']) ? $attr['multiple'] : false;
        
        if (isset($attr['name']) && !empty($options['multiple']) && substr($attr['name'], -2) !== '[]') {
            $attr['name'] .= '[]';
        }
        
        if (isset($options['items'])) $this->items = $options['items'];
        
        unset($options['name'], $options['items']);
        parent::__construct($options, $attr);
    }
    
    
    /**
     * Get the value of the control.
     * 
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Detect if keys are in fact the items.
     * 
     * @param array $items
     * @return boolean
     */
    protected function keysHoldValues(array $items)
    {
        $usekeys = false;
        
        foreach ($items as $value) {
            if (!array_key_exists($value, $this->items)) {
                $usekeys = true;
                break;
            }
        }
        
        if (!$usekeys) return false;

        foreach (array_keys($items) as $key) {
            if (!array_key_exists($key, $this->items)) {
                $usekeys = false;
                break;
            }
        }
        
        return $usekeys;
    }
    
    /**
     * Set the value of the control.
     * 
     * @param string $value
     * @return Control $this
     */
    public function setValue($value)
    {
        if (is_array($value) && $this->keysHoldValues($value)) $value = array_keys($value);
        if ($this->attr['multiple'] && !is_array($value)) $value = (string)$value === '' ? [] : (array)$value;
        
        $this->value = $value;
        return $this;
    }

    /**
     * Return the name of the control.
     * 
     * @return string
     */
    public function getName()
    {
        $name = $this->getAttr('name');
        if (substr($name, -2) == '[]') $name = substr($name, 0, -2);
        
        return $name;
    }
    
    
    /**
     * Validate the select control.
     * 
     * @return boolean
     */
    public function validate()
    {
        if (!$this->getOption('basic-validation')) return true;
        return $this->validateRequired();
    }
}
