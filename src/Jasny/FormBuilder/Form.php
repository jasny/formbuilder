<?php

namespace Jasny\FormBuilder;

/**
 * Representation of an HTML5 <form>.
 * 
 * @option method  Form method attribute
 * @option action  Form action attribute
 */
class Form extends Group
{
    /** @var string */
    const TAGNAME = 'form';

    
    /**
     * Class constructor.
     * 
     * @param array $options  Element options
     * @param array $attr     HTML attributes
     */
    public function __construct(array $options=[], array $attr=[])
    {
        if (isset($options['method'])) $attr['method'] = $options['method'];
        $attr += ['method'=>'post'];
        
        if (isset($options['action'])) $attr['action'] = $options['action'];
        
        unset($options['method'], $options['action']);
        parent::__construct($options, $attr);
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
