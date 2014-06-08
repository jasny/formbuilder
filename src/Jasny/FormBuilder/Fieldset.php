<?php

namespace Jasny\FormBuilder;

/**
 * Representation of an HTML <fieldset>.
 */
class Fieldset extends Group
{
    /**
     * <legend> of the fieldset
     * @var string
     */
    protected $legend;
    
    /**
     * Class constructor.
     * 
     * @param string $legend 
     * @param array  $attr     HTML attributes
     * @param array  $options  FormElement options
     */
    public function __construct($legend=null, array $attr=[], array $options=[])
    {
        if (isset($legend)) $this->legend = $legend;
        parent::__construct('fieldset', $attr, $options);
    }
    
    /**
     * Set the legend of the fieldset.
     * 
     * @param string $legend
     * @return Boostrap\Fieldset  $this
     */
    public function setLegend($legend)
    {
        $this->legend = $legend;
        return $this;
    }
    
    /**
     * Get the legend of the fieldset.
     * 
     * @return string
     */
    public function getLegend()
    {
        return $this->legend;
    }
    
    /**
     * Render the fieldset to HTML.
     * 
     * @return string
     */
    public function open()
    {
        $html = "<fieldset {$this->attr}>";
        if (isset($this->legend)) $html .= "\n<legend>" . $this->legend . "</legend>";
        
        return $html;
    }
}
