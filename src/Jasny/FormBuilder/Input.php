<?php

namespace Jasny\FormBuilder;

/**
 * Representation of an <input> element in a form.
 */
class Input extends Control
{
    /**
     * Class constructor.
     * 
     * @param array $name
     * @param array $description  Description as displayed on the label 
     * @param array $attr         HTML attributes
     * @param array $options      FormElement options
     */
    public function __construct($name=null, $description=null, array $attr=[], array $options=[])
    {
        $attr += $this->attr + ['type'=>'text'];
        
        if ($attr['type'] === 'checkbox' && !isset($attr['value'])) $attr['value'] = 1;

        if (in_array($attr['type'], ['button', 'submit', 'reset']) && !isset($attr['value'])) {
            $attr['value'] = function() {
                return $this->getDescription();
            };
        }
        
        $noPlaceholder = ['hidden', 'button', 'submit', 'reset', 'checkbox', 'radio', 'file'];
        if (!in_array($attr['type'], $noPlaceholder) && !isset($attr['placeholder'])) {
            $attr['placeholder'] = function() {
                return $this->getOption('label') ? null : $this->getDescription();
            };
        }
        
        parent::__construct($name, $description, $attr, $options);
    }
    
    
    /**
     * Get the value of the control.
     * 
     * @return mixed
     */
    public function getValue()
    {
        $value = $this->attr['value'];
        if ($value instanceof FormElement) $value = $value->getValue();

        $type = $this->attr['type'];
        if (($type === 'checkbox' || $type === 'radio') && !$this->attr->get('checked')) $value = false;
        
        return $value;
    }
    
    /**
     * Set the value of the control.
     * 
     * @param mixed $value
     * @return Boostrap/Control $this
     */
    public function setValue($value)
    {
        switch ($this->attr['type']) {
            case 'checkbox':
                $checked = (boolean)$value;
                $this->attr['checked'] = $checked;
                if ($checked) $this->attr['value'] = $value;
                break;
            case 'radio':
                $this->setAttr('checked', $value == $this->getAttr('value'));
                break;
            default:
                $this->attr['value'] = $value;
                break;
        }
        
        return $this;
    }

    /**
     * Get all options.
     * 
     * @return array
     */
    public function getOptions()
    {
        $options = parent::getOptions();
        
        switch ($this->attr['type']) {
            case 'hidden':
                if (!isset($this->options['label'])) $options['label'] = false;
                if (!isset($this->options['container'])) $options['container'] = false;
                break;
            
            case 'checkbox':
            case 'radio':
                if (!isset($this->options['label'])) $options['label'] = 'inside';
                break;
                
            case 'button':
            case 'submit':
            case 'reset':
                if (!isset($this->options['label'])) $options['label'] = false;
                break;
        }
        
        return $options;
    }
    
    
    /**
     * Validate the input control.
     * 
     * @return boolean
     */
    protected function validate()
    {
        if (!$this->getOption('basic-validation')) return true;
        
        if (!$this->validateRequired()) return false;

        // Empty and not required, means no further validation
        if ($this->getValue() === null || $this->getValue() === '') return true;

        if ($this->attr['type'] === 'file' && !$this->validateUpload()) return false;
        if (!$this->validateType()) return false;
        if (!$this->validateMinMax()) return false;
        if (!$this->validateLength()) return false;
        if (!$this->validatePattern()) return false;

        if (!$this->validateMatch()) return false;
        
        return true;
    }
    

    /**
     * Render the element field to HTML.
     * 
     * @param string $control  Control HTML
     * @return string
     */
    protected function renderField($control)
    {
        // Determine default options and attributes
        if ($this->attr['type'] == 'checkbox' && $this->getOption('add-hidden')) {
            $name = htmlentities($this->attr['name']);
            $control = '<input type="hidden" name="' . $name . '" value="">' . $control;
        }

        return parent::renderField($control);
    }

    /**
     * Render the <input>.
     * 
     * @return string
     */
    protected function renderControl()
    {
        return "<input {$this->attr}>";
    }
}
