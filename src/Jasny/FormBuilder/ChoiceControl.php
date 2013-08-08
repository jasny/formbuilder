<?php

namespace Jasny\FormBuilder;

/**
 * Representation of a control with values in a form.
 */
abstract class ChoiceControl extends Control
{
    /**
     * @var array
     */
    protected $values;
    
    /**
     * @var string
     */
    protected $value;
    
    /**
     * Class constructor.
     * 
     * @param array $name
     * @param array $values       Key/value pairs for each choice
     * @param array $description  Description as displayed on the label 
     * @param array $attrs        HTML attributes
     * @param array $options      Element options
     */
    public function __construct($name=null, array $values=[], $description=null, array $attrs=[], array $options=[])
    {
        if (isset($name) && !empty($attrs['multiple'])) $name = $name . '[]';
        
        parent::__construct($name, $description, $attrs, $options);
        $this->values = $values;
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
     * Detect if keys are in fact the values.
     * 
     * @param array $values
     * @return boolean
     */
    protected function keysHoldValues(array $values)
    {
        $usekeys = false;
        
        foreach ($values as $value) {
            if (!array_key_exists($value, $this->values)) {
                $usekeys = true;
                break;
            }
        }
        
        if (!$usekeys) return false;

        foreach (array_keys($values) as $key) {
            if (!array_key_exists($key, $this->values)) {
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
        if ($this->getAttr('multiple') && !is_array($value)) $value = (string)$value === '' ? [] : (array)$value;
        
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
    public function isValid()
    {
        return $this->validateRequired();
    }
}
