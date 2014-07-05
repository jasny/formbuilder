<?php

namespace Jasny\FormBuilder;

/**
 * Base class of form control elements.
 * 
 * @option description
 * @option required         Value is required
 * @option required-suffix  Suffix added to label if required
 * @option validate         Perform server side validation (default true)
 * @option container        Element type for container
 * @option label            Display a label (true, false or 'inside')
 */
abstract class Control extends Element
{
    use RenderPartial;
    use BasicValidation;
    
    /**
     * Control value
     * @var string
     */
    protected $value;
    
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
        if (!isset($attr['name'])) $attr['name'] = function() {
            return $this->getName() . ($this->getOption('multiple') ? '[]' : '');
        };
        
        if (!isset($attr['value'])) $attr['value'] = function() {
            return $this->getValue();
        };
        
        if (!isset($attr['required'])) $attr['required'] = function() {
            return $this->getOption('required');
        };
        
        parent::__construct($options, $attr);
    }
    
    
    /**
     * Set the value of the element.
     * 
     * @param mixed $value
     * @return Control $this
     */
    public function setValue($value)
    {
        foreach ($this->getDecorators() as $decorator) {
            $value = $decorator->filter($this, $value);
        }
        
        return $this->value = $value;
    }
    
    /**
     * Get the value of the element.
     * 
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
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
     * Get the error message (after validation).
     * 
     * @return string
     */
    public function getError()
    {
        return $this->error;
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
