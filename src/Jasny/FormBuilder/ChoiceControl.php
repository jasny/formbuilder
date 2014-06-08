<?php

namespace Jasny\FormBuilder;

/**
 * Representation of a control with items in a form.
 */
abstract class ChoiceControl extends Control
{
    /**
     * @var array
     */
    protected $items;
    
    /**
     * @var string
     */
    protected $value;
    
    /**
     * Class constructor.
     * 
     * @param array $name
     * @param array $items        Key/value pairs for each choice
     * @param array $description  Description as displayed on the label 
     * @param array $attrs        HTML attributes
     * @param array $options      Element options
     */
    public function __construct($name=null, array $items=[], $description=null, array $attrs=[], array $options=[])
    {
        if (isset($name) && !empty($attrs['multiple'])) $name = $name . '[]';
        
        parent::__construct($name, $description, $attrs, $options);
        $this->items = $items;
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
