<?php

namespace Jasny\FormBuilder;

/**
 * Representation of a <select> element.
 * 
 * Options
 *  - selected-first  Put the selected option(s) on top of the list
 */
class Select extends ChoiceControl
{
    /**
     * Render the content of the HTML element.
     * 
     * @return string
     */
    protected function renderContent()
    {
        $value = $this->getValue();
        $selected_first = (boolean)$this->getOption('selected-first');
        
        $opts = $opts_first = [];

        $placeholder = $this->getOption('placeholder');
        if ($placeholder) {
            $selected = !isset($value) || $value === '';
            $opts_first[] = "<option value=\"\"" . ($selected ? ' selected' : '') . " disabled>"
                . htmlentities($placeholder) . "</option>\n";
        }
        
        foreach ($this->items as $key=>$val) {
            $selected = !is_array($value) ? $key == $value : in_array($key, $value);
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
    protected function renderElement()
    {
        return "<select {$this->attr}>\n" . $this->getContent() . "\n</select>";
    }
}
