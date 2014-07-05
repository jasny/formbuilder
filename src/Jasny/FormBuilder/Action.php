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
        $options += ['label'=>false, 'escape'=>true];
        parent::__construct($options, $attrs);
    }
    
    
    /**
     * Get the description of the element.
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->getOption('description') ?:
            ucfirst(preg_replace(['/^.+[\.\[]|\]/', '/[_-]/'], ['', ' '], $this->getName()));
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
     * Render the content of the element.
     * 
     * @return string
     */
    protected function renderContent()
    {
        $content = $this->getDescription();
        if ($this->getOption('escape')) $content = htmlentities($content);

        return $content;
    }
    
    /**
     * Render the element.
     * 
     * @return string
     */
    public function renderElement()
    {
        $tagname = $this::TAGNAME;
        return "<{$tagname} {$this->attr}>" . $this->getContent() . "</{$tagname}>";
    }
}
