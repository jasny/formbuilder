<?php

namespace Jasny\FormBuilder\Bootstrap;
use Jasny\FormBuilder as Base;

/**
 * Representation of a Jasny Bootstrap file upload widget.
 */
class FileUpload extends Base\Control
{
    use Base\Boostrap;
    
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
     * @param array $options      Element options
     */
    public function __construct($name=null, $description=null, array $attrs=[], array $options=[])
    {
        if (!isset($options['buttons'])) $options['buttons'] = self::$buttons;
        
        parent::__construct($name, $description, $attrs, $options);
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
     * @return Boostrap/Control $this
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
     * Return the name of the control.
     * 
     * @return string
     */
    public function getName()
    {
        return preg_replace('/\[\]$/', '', $this->getAttr('name'));
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
     * Get all HTML attributes.
     * 
     * @param boolean $cast  Cast to a string
     * @return array
     */
    public function getAttrs($cast=true)
    {
        $attrs = parent::getAttrs($cast);
        $attrs['class'] = (empty($attrs['class']) ? '' : $attrs['class'] . ' ')
            . 'fileupload fileupload-' . ($this->value ? 'exists' : 'new');

        return $attrs;
    }
    
    
    /**
     * Validate the select control.
     * 
     * @return boolean
     */
    public function isValid()
    {
        if (!$this->validateRequired()) return false;
        if (!$this->validateUpload()) return false;

        return true;
    }

    /**
     * Check if there ware upload errors.
     * 
     * @return boolean
     */
    protected function validateUpload()
    {
        // No error
        if (!is_array($this->value) || !$this->value['error']) return true;
        
        // An error
        $errors = $this->getOption('error:upload');
        $this->setError($errors[$this->value['error']]);
        return false;
    }
    
    
    /**
     * Render the widget as HTML
     * 
     * @return string
     */
    protected function render()
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
        
        $name = htmlentities($this->getAttr('name'));
        $attr_html = $this->renderAttrs(['name'=>null]);

        $button_select = htmlentities($options['buttons']['select']);
        $button_change = htmlentities($options['buttons']['change']);
        $button_remove = htmlentities($options['buttons']['remove']);
        
        $html = <<<HTML
<div{$attr_html} data-provides="fileupload">
  <div class="input-append">
    <div class="uneditable-input span3"><i class="icon-file fileupload-exists"></i> <span class="fileupload-preview">$value</span></div><span class="btn btn-file"><span class="fileupload-new">$button_select</span><span class="fileupload-exists">$button_change</span><input type="file" name="$name" /></span><button class="btn fileupload-exists" data-dismiss="fileupload">$button_remove</button>
  </div>
</div>
HTML;
        
        return $html;
    }
}
