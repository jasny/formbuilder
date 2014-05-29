<?php

namespace Jasny\FormBuilder;

/**
 * Representation of an HTML <a>.
 * 
 * @option boolean container  Wrap <a> in a <div>
 * @option boolean html       Consider content as HTML, so don't escape html entites
 */
class Link extends Element
{
    /**
     * Link text
     * @var string 
     */
    protected $text;
    
    /**
     * Element options 
     * @var array
     */
    protected $options = ['html'=>false];
    

    /**
     * Class constructor.
     * 
     * @param string $text     Link text
     * @param array  $attrs    HTML attributes
     * @param array  $options  Element options
     */
    public function __construct($text, array $attrs=[], array $options=[])
    {
        $this->text = $text;
        parent::__construct($attrs, $options);
    }
    
    /**
     * Set the text of the link.
     * 
     * @param string $text
     * @return Link $this
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }
    
    /**
     * Get the text of the link.
     * 
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Get all options.
     * 
     * @return array
     */
    public function getOptions()
    {
        return parent::getOptions() + ['container'=>true];
    }
    
    /**
     * Validate the element.
     * 
     * @return boolean
     */
    public function validation()
    {
        return true;
    }
    
    /**
     * Render the link to HTML.
     * 
     * @return string
     */
    protected function render()
    {
        $options = $this->getOptions();
        
        $content = $options['html'] ? $this->getText() : htmlentities($this->getText());
        $html = '<a' . $this->renderAttrs() . '>' . $content . '</a>';
        
        if ($options['container']) {
            $id_attr = $this->getAttr('id') ? ' id="' . $this->getAttr('id') . '-container"' : '';
            $html = "<div{$id_attr} class=\"control-container\">\n{$html}\n</div>";
        }
        
        return $html;
    }
}
