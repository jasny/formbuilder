<?php

namespace Jasny\FormBuilder;

/**
 * Representation of a <select> element.
 * 
 * @option string placeholder  First <option> with empty value
 */
class Select extends Choice
{
    /**
     * Class constructor.
     * 
     * @param array $options  Element options
     * @param array $attr     HTML attributes
     */
    public function __construct(array $options=[], array $attr=[])
    {
        if (!isset($attr['multiple'])) $attr['multiple'] = function() {
            return (boolean)$this->getOption('multiple');
        };
        
        return parent::__construct($options, $attr);
    }
    
    
    /**
     * Render the content of the HTML element.
     * 
     * @return string
     */
    protected function renderContent()
    {
        $items = $this->getItems();
        $value = $this->getValue();
        $selected_first = (boolean)$this->getOption('selected-first');
        
        $opts = $opts_first = [];

        $placeholder = $this->getOption('placeholder');
        if ($placeholder !== false) {
            $selected = !isset($value) || $value === '';
            $opts_first[] = "<option value=\"\"" . ($selected ? ' selected' : '') . " disabled>"
                . htmlentities($placeholder) . "</option>\n";
        }
        
        foreach ($items as $key=>$val) {
            $selected = !is_array($value) ? (string)$key === (string)$value : in_array($key, $value);
            $opt = "<option value=\"" . htmlentities($key) . "\"" . ($selected ? ' selected' : '') . ">"
                . htmlentities($val) . "</option>\n";
            
            if ($selected && $selected_first) $opts_first[] = $opt;
             else $opts[] = $opt;
        }
        
        return join("\n", array_merge($opts_first, $opts));
    }
    
    /**
     * Render the <select>
     * 
     * @return string
     */
    public function renderElement()
    {
        return "<select {$this->attr}>\n" . $this->getContent() . "\n</select>";
    }
}
