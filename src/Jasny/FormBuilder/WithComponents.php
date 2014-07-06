<?php

namespace Jasny\FormBuilder;

/**
 * Element that exists of several components.
 */
interface WithComponents
{
    /**
     * Initialise component
     * 
     * @param string          $name     Component name
     * @param string          $type     Element type
     * @param array           $options  Element options
     * @param array           $attr     Element attr
     * @return Element $this
     */
    public function newComponent($name, $type, array $options=[], array $attr=[]);
    
    /**
     * Get a component.
     * 
     * @param string $name
     * @return Element
     */
    public function getComponent($name);
    
    /**
     * Render to base HTML element.
     * 
     * @return string
     */
    public function renderElement();
}
