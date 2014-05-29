<?php

namespace Jasny\FormBuilder;

/**
 * Representation of a set of radio buttons or checkboxes in a form.
 * 
 * Options
 *  - selected-first  Put the selected option(s) on top of the list
 */
class Choice extends ChoiceControl
{
    /**
     * Get all HTML attributes.
     * 
     * @param boolean $cast  Cast to a string
     * @return array
     */
    public function getAttrs($cast=true)
    {
        $attrs = parent::getAttrs($cast);
        $attrs['class'] = (empty($attrs['class']) ? '' : $attrs['class'] . ' ') . 'input-group';

        return $attrs;
    }
    
    /**
     * Render the input control to HTML.
     * 
     * @return string
     */
    protected function generateControl()
    {
        $this->getId();
        $name = $this->getAttr('name');
        $type = $this->getAttr('multiple') ? 'checkbox' : 'radio';

        $selected_first = (boolean)$this->getOption('selected-first');
        
        // Build inputs
        $inputs = $inputs_first = [];
        
        foreach ($this->values as $key=>$val) {
            $selected = !is_array($this->value) ? $key == $this->value : in_array($key, $this->value);

            $html_attrs = "type=\"$type\" name=\"" . htmlentities($name) . "\""
                . "value=\"" . htmlentities($key) . "\""
                . ($selected ? ' checked' : '');
            $input = "<label><input $html_attrs> " . htmlentities($val) . "</label>\n";
            
            if ($selected && $selected_first) $inputs_first[] = $input;
             else $inputs[] = $input;
        }
        
        // Build html control
        return "<div" . $this->renderAttrs(['name'=>null, 'multiple'=>null]) . ">\n"
            . "<input type=\"hidden\" name=\"" . htmlentities($this->getName()) . "\" value=\"\">\n"
            . join("\n", array_merge($inputs_first, $inputs))
            . "</div>\n";
    }
}
