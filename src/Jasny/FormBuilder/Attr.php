<?php

namespace Jasny\FormBuilder;

/**
 * HTML attributes
 */
class Attr extends \ArrayIterator
{
    /**
     * Cast the value of an attribute to a string.
     * 
     * @param mixed  $value
     * @return string
     */
    protected function cast($value)
    {
        if ($value instanceof FormElement) $value = $value->getValue();
        if ($value instanceof \Closure) $value = $value();
        
        if ($value instanceof \DateTime) return $value->format('c');
        if (is_object($value) && method_exists($value, '__toString')) return (string)$value;
        if (is_array($value) || $value instanceof \stdClass || $value instanceof \JsonSerializable) {
            return json_encode($value);
        }
        
        return $value;
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
        foreach ($attrs as $key=>$value) {
            if (isset($value)) {
                $this->offsetSet($key, $value);
            } else {
                $this->offsetUnset($key);
            }
        }
        
        return $this;
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
     * Get an HTML attribute(s) without casting them.
     * 
     * @param string  $attr  Attribute name, omit to get all attributes
     * @return mixed
     */
    final public function getRaw($attr=null)
    {
        return isset($attr) ? $this->getOne($attr, true) : $this->getAll(true);
    }
    
    /**
     * Get a specific HTML attribute.
     * 
     * @param string  $attr
     * @param boolean $raw   Don't cast attribute
     * @return array
     */
    protected function getOne($attr, $raw=false)
    {
        if (!$this->offsetExists($attr)) return null;
        
        $value = parent::offsetGet($attr);
        return $raw ? $value : $this->cast($value);
    }
    
    /**
     * Get all HTML attributes.
     * 
     * @param boolean $raw   Don't cast attributes
     * @return array
     */
    protected function getAll($raw=false)
    {
        if ($raw) return $this->getArrayCopy();
        
        $attrs = $this->getArrayCopy();
        
        foreach ($attrs as $key=>&$value) {
            $value = $this->cast($value);
            if (!isset($value)) unset($attrs[$key]);
        }

        return $attrs;
    }
    
    
    /**
     * Get attributes as string
     * 
     * @param array $override  Attributes to add or override
     * @return string
     */
    public function render(array $override=[])
    {
        foreach ($override as &$value) {
            $value = $this->cast($value);
        }
        
        $attrs = $override + $this->getAll();
        
        $pairs = [];
        foreach ($attrs as $key=>$value) {
            static::appendAttr($pairs, $key, $this->getOne($key));
        }
        
        return join(' ', $pairs);
    }

    /**
     * Get specific attributes as string
     * 
     * @param array|string $attrs
     * @return string
     */
    public function renderOnly($attrs)
    {
        $pairs = [];
        foreach ((array)$attrs as $key) {
            static::appendAttr($pairs, $key, $this->getOne($key));
        }
        
        return join(' ', $pairs);
    }
    
    /**
     * Get attributes as string
     * 
     * @return string
     */
    final public function __toString()
    {
        return $this->render();
    }
    
    
    /**
     * Array access get
     * 
     * @param string $offset
     * @return string
     */
    final public function offsetGet($offset)
    {
        return $this->getOne($offset);
    }

    /**
     * Append a value.
     * 
     * @param mixed $value
     * @throws \Exception
     */
    final public function append($value)
    {
        throw new \Exception("Unable to add value '$value'. You need to use associated keys.");
    }
    
    
    /**
     * Add a key/value as HTML attribute.
     * 
     * @param array  $pairs
     * @param string $key
     * @param mixed  $value  Scalar
     */
    protected static function appendAttr(&$pairs, $key, $value)
    {
        if (!isset($value) || $value === false) return;

        $set = $value === true ? null : '="' . htmlentities($value) . '"';
        $pairs[] = htmlentities($key) . $set;
    }
}
