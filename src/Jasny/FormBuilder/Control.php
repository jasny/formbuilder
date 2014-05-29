<?php

namespace Jasny\FormBuilder;

/**
 * Base class of form control elements.
 * 
 * @internal The generate* functions generate the HTML of the control.
 * @internal The render* functions apply decoration to the generated HTML.
 * @internal The get* functions are the public methods to get a subcomponent.
 */
abstract class Control extends Node implements Element
{
    use Validation;
    
    /**
     * Element description
     * @var string
     */
    protected $description;
    
    /**
     * Error message
     * @var string
     */
    protected $error;
    
    
    /**
     * Class constructor.
     * 
     * @param array $name
     * @param array $description  Description as displayed on the label 
     * @param array $attrs        HTML attributes
     * @param array $options      Node options
     */
    public function __construct($name=null, $description=null, array $attrs=[], array $options=[])
    {
        if (isset($name)) $attrs = compact('name') + $attrs;
        $this->setDescription($description ?: ucfirst(str_replace('_', ' ', $name)));
        
        parent::__construct($attrs, $options);
    }
    
    
    /**
     * Get the form to wich this control is added.
     * 
     * @return Form
     */
    public function getForm()
    {
        $parent = $this->getParent();
        while ($parent && !$parent instanceof Form) {
            $parent = $parent->getParent();
        }
        
        return $parent;
    }

    /**
     * Get control identifier.
     * 
     * @return string
     */
    public function getId()
    {
        $id = $this->getAttr('id');
        if ($id) return $id;
        
        $name = $this->getName();
        $id = ($name ?: base_convert(uniqid(), 16, 32));
        if ($this->getForm()) $id = $this->getForm()->getId() . '-' . $id;
        
        $this->setAttr('id', $id);
        return $id;
    }

    /**
     * Set the name of the element.
     * 
     * @param string $name
     * @return Element $this
     */
    public function setName($name)
    {
        return $this->setAttr('name', $name);
    }
    
    /**
     * Return the name of the control.
     * 
     * @return string
     */
    public function getName()
    {
        return preg_replace('/\[\]$/', '', $this->getAttr('name'));
    }
    
    /**
     * Set the value of the element.
     * 
     * @param mixed $value
     * @return Element $this
     */
    abstract public function setValue($value);
    
    /**
     * Get the value of the element.
     * 
     * @return mixed
     */
    abstract public function getValue();
    
    /**
     * Set the description of the element.
     * 
     * @param string $description
     * @return Element $this
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
     * Get all options.
     * 
     * @return array
     */
    public function getOptions()
    {
        $options = parent::getOptions() + ['container' => true];
        
        if (!isset($this->options['label']) && !$this->getDescription()) {
            $options['label'] = false;
        }
        
        return $options;
    }

    
    /**
     * Get the error message (after validation).
     * 
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }
    
    /**
     * Set the error message.
     * 
     * @param string $error  The error message
     * @return string
     */
    protected function setError($error)
    {
        $this->error = trim($this->parse($error));
    }
    
    
    /**
     * Get the label as HTML
     * 
     * @return string
     */
    final public function getLabel()
    {
        return $this->renderLabel($this->getAttr(), $this->getOptions());
    }

    /**
     * Get the element as HTML
     * 
     * @return string
     */
    final public function getControl()
    {
        return $this->renderControl($this->getAttr(), $this->getOptions());
    }

    /**
     * Get the inner HTML
     * 
     * @return string
     */
    final public function getField()
    {
        return $this->renderField($this->getAttr(), $this->getOptions());
    }

    
    /**
     * Render the element
     * 
     * @return string
     */
    public function render()
    {
        $attr = $this->getAttr();
        $options = $this->getOptions();
        
        $innerHtml = $this->renderField($attr, $options);
        $this->renderContainer($innerHtml, $attr, $options);
    }
    
    /**
     * Render + decorate the label
     * 
     * @param array $attr
     * @param array $options
     * @return string
     */
    final protected function renderLabel($attr, $options)
    {
        $html = $this->generateLabel($options, $attr);
        
        foreach ($this->getDecorators() as $decorator) {
            $html = $decorator->renderLabel($this, $html, $attr, $options);
        }
        
        return $html;
    }
    
