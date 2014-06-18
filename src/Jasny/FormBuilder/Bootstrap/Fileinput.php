<?php

namespace Jasny\FormBuilder\Bootstrap;

use Jasny\FormBuilder as Base;

/**
 * Representation of a Jasny Bootstrap file upload widget.
 * 
 * @link http://jasny.github.io/bootstrap/javascript/#fileinput
 */
class Fileinput extends Base\BaseControl
{
    static public $buttons = array(
        'select' => "Select file",
        'change' => "Change",
        'remove' => "Remove"
    );
    
    /**
     * @var string
     */
    protected $value;

    
    /**
     * Class constructor.
     * 
     * @param array $name
     * @param array $description  Description as displayed on the label 
     * @param array $attrs        HTML attributes
     * @param array $options      Control options
     */
    public function __construct($name=null, $description=null, array $attrs=[], array $options=[])
    {
        if (!isset($options['buttons'])) $options['buttons'] = self::$buttons;
        
        parent::__construct($name, $description, $attrs, $options);
        $this->addClass(['fileupload', 'fileupload-new']);
    }
    
    
    /**
     * Get the value of the control.
     * 
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * Set the value of the control.
     * 
     * @param string $value
     * @return Fileinput $this
     */
    public function setValue($value)
    {
        if (is_string($value) && substr($value, 0, 2) == '^;') {
            list(, $name, $type, $size, $tmp_name, $error) = explode(':', $value);
            $tmp_name = ini_get('upload_tmp_dir') . '/' . $tmp_name;
            $value = null;
            
            if (is_uploaded_file($tmp_name)) $value = compact('name', 'type', 'size', 'tmp_name', 'error');
             else trigger_error("'$tmp_name' is not an uploaded file", E_USER_WARNING);
        }
        
        if (is_array($value) && $value['error'] == UPLOAD_ERR_NO_FILE) return;
        
        $this->value = $value;
        
        if (!$this->value || (is_array($value) && $value['error'])) {
            $this->removeClass('fileupload-exists')->addClass('fileupload-new');
        } else {
            $this->removeClass('fileupload-new')->addClass('fileupload-exists');
        }
        
        return $this;
    }
    
    /**
     * Check if a new file is uploaded.
     * 
     * @return boolean
     */
    public function isUploaded()
    {
        return is_array($this->value);
    }

    /**
     * Check if the file is cleared.
     * 
     * @return boolean
     */
    public function isCleared()
    {
        return $this->value === '';
    }
    
    /**
     * Set the name of the element.
     * 
     * @param string $name
     * @return Control $this
     */
    public function setName($name)
    {
        if ($this->getAttr('multiple') && substr($name, -2) != '[]') {
            $name .= '[]';
        }
        
        return $this->setAttr('name', $name);
    }
    
    /**
     * Move (or clear) uploaded file.
     * 
     * @param string $destination  File name, glob expression or directory name
     * @return string  Path to uploaded file
     */
    public function moveUploadedFile($destination)
    {
        if (!$this->isUploaded() && !$this->isCleared()) return;
        
        foreach (glob($destination, GLOB_BRACE) as $file) {
            if (is_file($file)) unlink($file);
        }
        if ($this->isCleared()) return;
        
        if (is_dir($destination)) {
            $destination .= basename($this->value['name']);
        } else {
            $parts = pathinfo($destination);
            $ext = pathinfo($this->value['name'], PATHINFO_EXTENSION);
            $destination = $parts['dirname'] . '/' . $parts['filename'] . '.' . $ext;
        }
        
        if (!file_exists(dirname($destination))) mkdir(dirname($destination), 0775, true);
        
        if (!move_uploaded_file($this->value['tmp_name'], $destination)) return false;
        return $destination;
    }
    
    /**
     * Validate the select control.
     * 
     * @return boolean
     */
    public function validate()
    {
        if (!$this->getOption('basic-validation')) return true;
        
        return
            $this->validateRequired() &&
            $this->validateUpload();
    }
    
    
    /**
     * Render the widget as HTML
     * 
     * @return string
     */
    protected function renderElement()
    {
        $options = $this->getOptions();
        
        $hidden = null;
        $value = null;
        
        if (is_array($this->value) && !$this->value['error']) {
            $hidden = '<input type="hidden" name="' . htmlentities($this->getAttr('name')) . '" '
                . 'value="^;' . htmlentities(join(';', $this->value)) . '">' . "\n";
            $value = htmlentities(basename($this->value['name']));
        } elseif ($this->value) {
            $value = htmlentities(basename($this->value));
        }
        
        $attr_html = $this->attr->render(['name'=>null, 'multiple'=>null]);

        $preview = $this->renderPreview($value, $options);
        $button_select = $this->renderSelectButton($options);
        $button_remove = $this->renderRemoveButton($options);
        
        $html = <<<HTML
<div{$attr_html} data-provides="fileupload">
  {$hidden}<div class="input-append">
    <div class="uneditable-input span3">{$preview}</div>{$button_select}{$button_remove}
  </div>
</div>
HTML;
        
        return $html;
    }
    
    /**
     * Render the preview for existing files
     * 
     * @param string $value
     * @return string
     */
    protected function renderPreview($value)
    {
        return <<<HTML
<i class="icon-file fileupload-exists"></i> <span class="fileupload-preview">$value</span>
HTML;
    }
    
    /**
     * Render the select button
     * 
     * @return string
     */
    protected function renderSelectButton()
    {
        $attr = $this->attr->renderOnly(['name', 'multiple']);
        
        $button_select = htmlentities($this->getOption('select-button'));
        $button_change = htmlentities($this->getOption('change-button'));
        
        return <<<HTML
<span class="btn btn-file"><span class="fileupload-new">$button_select</span><span class="fileupload-exists">$button_change</span><input type="file" $attr /></span> 
HTML;
    }
    
    /**
     * Render the remove button
     * 
     * @return string
     */
    protected function renderRemoveButton()
    {
        $button_remove = htmlentities($this->getOption('remove-button'));
        if (!$button_remove) return null;
        
        return <<<HTML
<button class="btn fileupload-exists" data-dismiss="fileupload">$button_remove</button>
HTML;
    }
}
