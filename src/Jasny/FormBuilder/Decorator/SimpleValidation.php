<?php

namespace Jasny\FormBuilder\Decorator;

use Jasny\FormBuilder\Decorator;

/**
 * Simple validation decorator
 */
class SimpleValidation extends Decorator
{
    /**
     * @var callable
     */
    protected $callback;

    
    /**
     * Class constructor.
     * 
     * @param callable $callback
     */
    public function __construct($callback)
    {
        $this->callback = $callback;
        $this->deep = false;
    }
    
    /**
     * Modify the value
     * 
     * @param Element $element
     * @param mixed   $isValid
     * @return mixed
     */
    public function validation(Element $element, $isValid)
    {
        if (!$isValid) return false;
        
        $message = null;
        $isValid = call_user_func($this->callback, $value, $message);
        
        if (!$isValid) $element->setError($message);
        return $isValid;
    }
}
