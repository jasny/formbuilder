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
     * @param array   $config  Tidy configuration
     * @param boolean $deep    Tidy each individual child
     */
    public function __construct(array $config=[], $deep=false)
    {
        $this->config = $config + $this->config;
        $this->deep = $deep;
    }
    
    /**
     * Render to HTML
     * 
     * @param Element $element
     * @param string  $html     Original rendered html
     * @return string
     */
    public function render($element, $html)
    {
        $tidy = tidy_parse_string($html, $this->config);
        $tidy->cleanRepair();
        return join($tidy->body()->child, "\n");
    }
}
