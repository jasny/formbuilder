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
    protected function generateControl()
    {
        if (!isset($this->attr->placeholder) && !$this->getOption('label')) {
            $extra['placeholder'] = $this->getDescription();
        }
        
        $attr = $this->attr->render($extra);
        return "<textarea $attr>" . $this->getValue() . "</textarea>";
    }
}
