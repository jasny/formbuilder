<?php

namespace Jasny\FormBuilder;

/**
 * Representation of a <textarea> element.
 */
class Textarea extends Control
{
    /**
     * Class constructor.
     * 
     * @param array $options  Element options
     * @param array $attr     HTML attributes
     */
    public function __construct(array $options=[], array $attr=[])
    {
        if (!isset($attr['placeholder'])) $attr['placeholder'] = function() {
            return $this->getOption('label') ? null : $this->getDescription();
        };
        
        parent::__construct($options, $attr);
    }
    

    /**
     * Validate the textarea control.
     * 
     * @return boolean
     */
    public function validate()
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
    public function renderElement()
    {
        return "<textarea {$this->attr}>" . htmlentities($this->getValue()) . "</textarea>";
    }
}
