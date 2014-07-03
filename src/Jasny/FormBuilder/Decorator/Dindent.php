<?php

namespace Jasny\FormBuilder\Decorator;

use Jasny\FormBuilder\Decorator;

/**
 * Indent the HTML.
 * @link https://github.com/gajus/dindent
 * 
 * @param int     spaces                 The number of spaces.
 * @param array   indentation_character  Specify the indentation char(s), use instead of spaces.
 * @param boolean deep                   Wether or not to indent each individual child node.
 */
class Dindent extends Decorator
{
    /**
     * Dindent options
     * @var array
     */
    protected $options;
    
    
    /**
     * Class constructor
     * 
     * @param array   $options
     * @param boolean $deep    Indent each individual child
     */
    public function __construct(array $options=[], $deep=false)
    {
        if (!class_exists('\Gajus\Dindent\Parser')) throw new Exception("Please add the Dindent library");

        if (isset($options['spaces'])) $options += ['indentation_character' => str_repeat(' ', $options['spaces'])];
        $this->options = $options;
        
        $this->deep = $deep;
    }
    
    /**
     * Render to HTML
     * 
     * @param Element   $element
     * @param string $html  Original rendered html
     * @return string
     */
    public function render($element, $html)
    {
        $parser = new \Gajus\Dindent\Parser($this->options);
        return $parser->indent($html);
    }
}
