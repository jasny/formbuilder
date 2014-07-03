<?php

namespace Jasny\FormBuilder;

/**
 * Base class of form control elements.
 */
abstract class Control extends Element
{
    use RenderPartial;
    use BasicValidation;
    
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
        if (isset($options['name'])) $attr['name'] = $options['name'];
        if (isset($options['required'])) $attr['required'] = $options['required'];
        
        if (!isset($options['description']) && isset($attr['name'])) {
            $options['description'] = ucfirst(preg_replace(['/^.+\./', '/[_-]/'], ['', ' '], $attr['name']));
        }
        
        unset($options['name'], $options['required']);
        parent::__construct($options, $attr);
    }
    
    
    /**
     * Set the name of the element.
     * 
     * @param string $name
     * @return Control $this
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
     * @return Control $this
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
