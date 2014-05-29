<?php

namespace Jasny\FormBuilder;

/**
 * HTML attributes
 */
class Attr
{
    /**
     * Class constructor
     * 
     * @param array $attr
     * @param Additional arrays may be passed
     */
    public function __construct($attr)
    {
        foreach (func_get_args() as $attr) {
            $this->set($attr);
        }
    }
    
    /**
     * Set HTML attribute(s).
     * 
     * @param string|array $attr   Attribute name or assoc array with attributes
     * @param mixed        $value
     * @return Attr $this
     */
    public function set($attr, $value=null)
    {
        $attrs = is_string($attr) ? [$attr => $value] : $attr;
        
        foreach ($attrs as $key => $value) {
            $this->$key = $value;
        }
        
        return $this;
    }
    
    
    /**
     * Cast the value of an attribute to a string.
     * 
     * @param string $attr    Attribute name
     * @param mixed  $value
     * @return string
     */
    protected function cast($attr, $value)
    {
        if ($value instanceof Element) $value = $value->getValue();

        if ($value === null || $value === false) return null;
        if ($value === true) return $attr;
        
        if ($value instanceof \DateTime) return $value->format('c');
        if (is_object($value) && method_exists($value, '__toString')) return (string)$value;
        if (!is_scalar($value)) return json_encode($value);
        
        return $value;
    }
    
    
    /**
     * Get an HTML attribute(s).
     * All attributes will be cased to their string representation.
     * 
     * @param string  $attr  Attribute name, omit to get all attributes
     * @return mixed
     */
    final public function get($attr=null)
    {
        return isset($attr) ? $this->getOne($attr) : $this->getAll();
    }
    
    /**
     * Get a specific HTML attribute (casted).
     * 
     * @return array
     */
    protected function getOne($attr)
    {
        return isset($this->$attr) ? $this->castAttr($attr, $this->$attr) : null;
    }
    
    /**
     * Get all HTML attributes (casted).
     * 
     * @return array
     */
    protected function getAll()
    {
        $attrs = get_object_vars($this);
        
        foreach ($attrs as $key=>&$value) {
            $value = $this->castAttr($key, $value);
            if (!isset($value)) unset($attrs[$key]);
        }

        return $attrs;
    }
    
    
    /**
     * Get attributes as string
     * 
     * @param array $override  Attributes to override
     * @return string
     */
    public function render(array $override=[])
    {
        $attrs = $this->getAll() + $override;
        
        $pairs = [];
        foreach ($attrs as $key=>$value) {
            if (!isset($value) || $value === false) continue;
            
            $set = $value === true ? null : '="' . htmlentities($value) . '"';
            $pairs[] = htmlentities($key) . $set;
        }
        
        return join(' ', $pairs);
    }

    /**
     * Get specific attributes as string
     * 
     * @param array $attrs
     * @return string
     */
    public function renderOnly(array $attrs)
    {
        $pairs = [];
        foreach ($attrs as $key) {
            $value = $this->getOne($key);
            if (!isset($value) || $value === false) continue;
            
            $set = $value === true ? null : '="' . htmlentities($value) . '"';
            $pairs[] = htmlentities($key) . $set;
        }
        
        return join(' ', $pairs);
    }
    
    /**
     * Get attributes as string
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}
