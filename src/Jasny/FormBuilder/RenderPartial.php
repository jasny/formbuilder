<?php

namespace Jasny\FormBuilder;

/**
 * Render only part of the element
 */
trait RenderPartial
{
    /**
     * Get prepend HTML.
     * 
     * @return string
     */
    public function getPrepend()
    {
        $this->applyDecorators();
        
        $prepend = $this->getOption('prepend');
        
        foreach ($this->getDecorators() as $decorator) {
            $prepend = $decorator->renderPrepend($this, $prepend);
        }
        
        return $prepend;
    }
            
    /**
     * Get append HTML.
     * 
     * @return string
     */
    public function getAppend()
    {
        $this->applyDecorators();
        
        $append = $this->getOption('append');
       
        foreach ($this->getDecorators() as $decorator) {
            $append = $decorator->renderAppend($this, $append);
        }
        
        return $append;
    }
    
    
    /**
     * Get the <label> component.
     * 
     * @return string
     */
    final public function getLabel()
    {
        $opt = $this->getOption('label');
        if ($opt === false || $opt === 'inside') return null;
        
        $this->applyDecorators();
        
        $html = $this->renderLabel();
        
        foreach ($this->getDecorators() as $decorator) {
            $html = $decorator->renderLabel($this, $html);
        }
        
        return $html;
    }
    
    /**
     * Render the control including layout elements.
     * 
     * @return string
     */
    final public function getControl()
    {
        $this->applyDecorators();
        
        $el = $this->renderElement();
        $html = $this->renderControl($el);
        
        foreach ($this->getDecorators() as $decorator) {
            $html = $decorator->renderControl($this, $html, $el);
        }
        
        return $html;
    }
    
    /**
     * Get content of the element.
     * 
     * @return string
     */
    final public function getContent()
    {
        $content = $this->renderContent();
        
        foreach ($this->getDecorators() as $decorator) {
            $content = $decorator->renderContent($this, $content);
        }
        
        return $content;
    }
    
    
    /**
     * Render the element to HTML
     * 
     * @return string
     */
    final public function render()
    {
        $label = $this->getLabel();
        $control = $this->getControl();
        
        $html = $this->renderContainer($label, $control);
        
        foreach ($this->getDecorators() as $decorator) {
            $html = $decorator->renderContainer($this, $html, $label, $control);
        }
        
        return $html;
    }
    
    /**
     * Render the container.
     * 
     * @param string $label    HTML of the label
     * @param string $control  HTML of the control
     * @return string
     */
    protected function renderContainer($label, $control)
    {
        $html = ($label ? $label . "\n" : '') . $control;
        
        // Put everything in a container
        if ($this->getOption('container')) $html = "<div>\n{$html}\n</div>";
        
        return $html;
    }
    
    /**
     * Render the label.
     * 
     * @return string
     */
    protected function renderLabel()
    {
        return '<label for="' . $this->getId() . '">'
            . $this->getDescription()
            . '</label>';
    }
    
    /**
     * Render the content of the HTML element.
     * 
     * @return string
     */
    protected function renderContent()
    {
        return null;
    }
    
    /**
     * Render to base HTML element.
     * 
     * @return string
     */
    abstract protected function renderElement();    
    
    /**
     * Render the element control.
     * 
     * @param string $el  HTML element
     * @return string
     */
    protected function renderControl($el)
    {
        return $this->getPrepend() . $el . $this->getAppend();
    }
}
