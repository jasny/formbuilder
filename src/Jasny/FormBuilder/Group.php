<?php

namespace Jasny\FormBuilder;

/**
 * Base class for an HTML element with children.
 */
abstract class Group extends Element
{
    /**
     * The HTML tag name.
     * @var string
     */
    protected $tagname;
    
    /**
     * Elements of the group.
     * @var array
     */
    protected $elements = array();

    
    /**
     * Add an element to the group.
     * 
     * @param Element|string $element
     * @return Group  $this
     */
    public function add($element)
    {
        if (is_string($element) && $element[0] !== '<') $element = $this->build($element, array_slice(func_get_args(), 1));
        if ($element instanceof Element) $element->parent = $this;
        
        $this->elements[] = $element;
        return $this;
    }
    
    /**
     * Add an element and return it.
     * 
     * @param Element|string  $element
     * @return Element  $element
     */
    public function begin($element)
    {
        if (is_string($element) && $element[0] !== '<') $element = $this->build($element, array_slice(func_get_args(), 1));
        
        $this->add($element);
        return $element;
    }
    
    /**
     * Get the elements of the group.
     * 
     * @return array
     */
    public function getElements()
    {
        return $this->elements;
    }
    
    
    /**
     * Find a control.
     * 
     * @param string $name  Control name or #id
     * @return Bootstrap\Control
     */
    public function getControl($name)
    {
        if ($name[0] == '#') $id = substr($name, 1);
        
        foreach ($this->elements as $element) {
            if ($element instanceof Control) {
                if (isset($id) ? $element->getId() == $id : $element->getName() == $name) return $element;
            } elseif ($element instanceof Group) {
                $control = $element->getControl($name);
                if ($control) return $control;
            }
        }
        
        return null; // Not found
    }
    
    /**
     * Get all the controls in the group (incl children).
     * 
     * @return array
     */
    public function getControls()
    {
        $controls = array();
        
        foreach ($this->elements as $element) {
            if ($element instanceof Control) $controls[] = $element;
             elseif ($element instanceof Group) $controls = array_merge($controls, $element->getControls());
        }
        
        return $controls;
    }
    
    
    /**
     * Set the values of the controls.
     * 
     * @param array $values
     * @return Group  $this
     */
    public function setValues($values)
    {
        $values = (array)$values;

        foreach ($this->getControls() as $control) {
            $name = $control->getName();
            if ($name && isset($values[$name])) $control->setValue($values[$name]);
        }
        
        return $this;
    }
    
    /**
     * Get the values of the controls.
     * 
     * @return array
     */
    public function getValues()
    {
        $values = array();
        
        foreach ($this->getControls() as $control) {
            if ($control->getName()) $values[$control->getName()] = $control->getValue();
        }
        
        return $values;
    }
    
    /**
     * Validate the controls in the group.
     * 
     * @return boolean
     */
    public function isValid()
    {
        $ret = true;
        
        foreach ($this->elements as $element) {
            if (!$element instanceof Element || $element->getOption('validation') == false) continue;
            $ret = $ret && $element->isValid();
        }
        
        return $ret;
    }
    
    
    /**
     * Render the element to HTML.
     * 
     * @return string
     */
    protected function render()
    {
        $html = "<" . $this->tagname . $this->renderAttrs() . ">\n";
        
        foreach ($this->elements as $element) {
            if (!isset($element) || ($element instanceof Element && !$element->getOption('render'))) continue;
            $html .= (string)$element . "\n";
        }
        
        $html .= "</" . $this->tagname . ">";
        
        return $html;
    }
}
