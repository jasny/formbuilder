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
    /** @var string */
    const TAGNAME = 'button';
}
