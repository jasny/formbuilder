<?php

namespace Jasny\FormBuilder;

/**
 * Base class for a link or button.
 */
abstract class Action extends Element
{
    use RenderPartial;
    
    /**
     * Class constructor.
     * 
     * @param array  $options  Element options
     * @param array  $attrs    HTML attributes
     */
    public function __construct(array $options=[], array $attrs=[])
    {
        $options += ['label'=>false, 'encode'=>true];
        parent::__construct($options, $attrs);
    }
    
    
    /**
     * Set the description of the element.
     * 
     * @param string $description
     * @return Control $this
     */
    final public function setDescription($description)
    {
        $this->setOption('description', $description);
        return $this;
    }
    
    /**
     * Get the description of the element.
     * 
     * @return string
     */
    final public function getDescription()
    {
        return $this->getOption('description');
    }
    
        
    /**
     * Validate the element.
     * 
     * @return boolean
     */
    public function validate()
    {
        return true;
    }
    
    
    /**
     * Get the content of the button/link.
     * 
     * @return string
     */
    public function renderContent()
    {
        $content = $this->getDescription();
        if ($this->getOption('escape')) $content = htmlentities($content);

        return $content;
    }
}
