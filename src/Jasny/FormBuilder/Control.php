<?php

namespace Jasny\FormBuilder;

/**
 * Base class of form control elements.
 */
abstract class Control extends Element
{
    /**
     * Control description
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
     * @param array $options      Element options
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
        while ($parent && !$parent instanceof Form) $parent = $parent->getParent();
        
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
     * Set the name of the control.
     * 
     * @param string $name
     * @return Control $this
     */
    public function setName($name)
    {
        return $this->setAttr('name', $name);
    }
    
    /**
     * Get the name of the control.
     * 
     * @return string
     */
    public function getName()
    {
        return $this->getAttr('name');
    }
    
    /**
     * Set the value of the control.
     * 
     * @param mixed $value
     * @return Control $this
     */
    abstract public function setValue($value);
    
    /**
     * Get the value of the control.
     * 
     * @return mixed
     */
    abstract public function getValue();
    
    /**
     * Set the description of the control.
     * 
     * @param string $description
     * @return Control $this
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
    
    /**
     * Get the description of the control.
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
        return parent::getOptions() + ['container'=>true, 'label'=>(boolean)$this->getDescription()];
    }
    

    /**
     * Validate if the control has a value if it's required.
     * 
     * @return boolean
     */
    protected function validateRequired()
    {
        if ($this->getAttr('required')) {
            $value = $this->getValue();
            if ($value === null || $value === '') {
                $this->error = $this->setError($this->getOption('error:required'));
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Validate if min and max for value.
     * 
     * @return boolean
     */
    protected function validateMinMax()
    {
        $value = $this->getValue();
        if ($value instanceof DateTime) $value = $value->format('c');
        
        $min = $this->getAttr('min');
        if (isset($min) && $min !== false && $value < $min) {
            $this->setError($this->getOption('error:min'));
            return false;
        }
        
        $max = $this->getAttr('max');
        if (isset($max) && $max !== false && $value > $max) {
            $this->setError($this->getOption('error:max'));
            return false;
        }
        
        return true;
    }

    /**
     * Validate the length of the value.
     * 
     * @return boolean
     */
    protected function validateLength()
    {
        $value = $this->getValue();
        
        $minlength = $this->getAttr('minlength');
        if (isset($minlength) && $minlength !== false && strlen($value) > $minlength) {
            $this->setError($this->getOption('error:minlength'));
            return false;
        }
        
        $maxlength = $this->getAttr('maxlength');
        if (isset($maxlength) && $maxlength !== false && strlen($value) > $maxlength) {
            $this->setError($this->getOption('error:maxlength'));
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate the value of the control against a regex pattern.
     * 
     * @return boolean
     */
    protected function validatePattern()
    {
        $pattern = $this->getAttr('pattern');
        if ($pattern && !preg_match('/' . str_replace('/', '\/', $pattern) . '/', $this->getValue())) {
            $this->setError($this->getOption('error:pattern'));
            return false;
        }
        
        return true;
    }
    
    /**
     * Match value against another control.
     * 
     * @return boolean
     */
    protected function validateMatch()
    {
        $other = $this->getAttr('match');
        if (!$other) return true;
        
        if (!$other instanceof Control) $other = $this->getForm()->getControl($other);
        
        if ($this->getValue() != $other->getValue()) {
            $this->setError($this->getOption('error:match'));
            return false;
        }
        
        return true;
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
        $this->error = preg_replace_callback('/{(?:(.*)|)?(@|%|\$)(.+?)(?:|(.*))?}/', array($this, 'setErrorPlaceholder'), $error);
    }
    
    /**
     * Callback for setError method.
     * 
     * @param array $match
     * @return string
     */
    protected function setErrorPlaceholder($match)
    {
        if ($match[2] == '@') {
            $val = $this->getAttr($match[3]);
        } elseif ($match[2] == '%') {
            $val = $this->getOption($match[3]);
        } elseif ($match[2] == '$') {
            switch ($match[3]) {
                case 'value': $val = (string)$this->getValue(); break;
                case 'length': $val = strlen($this->getValue()); break;
                case 'desc': $val = $this->desc; break;
                default: trigger_error("Unknown variable '{$match[2]}' in error messge.", E_USER_WARNING); return null;
            }
        }
        
        if ($val === null || $val === '') return null;
        
        return $match[1] . $val . (isset($match[4]) ? $match[4] : '');
    }
    
    
    /**
     * Render the input control to HTML.
     * 
     * @param string $html  HTML of the control
     * @return string
     */
    protected function renderContainer($html)
    {
        $this->getId();

        $options = $this->getOptions();
        $error = $this->getError();
        
        // Build <label>
        if ($options['label'] === 'inside') {
            $html = "<label class=\"" . $this->getAttr('type') . "\">\n"
                . $html . "\n"
                . $this->getDescription() . ($this->getAttr('required') ? $options['required-suffix'] : '') . "\n"
                . "</label>";
        } elseif ($options['label']) {
            $html = "<label for=\"" . $this->getId() . "\">"
                . $this->getDescription() . ($this->getAttr('required') ? $options['required-suffix'] : '')
                . "</label>\n"
                . $html;
        }
        
        // Add error
        if ($error) $html .= "\n<span class=\"error\">{$error}</span>";
        
        // Put everything in a container
        if ($options['container']) {
            $id = $this->getId() . "-container";
            $html = "<div id=\"$id\" class=\"control-container\"" . ($error ? " error" : '') . ">\n{$html}\n</div>";
        }
        
        return $html;
    }
}
