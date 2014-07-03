<?php

namespace Jasny\FormBuilder;

/**
 * HTML attributes
 */
class Attr extends \ArrayIterator
{
    /**
     * Class constructor
     * 
     * @param array $array
     */
    public function __construct($array = [])
    {
        $array += ['class' => []];
        if (is_string($array['class'])) $array['class'] = preg_split('/\s+/', $array['class']);
        
        parent::__construct($array);
    }
    
    /**
     * Cast the value of an attribute to a string.
     * 
     * @param string $key
     * @param mixed  $value
     * @return string
     */
    protected function cast($key, $value)
    {
        if ($key === 'class' && is_array($value)) return $this->castClass($value);
        
        if ($value instanceof Control) $value = $value->getValue();
        if ($value instanceof \Closure) $value = $value();
        
        if ($value instanceof \DateTime) return $value->format('c');
        if (is_object($value) && method_exists($value, '__toString')) return (string)$value;
        if (is_array($value) || $value instanceof \stdClass || $value instanceof \JsonSerializable) {
            return json_encode($value);
        }
        
        return $value;
    }

    /**
     * Cast the value of an attribute to a string.
     * 
     * @param array $values
     * @return string
     */
    protected function castClass($values)
    {
        $classes = [];
        foreach ($values as $value) {
            $class = $this->cast(null, $value);
            if ($class) $classes[] = $class;
        }
        
        return !empty($classes) ? join(' ', array_unique($classes)) : null;
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
                $this[$key] = $value;
            } elseif ($this[$key]) {
                unset($this[$key]);
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
        return isset($attr) ? $this[$attr] : $this->getAll();
    }
    
    /**
     * Get an HTML attribute(s) without casting them.
     * 
     * @param string  $attr  Attribute name, omit to get all attributes
     * @return mixed
     */
    final public function getRaw($attr=null)
    {
        if (isset($attr)) return parent::offsetGet($attr);
        return $this->getAll(true);
    }
    
    
    /**
     * Get all HTML attributes.
     * 
     * @param boolean $raw   Don't cast attributes
     * @return array
     */
    protected function getAll($raw=false)
    {
        $attrs = $this->getArrayCopy();
        if ($raw) return $attrs;
        
        foreach ($attrs as $key=>&$value) {
            $value = $this->cast($key, $value);
        }

        return $attrs;
    }

    
    /**
     * Check if class is present
     * 
     * @param string $class
     * @return boolean
     */
    public function hasClass($class)
    {
        $attr = parent::offsetGet('class');
        
        foreach ($attr as $cur) {
            if ($this->cast(null, $cur) === $class) return true;
        }
        
        return false;
    }
    
    /**
     * Add a class
     * 
     * @param string|array $class  Multiple classes may be specified as array or using a space
     * @return Attr $this
     */
    public function addClass($class)
    {
        $attr = parent::offsetGet('class');
        
        if (is_string($class)) $class = preg_split('/\s+/', $class);
        if (!is_array($class)) $class = [$class];
        
        parent::offsetSet('class', array_merge($attr, $class));
    }
    
    /**
     * Remove a class
     * 
     * @param string|array $class  Multiple classes may be specified as array or using a space
     * @return Attr $this
     */
    public function removeClass($class)
    {
        $attr = parent::offsetGet('class');
        if (!is_array($class)) $remove = preg_split('/\s+/', $class);
        
        foreach ($attr as $i=>$cur) {
            if (in_array($this->cast(null, $cur), $remove)) unset($attr[$i]);
        }
        
        parent::offsetSet('class', $attr);
        return $this;
    }
    
    
    /**
     * Get attributes as string
     * 
     * @param array $override  Attributes to add or override
     * @return string
     */
    public function render(array $override=[])
    {
        foreach ($override as $key=>&$value) {
            $value = $this->cast($key, $value);
        }
        
        $attrs = $override + $this->getAll();
        
        $pairs = [];
        foreach ($attrs as $key=>$value) {
            static::appendAttr($pairs, $key, $value);
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
            static::appendAttr($pairs, $key, $this[$key]);
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
     * Get value for an offset
     * 
     * @param string $index
     * @return string
     */
    public function offsetGet($index)
    {
        if (!parent::offsetExists($index)) return null;
        
        $value = parent::offsetGet($index);
        return $this->cast($index, $value);
    }

    /**
     * Set value for an offset
     * 
     * @param string $index   The index to set for.
     * @param string $newval  The new value to store at the index.
     */
    public function offsetSet($index, $newval)
    {
        if ($index === 'class' && is_string($newval)) $newval = preg_split('/\s+/', $newval);
        parent::offsetSet($index, $newval);
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
