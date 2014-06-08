<?php

namespace Jasny\FormBuilder;

/**
 * Hidden input
 */
class Hidden extends Input
{
    /**
     * HTML attributes
     * @var Attr
     */
    public $attr = ['type' => 'hidden'];
    
    /**
     * Class constructor.
     * 
     * @param array $name
     * @param array $attr     HTML attributes
     * @param array $options  FormElement options
     */
    public function __construct($name=null, array $attr=[], array $options=[])
    {
        parent::__construct($name, null, $attr, $options);
    }
}
