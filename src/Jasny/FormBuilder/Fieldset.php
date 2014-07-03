<?php

namespace Jasny\FormBuilder;

/**
 * Representation of an HTML <fieldset>.
 * 
 * @option legend  <legend> of the fieldset
 */
class Fieldset extends Group
{
    /** @var string */
    const TAGNAME = 'fieldset';
    
    /**
     * @var Legend
     */
    protected $legend;
    
    
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
     * Get the legend of the fieldset.
     * 
     * @return string
     */
    public function getLegend()
    {
        if (!isset($this->legend)) {
            $this->legend = new Legend();
        }
        
        return $this->legend->setContent(function() {
            return $this->getOption('legend');
        });
    }
    
    /**
     * Render the fieldset to HTML.
     * 
     * @return string
     */
    public function open()
    {
        return "<fieldset {$this->attr}>" . $this->getLegend();
    }
}
