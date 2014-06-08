<?php

namespace Jasny\FormBuilder;

/**
 * Representation of an HTML <form> with bootstrap elements.
 */
class Form extends Group
{
    /**
     * The HTML tag name.
     * @var string
     */
    protected $tagname = 'form';

    
    /**
     * Class constructor.
     * 
     * @param array $attrs    HTML attributes
     * @param array $options  Element options
     */
    public function __construct(array $attrs=array(), array $options=array())
    {
        $attrs += ['method'=>'post'];
        parent::__construct($attrs, $options);
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
     * Get all options.
     * 
     * @return array
     */
    public function getOptions()
    {
        return parent::getOptions() + ['container'=>true, 'label'=>true];
    }
    
    
    /**
     * Check if method matches and apply $_POST or $_GET parameters.
     * 
     * @param boolean $apply  Set values using $_POST or $_GET parameters
     * @return boolean
     */
    public function isSubmitted($apply=true)
    {
        if ($_SERVER['REQUEST_METHOD'] != $this->getAttr('method')) return false;
        
        if ($apply) $this->setValues($_SERVER['REQUEST_METHOD'] === 'GET' ? $_GET : $_POST + $_FILES);
        return true;
    }
}