    /**
     * Render + decorate the element
     * 
     * @param array $attr
     * @param array $options
     * @return string
     */
    final protected function renderControl($attr, $options)
    {
        $html = $this->generateControl($options, $attr);
        
        foreach ($this->getDecorators() as $decorator) {
            $html = $decorator->renderControl($this, $html, $attr, $options);
        }
        
        return $html;
    }
    
    /**
     * Render + decorate the element to inner HTML
     * 
     * @param array $attr
     * @param array $options
     * @return string
     */
    final protected function renderField($attr, $options)
    {
        $html = $this->generateField($options, $attr);
        
        foreach ($this->getDecorators() as $decorator) {
            $html = $decorator->renderField($this, $html, $attr, $options);
        }
        
        return $html;
    }
    
    /**
     * Render + decorate the container
     * 
     * @param string $innerHtml  HTML of the element
     * @param array  $attr
     * @param array  $options
     * @return string
     */
    final protected function renderContainer($innerHtml, $attr, $options)
    {
        $html = $this->generateContainer($innerHtml, $options, $attr);
        
        foreach ($this->getDecorators() as $decorator) {
            $html = $decorator->renderContainer($this, $innerHtml, $html, $attr, $options);
        }
        
        return $html;
    }
    
    
    /**
     * Render the label.
     * 
     * @param array $attr
     * @param array $options
     * @return string
     */
    protected function generateLabel($attr, $options)
    {
        return "<label for=\"" . $this->getId() . "\">"
            . $this->getDescription()
            . (!empty($attr['required']) ? $options['required-suffix'] : '') . "\n"
            . "</label>\n";
    }
    
    /**
     * Render the element control to HTML.
     * 
     * @param array $attr
     * @param array $options
     * @return string
     */
    abstract protected function generateControl($attr, $options);

    /**
     * Render the element field to HTML.
     * 
     * @param array $attr
     * @param array $options
     * @return string
     */
    protected function generateField($attr, $options)
    {
        $html = $this->renderControl($attr, $options);
        
        if (!empty($options['prepend'])) {
            $html = $this->parse($options['prepend']) . ' ' . $html;
        }
        
        if (!empty($options['append'])) {
            $html = $html . ' ' . $this->parse($options['append']);
        }

        if (isset($options['label']) && $options['label'] === 'contain') {
            $html = "<label>\n"
                . $html . "\n"
                . $this->getDescription()
                . (!empty($attr['required']) ? $options['required-suffix'] : '') . "\n"
                . "</label>";
        }
        
        $script = $this->getValidationScript();
        if ($script) $html .= "\n" . $script;
        
        return $html;
    }

    /**
     * Render the container.
     * 
     * @param string $innerHtml  HTML of the element
     * @param array  $attr
     * @param array  $options
     * @return string
     */
    protected function generateContainer($innerHtml, $attr, $options)
    {
        $html = $innerHtml;

        $label = $this->renderLabel($attr, $options);
        if ($label) $html = $label . "\n" . $html;
        
        // Add error
        $error = $this->getError();
        if ($error) $html .= "\n<span class=\"error\">{$error}</span>";
        
        // Put everything in a container
        if ($options['container']) {
            $id = $this->getId() . "-container";
            $html = "<div id=\"$id\">\n{$html}\n</div>";
        }
        
        return $html;
    }
    

    /**
     * Get a value for a placeholder
     * 
     * @param string $var
     * @return string
     */
    protected function resolvePlaceholder($var)
    {
        // preg_replace callback
        if (is_array($var)) {
            $var = $var[1];
        }
        
        switch ($var) {
            case 'value':
                $var = (string)$this->getValue();
                break;

            case 'length':
                $var = strlen($this->getValue());
                break;

            case 'desc':
                $var = $this->getDescription();
                break;
        }
        
        return parent::resolvePlaceholder($var);
    }
}
