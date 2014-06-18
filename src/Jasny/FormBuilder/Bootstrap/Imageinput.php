<?php

namespace Jasny\FormBuilder\Bootstrap;

/**
 * Representation of a Jasny Bootstrap file upload widget.
 */
class Imageinput extends Fileinput
{
    /**
     * Create base64 encoded image to embed in HTML 
     * 
     * @param string $file
     * @return string
     */
    protected function createInlineImage($file)
    {
        $picture = file_get_contents($file);
        $size = getimagesize($file);

        // base64 encode the binary data, then break it into chunks according to RFC 2045 semantics
        $base64 = chunk_split(base64_encode($picture));
        return '<img src="data:' . $size['mime'] . ';base64,' . "\n" . $base64 . '" ' . $size[3] . ' />';
    }
    
    /**
     * Render the widget as HTML
     * 
     * @param array $options
     * @param array $attr
     * @return string
     */
    protected function renderElement()
    {
        $hidden = null;
        $name = htmlentities($this->getName());
        $attr_html = $this->attr->render(['name'=>null]);
        
        if (is_array($this->value) && !$this->value['error']) {
            $hidden = '<input type="hidden" name="' . $name . '" '
                . 'value="^;' . htmlentities(join(';', $this->value)) . '">' . "\n";
        }

        $preview = $this->renderPreview();
        $button_select = $this->renderSelectButton();
        $button_remove = $this->renderRemoveButton();

        $html = <<<HTML
<div {$attr_html} data-provides="fileinput">
  $preview
  <div>
    $button_select
    $button_remove
  </div>
</div>
HTML;
        
        return $html;
    }    

    /**
     * Render image preview
     * 
     * @return $html
     */
    protected function renderPreview()
    {
        $holder = $this->getOption('holder');
        
        if (is_array($this->value)) {
            $image = $this->value['error'] ? null : $this->createInlineImage($this->value['tmp_name']);
        } else {
            $image = '<img src="' . htmlentities($this->value) . '">';
        }
        
        if ($holder) {
            $html = '<div class="fileinput-new thumbnail" data-trigger="fileinput" >' . $holder . '</div>' . "\n"
                . '<div class="fileinput-exists fileinput-preview thumbnail">' . $image . '</div>';
        } else {
            $html = '<div class="fileinput-preview thumbnail" data-trigger="fileinput" >' . $image . '</div>';
        }
        
        return $html;
    }
}
