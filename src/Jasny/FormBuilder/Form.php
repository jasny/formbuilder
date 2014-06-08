<?php

namespace Jasny\FormBuilder;

/**
 * Representation of an HTML <form> with bootstrap elements.
 */
class Form extends Group
{
    /**
     * Class constructor.
     * 
     * @param array $attrs    HTML attributes
     * @param array $options  Element options
     */
    public function __construct(array $attrs=[], array $options=[])
    {
        parent::__construct('form', $attrs + ['method'=>'post'], $options);
    }
    
    
    /**
     * Get unique identifier
     */
    public function getId()
    {
        if (!isset($this->attr['id'])) {
            $this->attr['id'] = isset($this->attr['name']) ?
                $this->attr['name'] . '-form' :
                base_convert(uniqid(), 16, 36);
        }
        
        return $this->attr['id'];
    }
    
    
    /**
     * Check if method matches and apply $_POST or $_GET parameters.
     * 
     * @param boolean $apply  Set values using $_POST or $_GET parameters
     * @return boolean
     */
    public function isSubmitted($apply=true)
    {
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        if ($method != $this->getAttr('method')) return false;
        
        if ($apply) $this->setValues($method === 'GET' ? $_GET : $_POST + $_FILES);
        return true;
    }
}
