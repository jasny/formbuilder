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
     * Wether or not to indent each individual child node.
     * @var boolean
     */
    protected $deep = false;
    
    /**
     * Class constructor
     * 
     * @param array $options
     */
    public function __construct(array $options=[])
    {
        if (!class_exists('\Gajus\Dindent\Parser')) throw new Exception("Please add the Dindent library");
        
        if (isset($options['spaces'])) $options += ['indentation_character' => str_repeat(' ', $options['spaces'])];
        $this->options = $options;
        
        $this->deep = !empty($options['deep']);
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
