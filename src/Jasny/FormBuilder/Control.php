<?php

namespace Jasny\FormBuilder;

/**
 * Base class of form control elements.
 */
abstract class Control extends Element implements FormElement
{
    use Validation;
    
    /**
     * Error message
     * @var string
     */
    protected $error;
    
    
    /**
     * Class constructor.
     * 
     * @param array $options  Element options
     * @param array $attr     HTML attributes
     */
    public function __construct(array $options=[], array $attr=[])
    {
        if (isset($options['name'])) $attr = ['name'=>$options['name']] + $attr;
        
        if (!isset($options['description']) && isset($attr['name'])) {
            $options['description'] = ucfirst(str_replace('_', ' ', $attr['name']));
        }
        
        unset($options['name']);
        parent::__construct($options, $attr);
    }
    
    
    /**
     * Set the name of the element.
     * 
     * @param string $name
     * @return FormElement $this
     */
    public function setName($name)
    {
        return $this->attr['name'] = $name;
    }
    
    /**
     * Return the name of the control.
     * 
     * @return string
     */
    public function getName()
    {
        return preg_replace('/\[\]$/', '', $this->attr['name']);
    }
    
    /**
     * Set the value of the element.
     * 
     * @param mixed $value
     * @return FormElement $this
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
        
        if (!isset($this->options['label']) && empty($this->options['description'])) {
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
    public function setError($error)
    {
        $this->error = trim($this->parse($error));
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
     * Render the control including layout elements.
     * 
     * @return string
     */
    final public function getField()
    {
        $control = $this->getControl();
        $html = $this->renderField($control);
        
        foreach ($this->getDecorators() as $decorator) {
            $html = $decorator->renderField($this, $html, $control);
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
        
        // Add error
        $error = $this->getError();
        if ($error) $html .= "\n<span class=\"error\">{$error}</span>";
        
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
     * Render the element field to HTML.
     * 
     * @param string $control  Control HTML
     * @return string
     */
    protected function renderField($control)
    {
        $html = $control;
        
        $prepend = $this->getOption('prepend');
        if ($prepend) $html = $prepend . ' ' . $html;
        
        $append = $this->getOption('append');
        if ($append) $html = $html . ' ' . $append;

        $label = $this->getOption('label');
        if ($label === 'contain') {
            $html = "<label>\n"
                . $html . "\n"
                . $this->getDescription()
                . ($this->attr['required'] ? $this->getOption('required-suffix') : '')
                . "</label>";
        }
        
        $script = $this->getValidationScript();
        if ($script) $html .= "\n" . $script;
        
        return $html;
    }
    
    /**
     * Render the element control to HTML.
     * 
     * @return string
     */
    abstract protected function renderControl();
    
    
    /**
     * Get a value for a placeholder
     * 
     * @param string $var
     * @return string
     */
    protected function resolvePlaceholder($var)
    {
        // preg_replace callback
        if (is_array($var)) $var = $var[1];
        
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
