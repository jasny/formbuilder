<?php

namespace Jasny\FormBuilder;

/**
 * Representation of an <button> element in a form.
 * 
 * @option html Description is HTML so don't escape
 */
class Button extends Element
{
    /**
     * FormElement description
     * @var string
     */
    protected $description = 'submit';
    
    /**
     * Class constructor.
     * 
     * @param array $description  Description as displayed on the label 
     * @param array $attr         HTML attributes
     * @param array $options      FormElement options
     */
    public function __construct($description=null, array $attr=[], array $options=[])
    {
        parent::__construct($attr, $options);
        if (isset($description)) $this->description = $description;
    }
    
    
    /**
     * Set the description of the element.
     * 
     * @param string $description
     * @return FormElement $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
    
    /**
     * Get the description of the element.
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    
    /**
     * Standard validation for the element
     * 
     * @return boolean
     */
    protected function validate()
    {
        return true;
    }
    
    
    /**
     * Render the element to HTML
     * 
     * @return string
     */
    final public function render()
    {
        $control = $this->getControl();
        $html = $this->renderContainer($control);
        
        foreach ($this->getDecorators() as $decorator) {
            $html = $decorator->renderContainer($this, $html, null, $control);
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
     * Render the container.
     * 
     * @param string $field  HTML of the field
     * @return string
     */
    protected function renderContainer($field)
    {
        return $this->getOption('container') ? "<div>\n{$field}\n</div>" : $field;
    }
    
    /**
     * Render the <input>.
     * 
     * @return string
     */
    protected function renderControl()
    {
        $description = $this->getDescription();
        if (empty($this->options['html'])) $description = htmlentities($description);
        
        return "<button {$this->attr}>$description</button>";
    }
}
