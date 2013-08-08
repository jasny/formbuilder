<?php

namespace Jasny\FormBuilder\Bootstrap;
use Jasny\FormBuilder as Base;

/**
 * Representation of an HTML <form> with bootstrap elements.
 */
class Form extends Base\Form
{
    /**
     * Get all options.
     * 
     * @return array
     */
    public function getOptions()
    {
        $form_inline = preg_match('/\b(form-inline|form-search)\b/', $this->getAttr('class'));
        
        return parent::getOptions() + [
            'container' => !$form_inline,
            'label' => !$form_inline
        ];
    }
}
