<?php

namespace Jasny\FormBuilder;

/**
 * Hidden input
 */
class Hidden extends Input
{
    /**
     * Class constructor.
     * 
     * @param array $name
     * @param array $attr     HTML attributes
     * @param array $options  FormElement options
     */
    public function __construct($name=null, array $attr=[], array $options=[])
    {
        $attr += ['type' => 'hidden'];
        parent::__construct($name, null, $attr, $options);
    }
}
