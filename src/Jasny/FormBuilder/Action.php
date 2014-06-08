<?php

namespace Jasny\FormBuilder;

/**
 * Base class for a link or button.
 */
abstract class Action extends Element
{
    /**
     * Class constructor.
     * 
     * @param array  $options  Element options
     * @param array  $attrs    HTML attributes
     */
    public function __construct(array $options=[], array $attrs=[])
    {
        if (!isset($options['escape'])) $options['escape'] = true;
        parent::__construct($options, $attrs);
    }
    
    
    /**
     * Set the text of the link.
     * 
     * @param string $content
     * @return Link $this
     */
    final public function setContent($content)
    {
        $this->setOption('content', $content);
        return $this;
    }
    
    /**
     * Get the text of the link.
     * 
     * @return string
     */
    final public function getContent()
    {
        return $this->getOption('content') ?: $this->getOption('description');
    }
    
    /**
     * Set the description of the element.
     * 
     * @param string $description
     * @return FormElement $this
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
     * Get all options.
     * 
     * @return array
     */
    public function getOptions()
    {
        $options = parent::getOptions();
        if (!isset($this->options['label'])) $options['label'] = false;
        
        return $options;
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
     * Get the <label> component.
     * 
     * @return string
     */
    final public function getLabel()
    {
        $opt = $this->getOption('label');
        if ($opt === false || $opt === 'inside') return null;
        
        $html = $this->renderLabel();
        
        foreach ($this->getDecorators() as $decorator) {
            $html = $decorator->renderLabel($this, $html);
        }
        
        return $html;
    }
    
    /**
     * Render the link including layout elements.
     * 
     * @return string
     */
    final public function getField()
    {
        $html = $this->getControl();
        
        foreach ($this->getDecorators() as $decorator) {
            $html = $decorator->renderField($this, $html, $html);
        }
        
        return $html;
    }
    
    /**
     * Get the input control.
     * 
     * @return string
     */
    final public function getControl()
    {
        $html = $this->renderControl();
        
        foreach ($this->getDecorators() as $decorator) {
            $html = $decorator->renderControl($this, $html);
        }
        
        return $html;
    }
    
    
    /**
     * Render the element to HTML
     * 
     * @return string
     */
    final public function render()
    {
        $label = $this->getLabel();
        $field = $this->getField();
        $html = $this->renderContainer($label, $field);
        
        foreach ($this->getDecorators() as $decorator) {
            $html = $decorator->renderContainer($this, $html, $label, $field);
        }
        
        return $html;
    }
    
    /**
     * Render the container.
     * 
     * @param string $label  HTML of the label
     * @param string $field  HTML of the field
     * @return string
     */
    protected function renderContainer($label, $field)
    {
        $html = ($label ? $label . "\n" : '') . $field;
        
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
            . ($this->attr['required'] ? $this->getOption('required-suffix') : '')
            . '</label>';
    }
    
    /**
     * Render the element control to HTML.
     * 
     * @return string
     */
    abstract protected function renderControl();
}
