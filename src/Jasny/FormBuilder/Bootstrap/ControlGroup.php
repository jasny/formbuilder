<?php

namespace Jasny\FormBuilder\Bootstrap;

/**
 * Use a Bootstrap control-group as container for a control
 */
trait ControlGroup
{
    /**
     * Render the input control to HTML.
     * 
     * @return string
     */
    protected function renderContainer($html)
    {
        $this->getId();

        $options = $this->getOptions();
        $error = $this->getError();
        
        // Build <label>
        if ($options['label'] === 'inside') {
            $html = "<label class=\"" . $this->getAttr('type') . "\">\n"
                . $html . "\n"
                . $this->getDescription() . ($this->getAttr('required') ? $options['required-suffix'] : '') . "\n"
                . "</label>";
        } elseif ($options['label']) {
            $class = ($options['container'] ? ' class="control-label"' : '');
            $label = "<label{$class} for=\"" . $this->getId() . "\">"
                . $this->getDescription() . ($this->getAttr('required') ? $options['required-suffix'] : '')
                . "</label>\n";
        }

        if ($error) $html .= "<span class=\"help-inline\">{$error}</span>";
        if (!empty($options['help-block'])) $html .= "\n<div class=\"help-block\">{$options['help-block']}</div>";
        if ($options['container']) $html = "<div class=\"controls\">\n{$html}\n</div>";
        if (isset($label)) $html = $label . $html;
        
        // Build HTML
        if ($options['container']) {
            $html = "<div class=\"control-group" . ($error ? " error" : '') . "\">\n{$html}\n</div>";
        } elseif ($error) {
            $html = "<span class=\"control-group error\">\n{$html}\n</span>";
        }
        
        return $html;
    }
}