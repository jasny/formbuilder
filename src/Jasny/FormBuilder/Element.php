<?php

namespace Jasny\FormBuilder;

/**
 * Base class for bootstrap elements.
 */
abstract class Element
{
    /**
     * Default options
     * @var array
     */
    static public $defaults = [
        'render' => true,           // Render element
        'validation' => true,       // Server-side validation enabled
        'required-suffix' => ' *',  // Suffix label for required controls
        
        'error:required' => "Please fill out this field",
        'error:type' => "Please enter a {@type}",
        'error:min' => "Value must be greater or equal to {@min}",
        'error:max' => "Value must be less or equal to {@max}",
        'error:minlength' => "Please use at least {@minlength} characters for this text (you are currently using {\$length} characters)",
        'error:maxlength' => "Please shorten this text to {@maxlength} characters or less (you are currently using {\$length} characters)",
        'error:pattern' => "Please match the requested format{: |@title}",
        'error:match' => "The 2 fields don't match",
        'error:upload' => [
            UPLOAD_ERR_INI_SIZE => "The uploaded file exceeds the upload_max_filesize directive in php.ini.",
            UPLOAD_ERR_FORM_SIZE => "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.",
            UPLOAD_ERR_PARTIAL => "The uploaded file was only partially uploaded.",
            UPLOAD_ERR_NO_FILE => "No file was uploaded.",
            UPLOAD_ERR_NO_TMP_DIR => "Missing a temporary folder.",
            UPLOAD_ERR_CANT_WRITE => "Failed to write file to disk.",
            UPLOAD_ERR_EXTENSION => "A PHP extension stopped the file upload.",
        ]
    ];
    
    
    /**
     * Parent element
     * @var Bootstrap/Group
     */
    protected $parent;
    
    /**
     * HTML attributes
     * @var array
     */
    protected $attrs = [];
    
    /**
     * Element options 
     * @var array
     */
    protected $options = [];

    
    /**
     * Class constructor.
     * 
     * @param array $attrs    HTML attributes
     * @param array $options  Element options
     */
    public function __construct(array $attrs=[], array $options=[])
    {
        $this->attrs = array_merge($this->attrs, $attrs);
        $this->options = array_merge($this->options, $options);
    }
    
    /**
     * Return parent object.
     * 
     * @return Bootstrap/Group
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Alias of Bootstrap/Element::getParent();
     * 
     * @return Bootstrap/Element
     */
    public function end()
    {
        return $this->getParent();
    }
    
    
    /**
     * Set HTML attribute(s).
     * 
     * @param string|array $attr   Attribute name or assoc array with attributes
     * @param mixed        $value
     * @return Bootstrap/Element $this
     */
    public function setAttr($attr, $value=null)
    {
        if (is_array($attr)) $this->attrs = array_merge($this->attrs, $attr);
         else $this->attrs[$attr] = $value;
         
        return $this;
    }

    /**
     * Cast the value of an attribute to a string.
     * 
     * @param string $attr    Attribute name
     * @param mixed  $value
     * @return string
     */
    protected function castAttr($attr, $value)
    {
        if ($value instanceof Control) $value = $value->getValue();

        if ($value === null || $value === false) return null;
        if ($value === true) return $attr;
        
        if ($value instanceof \DateTime) return $value->format('c');
        if (is_object($value) && method_exists($value, '__toString')) return (string)$value;
        if (!is_scalar($value)) return json_encode($value);
        
        return $value;
    }
    
    /**
     * Get an HTML attribute.
     * 
     * @param string  $attr  Attribute name, omit to get all attributes
     * @param boolean $cast  Cast to a string
     * @return mixed
     */
    public final function getAttr($attr, $cast=true)
    {
        $values = $this->getAttrs(false);
        if (!isset($values[$attr])) return null;
        
        return $cast ? $this->castAttr($attr, $values[$attr]) : $values[$attr];
    }

    /**
     * Get all HTML attributes.
     * 
     * @param boolean $cast  Cast to a string
     * @return array
     */
    public function getAttrs($cast=true)
    {
        $values = $this->attrs;

        if ($cast) {
            foreach ($values as $key=>&$value) {
                $value = $this->castAttr($key, $value);
                if (!isset($value)) unset($values[$key]);
            }
        }

        return $values;
    }
    
    
    /**
     * Set an option or array with options
     * 
     * @param sting|array $option  Option name or array with options
     * @param mixed       $value
     * @return Boostrap/Element $this
     */
    public function setOption($option, $value=null)
    {
        if (is_array($option)) {
            foreach ($option as $key=>$value) {
                if (!isset($value)) unset($this->options[$key]);
                 else $this->options[$key] = $value;
            }
        } elseif (!isset($value)) {
            unset($this->options[$option]);
        } else {
            $this->options[$option] = $value;
        }
        
        return $this;
    }
    
    /**
     * Get an option.
     * 
     * @param string  $option  Option name; omit to get all options
     * @return mixed
     */
    public final function getOption($option=null)
    {
        $options = $this->getOptions();
        return isset($options[$option]) ? $options[$option] : null;
    }
        
    /**
     * Get all options.
     * 
     * @param boolean $bubble  Also get options from ancestors and default options.
     * @return array
     */
    public function getOptions()
    {
        return $this->options + (isset($this->parent) ? $this->parent->getOptions() : self::$defaults);
    }
    
    
    /**
     * Validate the element.
     * 
     * @return boolean
     */
    abstract public function isValid();

    
    /**
     * Get attributes as string.
     * 
     * @param array $attr  Overwrite attributes
     * @return string
     */
    public function renderAttrs(array $attrs=[])
    {
        $attrs += $this->getAttrs();
        
        $str = "";
        foreach ($attrs as $key=>$value) {
            if (!isset($value) || $value === false) continue;
            $str .= ' ' . htmlentities($key) . '="' . htmlentities($value) . '"';
        }
        
        return $str;
    }
    
    /**
     * Render the element to HTML (without post processing).
     * 
     * @return string
     */
    abstract protected function render();

    /**
     * Render the element to HTML.
     * 
     * @return string
     */
    public function __toString()
    {
        $html = $this->render();
        
        $tidy_cfg = $this->getOption('tidy', false);
        if ($tidy_cfg) {
            $tidy = new \tidy();
            $tidy->parseString($html, $tidy_cfg);
            $html = join("\n", $tidy->body()->child);
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
        $class = __NAMESPACE__ . '\\' . str_replace(' ', '', ucwords(str_replace('-', ' ', $element)));
        if (!class_exists($class)) throw new Exception("Unable to build a $element.");
        
        $refl = new \ReflectionClass($class);
        return $refl->newInstanceArgs($args);
    }
}
