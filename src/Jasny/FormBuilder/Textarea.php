<?php

namespace Jasny\FormBuilder;

/**
 * Representation of a <textarea> element in a Bootstrap form.
 * 
 * @option id           Element id
 * @option name         Element name
 * @option description  Description as displayed on the label
 * @option type         HTML5 input type
 * @option value        Element value
 */
class Textarea extends BaseControl
{
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
        if (isset($options['value'])) $this->value = $options['value'];
        
        if (!isset($attr['placeholder'])) {
            $attr['placeholder'] = function() {
                return $this->getOption('label') ? null : $this->getDescription();
            };
        }
        
        unset($options['value']);
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
     * Set the value of the control.
     * 
     * @param string $value
     * @return Boostrap/BaseControl $this
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
    protected function renderElement()
    {
        return "<textarea {$this->attr}>" . $this->getValue() . "</textarea>";
    }
}
