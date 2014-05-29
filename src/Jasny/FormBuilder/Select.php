<?php

namespace Jasny\FormBuilder;

/**
 * Representation of a <select> element in a Bootstrap form.
 * 
 * Options
 *  - selected-first  Put the selected option(s) on top of the list
 */
class Select extends ChoiceControl
{
    /**
     * Render the <select>
     * 
     * @return string
     */
    protected function generateControl()
    {
        $selected_first = (boolean)$this->getOption('selected-first');
        
        $opts = $opts_first = [];
        
        foreach ($this->values as $key=>$val) {
            $selected = !is_array($this->value) ? $key == $this->value : in_array($key, $this->value);
            $opt = "<option value=\"" . htmlentities($key) . "\"" . ($selected ? ' selected' : '') . ">"
                . htmlentities($val) . "</option>\n";
            
            if ($selected && $selected_first) $opts_first[] = $opt;
             else $opts[] = $opt;
        }
        
        $html = "<select" . $this->renderAttrs() . ">\n"
            . join("\n", array_merge($opts_first, $opts))
            . "</select>\n";
        
        return $this->renderContainer($html);
    }
}
