<?php

namespace Jasny\FormBuilder;

use Jasny\FormBuilder\Decorator;

/**
 * Tidy up the HTML.
 * @link http://www.php.net/tidy
 * 
 * Set config option 'deep' to true, to tidy each individual child node.
 */
class Tidy extends Decorator
{
    /**
     * Tidy configuration
     * @var array
     */
    protected $config;
    
    /**
     * Class constructor.
     * 
     * @param array $config  Tidy configuration
     */
    public function __construct(array $config=[])
    {
        $this->config = $config;
    }
    
    /**
     * Wether or not to apply the decorator to all descendants.
     * 
     * @return boolean
     */
    public function isDeep()
    {
        return !empty($this->config['deep']);
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
        $tidy = new \tidy();
        $tidy->parseString($html, $this->config);
        $html = join("\n", $tidy->body()->child);
        
        return $html;
    }
}
