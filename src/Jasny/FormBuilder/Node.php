<?php

namespace Jasny\FormBuilder;

/**
 * Base class for an HTML element with content.
 */
abstract class Node extends Element
{
    /**
     * The HTML content.
     * @var string|\Closure
     */
    protected $content;
    
    
    /**
     * Class constructor.
     * 
     * @param array $options  Element options
     * @param array $attr     HTML attributes
     */
    public function __construct(array $options = [], array $attr = [])
    {
        parent::__construct($options, $attr);
    }
    
    
    /**
     * Set node content
     * 
     * @param string|\Closure $content
     * @return Node $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }
    
    /**
     * Get the node content
     * 
     * @return string
     */
    public function getContent()
    {
        $content = $this->content;
        if ($content instanceof \Closure) $content = $content();
        
        foreach ($this->getDecorators() as $decorator) {
            $content = $decorator->renderContent($this, $content);
        }
        
        return (string)$content;
    }
    
    /**
     * Render the element
     * 
     * @return string
     */
    protected function render()
    {
        $tagname = $this::TAGNAME;
        return "<{$tagname} {$this->attr}>" . $this->getContent() . "</{$tagname}>";
    }
}
