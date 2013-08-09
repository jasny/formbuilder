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
     * Get all HTML attributes.
     * 
     * @param boolean $cast  Cast to a string
     * @return array
     */
    public function getAttrs($cast=true)
    {
        $attr = parent::getAttrs($cast);
        if (!isset($attr['placeholder']) && !$this->getOption('label')) $attr['placeholder'] = $this->getDescription();
        
        return $attr;
    }
    

    /**
     * Validate the textarea control.
     * 
     * @return boolean
     */
    public function isValid()
    {
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
    protected function render()
    {
        $html = "<textarea" . $this->renderAttrs() . ">" . $this->getValue() . "</textarea>";
        return $this->renderContainer($html);
    }
}
