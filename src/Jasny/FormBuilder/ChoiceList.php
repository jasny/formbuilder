<?php

namespace Jasny\FormBuilder;

/**
 * Representation of a set of radio buttons or checkboxes in a form.
 * 
 * @option selected-first  Put the selected option(s) on top of the list
 * @option single-line     Put all items on a single line
 */
class ChoiceList extends Choice
{
    /**
     * Class constructor.
     * 
     * @param array $options  Element options
     * @param array $attr     HTML attributes
     */
    public function __construct(array $options = [], array $attr = [])
    {
        parent::__construct($options, $attr);
        $this->addClass('choicelist');
    }
    
    /**
     * Render the content of the HTML element.
     * 
     * @return string
     */
    protected function renderContent()
    {
        $this->getId();
        $name = $this->getAttr('name');
        $value = $this->getValue();
        $required = $this->getAttr('required');
        $type = $this->getOption('multiple') ? 'checkbox' : 'radio';

        $selected_first = (boolean)$this->getOption('selected-first');
        $single_line = (boolean)$this->getOption('single-line');
        
        // Build inputs
        $inputs = $inputs_first = [];
        
        foreach ($this->items as $key=>$val) {
            $selected = !is_array($value) ? (string)$key === (string)$value : in_array($key, $value);
            
            $html_attrs = 'type="' . $type . '" name="' . htmlentities($name) . '"'
                . 'value="' . htmlentities($key) . '"' . ($selected ? ' checked' : '') . ($required ? ' required' : '');
            $input = "<label><input $html_attrs> " . htmlentities($val) . "</label>";
            
            if (!$single_line) $input = '<div>' . $input . '</div>';
            
            if ($selected && $selected_first) $inputs_first[] = $input;
             else $inputs[] = $input;
        }
        
        $hidden = $type === 'checkbox' && $this->getOption('add-hidden') ?
            '<input type="hidden" name="' . htmlentities($this->getName()) . '" value="">' . "\n" : '';
        
        return $hidden . join("\n", array_merge($inputs_first, $inputs));
    }
    
    /**
     * Render the input control to HTML.
     * 
     * @return string
     */
    public function renderElement()
    {
        if ($this->getOption('single-line')) $this->addClass('choicelist-single-line');
        
        // Build html control
        return "<div " . $this->attr->render(['name'=>null, 'multiple'=>null, 'required'=>null]) . ">\n"
            . $this->getContent() . "\n"
            . "</div>";
    }
}
