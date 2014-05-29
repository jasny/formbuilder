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
     * @param array $attrs        HTML attributes
     * @param array $options      Element options
     */
    public function __construct($name=null, $description=null, array $attrs=[], array $options=[])
    {
        if (!isset($attrs['type'])) $attrs['type'] = 'text';
        if ($attrs['type'] == 'checkbox' && !isset($attrs['value'])) $attrs['value'] = 1;
        
        parent::__construct($name, $description, $attrs, $options);
    }
    
    
    /**
     * Get the value of the control.
     * 
     * @return mixed
     */
    public function getValue()
    {
        $value = $this->getAttr('value', false);
        if ($value instanceof Control) $value = $value->getValue();

        if (($this->getAttr('type') == 'checkbox' || $this->getAttr('type') == 'radio') && !$this->getAttr('checked')) {
            $value = false;
        }
        
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
        switch ($this->getAttr('type')) {
            case 'checkbox':
                $checked = (boolean)$value;
                $this->setAttr('checked', $checked);
                if ($checked) $this->setAttr('value', $value);
                break;
            case 'radio':
                $this->setAttr('checked', $value == $this->getAttr('value'));
                break;
            default:
                $this->setAttr('value', $value);
                break;
        }
        
        return $this;
    }

    
    /**
     * Get an HTML attribute(s).
     * All attributes will be cased to their string representation.
     * 
     * @param string  $attr  Attribute name, omit to get all attributes
     * @return mixed
     */
    public function getAttr($attr = null)
    {
        $attrs = parent::getAttr($cast);
        $type = $attrs['type'];
        
        if ($this->getDescription()) {
            if (!isset($attrs['value']) && ($type == 'button' || $type == 'submit' || $type == 'reset')) {
                $attrs['value'] = $this->getDescription();
            } elseif (!isset($attrs['placeholder']) && !$this->getOption('label')) {
                $attrs['placeholder'] = $this->getDescription();
            }
        }
        
        return $attrs;
    }
    
    /**
     * Get all options.
     * 
     * @return array
     */
    public function getOptions()
    {
        $options = parent::getOptions();
        
        switch ($this->attrs['type']) {
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

        if ($this->getAttr('type') === 'file' && !$this->validateUpload()) return false;
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
     * @param array $attr
     * @param array $options
     * @return string
     */
    protected function generateField($attr, $options)
    {
        $html = parent::generateField($attr, $options);
        
        // Determine default options and attributes
        if ($this->getAttr('type') == 'checkbox' && $this->getOption('add-hidden')) {
            $name = htmlentities($this->getAttr('name'));
            $html = '<input type="hidden" name="' . $name . '" value="">' . "\n" . $html;
        }
        
        return $html;
    }

    /**
     * Render the <input>.
     * 
     * @return string
     */
    protected function generateControl()
    {
        if (!isset($this->attr->placeholder) && !$this->getOption('label')) {
            $extra['placeholder'] = $this->getDescription();
        }
        
        $attr = $this->attr->render($extra);
        return "<input $attr>";
    }
}
