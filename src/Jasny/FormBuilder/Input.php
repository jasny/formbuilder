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
     * Get all HTML attributes.
     * 
     * @param boolean $cast  Cast to a string
     * @return array
     */
    public function getAttrs($cast=true)
    {
        $attrs = parent::getAttrs($cast);
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
    public function isValid()
    {
        if (!$this->validateRequired()) return false;
        
        // Empty and not required, means no further validation
        if ($this->getValue() === null || $this->getValue() === '') return true;

        if (!$this->validateType()) return false;
        if (!$this->validateMinMax()) return false;
        if (!$this->validateLength()) return false;
        if (!$this->validatePattern()) return false;

        if (!$this->validateMatch()) return false;
        
        return true;
    }

    /**
     * Validate if value matches the input type.
     * 
     * @return boolean
     */
    protected function validateType()
    {
        $value = $this->getValue();
        $type = $this->getAttr('type');
        
        switch ($type) {
            case 'color':
                if (preg_match('/^#\d{3}(\d{3})?|rgb(\d{1,3},\d{1,3},\d{1,3});?$/', $value)) return true;
                break;
            case 'number':
                if (ctype_digit((string)$value)) return true;
                break;
            case 'range':
                if (is_numeric($value)) return true;
                break;
            case 'date':
            case 'datetime':
            case 'datetime-local':
                try {
                    if (!$value instanceof \DateTime) new \DateTime((string)$value);
                    return true;
                } catch (Exception $e) {}
                break;
            case 'time':
                if (preg_match('/^\d\d?:\d\d?(:\d\d?)?$/', $value)) return true;
                break;
            case 'month':
                if (preg_match('/^\d\d(\d\d)?-\d\d$/', $value)) return true;
                break;
            case 'week':
                if (preg_match('/^\d\d?:\d\d?(:\d\d?)?$/', $value)) return true;
                break;
            case 'url':
                if (preg_match('/^\w+:/', $value)) return true;
                break;
            case 'email':
                if (preg_match('/^[\w\-\.]+@[\w\-\.]+$/', $value)) return true;
                break;
            default:
                return true;
        }
        
        $this->setError($this->getOption('error:type'));
        return false;
    }
    
    /**
     * Render the <input>.
     * 
     * @return string
     */
    protected function render()
    {
        // Determine default options and attributes
        $type = $this->getAttr('type');
        
        // Render the input
        $html = "<input" . $this->renderAttrs() . ">";
        
        if ($type == 'checkbox' || $type == 'radio') {
            $html = "<input type=\"hidden\" name=\"" . htmlentities($this->getAttr('name')) . "\" value=\"\">\n$html";
        }
        
        return $this->renderContainer($html);
    }
}
