<?php

namespace Jasny\FormBuilder\Decorator;

use Jasny\FormBuilder\Decorator;

/**
 * Simple filter decorator
 */
class SimpleFilter extends Decorator
{
    /**
     * @var callable
     */
    protected $callback;

    
    /**
     * Class constructor.
     * 
     * @param callable $callback
     * @param boolean  $deep      Apply filter to children
     */
    public function __construct($callback, $deep=false)
    {
        $this->callback = $callback;
        $this->deep = $deep;
    }
    
    /**
     * Modify the value
     * 
     * @param Element $element
     * @param mixed   $value
     * @return mixed
     */
    public function filter(Element $element, $value)
    {
        return call_user_func($this->callback, $value);
    }
}
