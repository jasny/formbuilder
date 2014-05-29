<?php

namespace Jasny\FormBuilder;

/**
 * Interface for form elements
 */
interface Element
{
    /**
     * Get the form to wich this control is added.
     * 
     * @return Form
     */
    public function getForm();

    /**
     * Get control identifier.
     * 
     * @return string
     */
    public function getId();

    /**
     * Set the name of the element.
     * 
     * @param string $name
     * @return Element $this
     */
    public function setName($name);
    
    /**
     * Get the name of the element.
     * 
     * @return string
     */
    public function getName();
    
    /**
     * Set the value of the element.
     * 
     * @param mixed $value
     * @return Element $this
     */
    public function setValue($value);
    
    /**
     * Get the value of the element.
     * 
     * @return mixed
     */
    public function getValue();
    
    /**
     * Set the description of the element.
     * 
     * @param string $description
     * @return Element $this
     */
    public function setDescription($description);
    
    /**
     * Get the description of the element.
     * 
     * @return string
     */
    public function getDescription();
    
    
    /**
     * Get the error message (after validation).
     * 
     * @return string
     */
    public function getError();
    
    /**
     * Set the error message.
     * 
     * @param string $error  The error message
     * @return string
     */
    public function setError($error);
    
    
    /**
     * Get the label
     * 
     * @return string
     */
    public function getLabel();

    /**
     * Get the element as HTML
     * 
     * @return string
     */
    public function getControl();

    /**
     * Get the element field
     * 
     * @return string
     */
    public function getField();

    
    /**
     * Render the element
     * 
     * @return string
     */
    public function render();

}
