<?php

namespace Jasny\FormBuilder\Decorator;

use Jasny\FormBuilder\Decorator;

/**
 * Tidy up the HTML.
 * @link http://www.php.net/tidy
 */
class Tidy extends Decorator
{
    /**
     * Wether or not to tidy each individual child node.
     * @var boolean
     */
    protected $deep;
    
    /**
     * Tidy configuration
     * @var array
     */
    protected $config = [
        'doctype' => 'omit',
        'output-html' => true,
        'show-body-only' => true,
    ];
    
    /**
     * Class constructor.
     * 
     * @param array $config  Tidy configuration
     * @param boolean $deep  Wether or not to tidy each individual child node.
     */
    public function __construct(array $config=[], $deep=false)
    {
        $this->config = $config + $this->config;
        $this->deep = $deep;
    }
    
    /**
     * Wether or not to tidy each individual child node.
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
        $tidy = tidy_parse_string($html, $this->config);
        $tidy->cleanRepair();
        return join($tidy->body()->child, "\n");
    }
}
