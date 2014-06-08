<?php

namespace Jasny\FormBuilder;

/**
 * Representation of a <textarea> element in a Bootstrap form.
 */
class Textarea extends Control
{
    /**
     * @var string
     */
    protected $value;
    
    /**
     * Class constructor.
     * 
     * @param string $name
     * @param string $description  Description as displayed on the label 
     * @param mixed  $value
     * @param array  $attr         HTML attributes
     * @param array  $options      FormElement options
     */
    public function __construct($name=null, $description=null, $value=null, array $attr=[], array $options=[])
    {
        if (isset($value)) $this->value = $value;
        
        if (!isset($attr['placeholder'])) {
            $attr['placeholder'] = function() {
                return $this->getOption('label') ? null : $this->getDescription();
            };
        }
        
        parent::__construct($name, $description, $attr, $options);
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
     * Set the value of the control.
     * 
     * @param string $value
     * @return Boostrap/Control $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }
    

    /**
     * Validate the textarea control.
     * 
     * @return boolean
     */
    public function validate()
    {
        if (!$this->getOption('basic-validation')) return true;
        
        if (!$this->validateRequired()) return false;
        
        // Empty and not required, means no further validation
        if ($this->getValue() === null || $this->getValue() === '') return true;
        
        if (!$this->validateLength()) return false;
        
        return true;
    }

    
    /**
     * Render the <textarea>
     * 
     * @return string
     */
    protected function renderControl()
    {
        return "<textarea {$this->attr}>" . $this->getValue() . "</textarea>";
    }
}
