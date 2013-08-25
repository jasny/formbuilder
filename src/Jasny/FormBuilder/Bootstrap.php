<?php

namespace Jasny\FormBuilder;

/**
 * Render element for use with Bootstrap
 */
trait Bootstrap
{
    /**
     * Get all options.
     * 
     * @return array
     */
    public function getOptions()
    {
        if ($this instanceof Form) {
            $form_inline = preg_match('/\b(form-inline|form-search)\b/', $this->getAttr('class'));

            return parent::getOptions() + [
                'container' => !$form_inline,
                'label' => !$form_inline
            ];
        }
        
        return parent::getOptions();
    }
    
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
        if (isset($options['label']) && $options['label'] === 'inside') {
            $html = "<label class=\"" . $this->getAttr('type') . "\">\n"
                . $html . "\n"
                . $this->getDescription() . ($this->getAttr('required') ? $options['required-suffix'] : '') . "\n"
                . "</label>";
        } elseif (!empty($options['label'])) {
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
    
    
    /**
     * Factory method
     * 
     * @param string $element
     * @param array  $args     Constructor arguments
     */
    public static function build($element, array $args=[])
    {
        $class =  __CLASS__ . '\\' . str_replace(' ', '', ucwords(str_replace('-', ' ', $element)));
        if (!class_exists($class)) return Element::build($element, $args);
        
        $refl = new \ReflectionClass($class);
        return $refl->newInstanceArgs($args);
    }
}
