<?php

namespace Jasny\FormBuilder;

/**
 * Representation of a control with items in a form.
 * 
 * @option items            Key/value pairs used to create <option> list
 * @option selected-first   Put the selected option(s) on top of the list
 * @option multiple         Allow multiple items to be selected
 */
abstract class Choice extends Control
{
    /**
     * Return list items
     * 
     * @return array
     */
    public function getItems()
    {
        return $this->getOption('items') ?: [];
    }
    
    /**
     * Set the value of the control.
     * 
     * @param string $value
     * @return Control $this
     */
    public function setValue($value)
    {
        if ($this->getOption('multiple') && !is_array($value)) {
            $value = (string)$value === '' ? [] : (array)$value;
        }
        
        return parent::setValue($value);
    }

    /**
     * Validate the control.
     * 
     * @return boolean
     */
    public function validate()
    {
        return $this->validateRequired();
    }
}
