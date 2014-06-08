<?php

namespace Jasny\FormBuilder;

/**
 * Base class for an HTML child with children.
 */
abstract class Group extends Element
{
    /**
     * The HTML tag name.
     * @var string
     */
    protected $tagname;
    
    /**
     * Child nodes of the group.
     * @var array
     */
    protected $children = [];

    
    /**
     * Apply modifications by decorator
     * 
     * @param Decorator $decorator
     */
    protected function applyDecorator(Decorator $decorator)
    {
        parent::applyDecorator($decorator);
        
        if ($decorator->isDeep()) {
            foreach ($this->children as $child) {
                if ($child instanceof Element) $child->applyDecorator($decorator);
            }
        }
    }
    
    /**
     * Add an child to the group.
     * 
     * @param Element|string $child
     * @return Group  $this
     */
    public function add($child)
    {
        if (is_string($child) && $child[0] !== '<') {
            $child = $this->build($child, array_slice(func_get_args(), 1));
        }
        
        if ($child instanceof Element) $child->parent = $this;
        
        foreach ($this->getDecorators() as $decorator) {
            if ($decorator->isDeep()) $child->applyDecorator($decorator);
        }
        
        $this->children[] = $child;
        return $this;
    }
    
    /**
     * Add an child and return it.
     * 
     * @param Element|string  $child
     * @return Element  $child
     */
    public function begin($child)
    {
        if (is_string($child) && $child[0] !== '<') {
            $child = $this->build($child, array_slice(func_get_args(), 1));
        }
        
        $this->add($child);
        return $child;
    }
    
    
    /**
     * Get the children of the group.
     * 
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }
    
    /**
     * Find a specific child through deep search.
     * 
     * @param string  $name    Element name or #id
     * @param boolean $unlink  Unlink the found element
     * @return Element
     */
    protected function deepSearch($name, $unlink = false)
    {
        if ($name[0] == '#') {
            $id = substr($name, 1);
            unset($name);
        }
        
        foreach ($this->children as $i=>$child) {
            if (isset($id) && $child->getId() === $id) {
                $found = $child;
                if ($unlink) unset($this->children[$i]);
            } elseif (isset($name) && $child instanceof FormElement && $child->getName() == $name) {
                $found = $child;
                if ($unlink) unset($this->children[$i]);
            } elseif ($child instanceof Group) {
                $found = $child->deepSearch($name);
            }
            
            if (isset($found)) return $found;
        }
        
        return null; // Not found
    }
    
    /**
     * Get a specific child (deep search).
     * 
     * @param string  $name    Element name or #id
     * @return Element
     */
    protected function get($name)
    {
        return $this->deepSearch($name);
    }
    
    /**
     * Get all the form elements in the group (deep search).
     * 
     * @return FormElement[]
     */
    public function getElements()
    {
        $elements = array();
        
        foreach ($this->children as $child) {
            if ($child instanceof FormElement) {
                $name = $child->getName();
                if ($name) {
                    $elements[$name] = $child;
                } else {
                    $elements[] = $child;
                }
            } elseif ($child instanceof Group) {
                $elements = array_merge($elements, $child->getElements());
            }
        }
        
        return $elements;
    }
    
    /**
     * Remove a specific child (deep search)
     * 
     * @param string $name  Element name or #id
     * @return Group $this
     */
    public function remove($name)
    {
        $this->deepSearch($name, true);
        return $this;
    }
    
    
    /**
     * Set the values of the elements.
     * 
     * @param array $values
     * @return Group  $this
     */
    public function setValues($values)
    {
        $values = (array)$values;

        foreach ($this->getElements() as $element) {
            $name = $element->getName();
            if ($name && isset($values[$name])) $element->setValue($values[$name]);
        }
        
        return $this;
    }
    
    /**
     * Get the values of the elements.
     * 
     * @return array
     */
    public function getValues()
    {
        $values = array();
        
        foreach ($this->getElements() as $element) {
            if ($element->getName()) $values[$element->getName()] = $element->getValue();
        }
        
        return $values;
    }
    
    /**
     * Validate the elements in the group.
     * 
     * @return boolean
     */
    protected function validate()
    {
        $ret = true;
        
        foreach ($this->children as $child) {
            if (!$child instanceof Element || $child->getOption('validation') == false) continue;
            $ret = $ret && $child->isValid();
        }
        
        return $ret;
    }
    
    
    /**
     * Render the opening tag
     */
    public function open()
    {
        return "<" . $this->tagname . rtrim(' ' . $this->attr) . ">";
    }

    /**
     * Render the closing tag
     */
    public function close()
    {
        return "</" . $this->tagname . ">";
    }

    /**
     * Render the child to HTML.
     * 
     * @return string
     */
    protected function render()
    {
        $html = $this->open();
        
        foreach ($this->children as $child) {
            if (!isset($child) || ($child instanceof Element && !$child->getOption('render'))) continue;
            $html .= (string)$child . "\n";
        }
        
        $html .= $this->close();
        
        return $html;
    }
}
