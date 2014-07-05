<?php

namespace Jasny\FormBuilder;

/**
 * Representation of an <input> element in a form.
 * 
 * @option string    type       HTML5 input type
 * @option boolean   multiple   Allow multiple files (only for type="file")
 * @option boolean   hidden     Add hidden input for checkbox
 * @option int|float min        Minimum value
 * @option int|float max        Maximum value
 * @option int       minlength  Minimum string length
 * @option int       maxlength  Maximum string length
 * @option string    pattern    Regexp pattern that value should match
 * @option Control   match      Match value of other element (retype password)
 * 
 * @todo Support multiple file upload
 */
class Input extends Control
{
    /**
     * Class constructor.
     * 
     * @param array  $options  Element options
     * @param array  $attr     HTML attributes
     */
    public function __construct(array $options=[], array $attr=[])
    {
        if (!isset($options['type'])) $options['type'] = 'text';
        $type = $options['type'];
        
        $options += $this->getDefaultOptions($type);
        
        if (!isset($attr['type'])) $attr['type'] = function() {
            return $this->getType();
        };
        
        if ($type === 'checkbox' || $type === 'radio') {
            if (!isset($attr['value'])) $attr['value'] = 1;
            if (!isset($attr['checked'])) $attr['checked'] = function() {
                return (bool)$this->getValue();
            };
        }
        
        if (in_array($type, ['button', 'submit', 'reset']) && !isset($attr['value'])) {
            $attr['value'] = function() {
                return $this->getDescription();
            };
        }
        
        $noPlaceholder = ['hidden', 'button', 'submit', 'reset', 'checkbox', 'radio', 'file'];
        if (!in_array($type, $noPlaceholder) && !isset($attr['placeholder'])) {
            $attr['placeholder'] = function() {
                return $this->getOption('label') ? null : $this->getDescription();
            };
        }
        
        foreach (['min', 'max', 'maxlength', 'pattern'] as $opt) {
            $attr[$opt] = function () use($opt) {
                return $this->getOption($opt);
            };
        }
        
        parent::__construct($options, $attr);
    }
    
    /**
     * Get default options for a specific type
     */
    protected function getDefaultOptions($type)
    {
        $options = [];
        
        switch ($type) {
            case 'hidden':
                if (!isset($this->options['label'])) $options['label'] = false;
                if (!isset($this->options['container'])) $options['container'] = false;
                break;
            
            case 'checkbox':
            case 'radio':
                if (!isset($this->options['label'])) $options['label'] = 'inside';
                break;
                
            case 'button':
            case 'submit':
            case 'reset':
                if (!isset($this->options['label'])) $options['label'] = false;
                break;
        }
        
        return $options;
    }
    
    /**
     * Get HTML5 input type
     * 
     * @return string
     */
    public function getType()
    {
        return $this->getOption('type');
    }
    
    
    /**
     * Validate the input control.
     * 
     * @return boolean
     */
    protected function validate()
    {
        if (!$this->validateRequired()) return false;

        // Empty and not required, means no further validation
        if ($this->getValue() === null || $this->getValue() === '') return true;

        if ($this->getType() === 'file') return $this->validateUpload();
        
        if (!$this->validateType()) return false;
        if (!$this->validateMinMax()) return false;
        if (!$this->validateLength()) return false;
        if (!$this->validatePattern()) return false;

        if (!$this->validateMatch()) return false;
        
        return true;
    }
    

    /**
     * Render the element field to HTML.
     * 
     * @param string $el  HTML element
     * @return string
     */
    public function renderControl($el)
    {
        // Determine default options and attributes
        if ($this->attr['type'] === 'checkbox' && $this->getOption('add-hidden')) {
            $el = '<input type="hidden" value="" ' . $this->attr->renderOnly(['name']) . '>' . "\n" . $el;
        }

        return parent::renderControl($el);
    }

    /**
     * Render the <input>.
     * 
     * @return string
     */
    public function renderElement()
    {
        return "<input {$this->attr}>";
    }
}
