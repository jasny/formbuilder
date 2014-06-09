<?php

namespace Jasny\FormBuilder;

/**
 * Base class for a link or button.
 * 
 * @option string url          The href attribute
 * @option string content      Content displayed within the button
 * @option string escape       HTML entity encode content (default is true)
 * @option string description  Description as displayed on the label
 */
class Hyperlink extends Action
{
    /**
     * Class constructor.
     * 
     * @param array $options  Element options
     * @param array $attr     HTML attributes
     */
    public function __construct(array $options=[], array $attr=[])
    {
        if (isset($options['url'])) $attr['href'] = $options['url'];
        
        unset($options['url']);
        parent::__construct($options, $attr);
    }
    
    /**
     * Set the URL of the link
     * 
     * @param string $url
     * @return Hyperlink $this
     */
    public function setUrl($url)
    {
        $this->attr['href'] = $url;
        return $this;
    }
    
    /**
     * Get the URL of the link
     * 
     * @return string
     */
    public function getUrl()
    {
        return $this->attr['href'];
    }
    
    /**
     * Render the link to HTML.
     * 
     * @return string
     */
    protected function renderElement()
    {
        return "<a {$this->attr}>" . $this->getContent() . "</a>";
    }
}
