<?php

namespace Jasny\FormBuilder;

/**
 * Representation of an HTML <fieldset>.
 */
class Fieldset extends Group
{
    /**
     * The HTML tag name.
     * @var string
     */
    protected $tagname = 'fieldset';
    
    /**
     * <legend> of the fieldset
     * @var string
     */
    protected $legend;
    
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
    protected function render()
    {
        $html = "<fieldset" . $this->renderAttrs() . ">\n";
        if (isset($this->legend)) $html .= "<legend>" . $this->legend . "</legend>\n";
        
        foreach ($this->elements as $element) {
            $html .= (string)$element . "\n";
        }
        
        $html .= "</fieldset>";
        
        return $html;
    }
}
