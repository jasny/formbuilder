<?php

namespace Jasny\FormBuilder;

/**
 * Representation of an HTML <fieldset>.
 * 
 * @option legend  <legend> of the fieldset
 */
class Fieldset extends Group
{
    /**
     * @var string
     */
    protected $tagname = 'fieldset';
    
    /**
     * Class constructor.
     * 
     * @param array  $options  Element options
     * @param array  $attr     HTML attributes
     */
    public function __construct(array $options=[], array $attr=[])
    {
        parent::__construct($options, $attr);
    }
    
    /**
     * Set the legend of the fieldset.
     * 
     * @param string $legend
     * @return Boostrap\Fieldset  $this
     */
    public function setLegend($legend)
    {
        $this->setOption('legend', $legend);
        return $this;
    }
    
    /**
     * Get the legend of the fieldset.
     * 
     * @return string
     */
    public function getLegend()
    {
        return $this->getOption('legend');
    }
    
    /**
     * Render the fieldset to HTML.
     * 
     * @return string
     */
    public function open()
    {
        $html = "<fieldset {$this->attr}>";
        if ($this->getLegend()) $html .= "\n<legend>" . $this->getLegend() . "</legend>";
        
        return $html;
    }
}
