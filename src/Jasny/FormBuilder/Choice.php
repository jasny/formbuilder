<?php

namespace Jasny\FormBuilder;

/**
 * Representation of a set of radio buttons or checkboxes in a form.
 * 
 * @option selected-first  Put the selected option(s) on top of the list
 * @option single-line     Put all items on a single line
 */
class Choice extends ChoiceControl
{
    /**
     * Render the input control to HTML.
     * 
     * @return string
     */
    protected function renderControl()
    {
        $this->getId();
        $name = $this->getAttr('name');
        $type = $this->getAttr('multiple') ? 'checkbox' : 'radio';

        $selected_first = (boolean)$this->getOption('selected-first');
        $single_line = (boolean)$this->getOption('single-line');
        
        // Build inputs
        $inputs = $inputs_first = [];
        
        foreach ($this->items as $key=>$val) {
            $selected = !is_array($this->value) ? $key == $this->value : in_array($key, $this->value);

            $html_attrs = "type=\"$type\" name=\"" . htmlentities($name) . "\""
                . "value=\"" . htmlentities($key) . "\""
                . ($selected ? ' checked' : '');
            $input = "<label><input $html_attrs> " . htmlentities($val) . "</label>";
            
            if (!$single_line) $input = '<div>' . $input . '</div>';
            
            if ($selected && $selected_first) $inputs_first[] = $input;
             else $inputs[] = $input;
        }
        
        $hidden = $type === 'checkbox' && $this->getOption('checkbox-hidden') ?
            '<input type="hidden" name="' . htmlentities($this->getName()) . '" value="">' . "\n" : '';
        
        // Build html control
        return "<div " . $this->attr->render(['name'=>null, 'multiple'=>null]) . ">\n"
            . $hidden
            . join("\n", array_merge($inputs_first, $inputs)) . "\n"
            . "</div>";
    }
}
