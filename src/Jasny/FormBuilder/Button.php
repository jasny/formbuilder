<?php

namespace Jasny\FormBuilder;

/**
 * Representation of an <button> element in a form.
 * 
 * @option string content      Content displayed within the button
 * @option string escape       HTML entity encode content (default is true)
 * @option string description  Description as displayed on the label
 */
class Button extends Action
{
    /**
     * Render the <button>.
     * 
     * @return string
     */
    protected function renderElement()
    {
        return "<button {$this->attr}>" . $this->getContent() . "</button>";
    }
}
