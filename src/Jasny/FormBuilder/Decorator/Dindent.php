<?php

namespace Jasny\FormBuilder\Decorator;

use Jasny\FormBuilder\Decorator;

/**
 * Indent the HTML.
 * @link https://github.com/gajus/dindent
 */
class Dindent extends Decorator
{
    /**
     * Dindent options
     * @var array
     */
    protected $options;
    
    /**
     * Wether or not to indent each individual child node.
     * @var boolean
     */
    protected $deep;
    
    /**
     * Class constructor.
     * 
     * @param int     $spaces   The number of spaces
     * @param array   $options  Dindent options
     * @param boolean $deep     Wether or not to indent each individual child node.
     */
    public function __construct($spaces=null, array $options=[], $deep=false)
    {
        if (isset($spaces)) $options['indentation_character'] = str_repeat(' ', $spaces);
        
        $this->options = $options;
        $this->deep = $deep;
    }
    
    /**
     * Wether or not to indent each individual child node.
     * 
     * @return boolean
     */
    public function isDeep()
    {
        return $this->deep;
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
