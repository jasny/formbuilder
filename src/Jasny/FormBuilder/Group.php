<?php

namespace Jasny\FormBuilder;

use Jasny\FormBuilder;

/**
 * Base class for an HTML child with children.
 */
class Group extends Element
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
     * Class constructor.
     * 
     * @param array $options  Element options
     * @param array $attr     HTML attributes
     */
    public function __construct(array $options = array(), array $attr = array())
    {
        parent::__construct($options, $attr);
    }
    
    
    /**
     * Factory method
     * 
     * @param string $element
     * @param array  $options  Element options
     * @param array  $attr     HTML attributes
     * @return FormBuilder\Element|FormBuilder\Control
     */
    protected function build($element, array $options=[], array $attr=[])
    {
        if ($this->parent) return $this->parent->build($element, $options, $attr);
        return FormBuilder::element($element, $options, $attr);
    }
    
    /**
     * Add an child to the group.
     * 
     * @param Element|string $child
     * @param array          $options  Element options
     * @param array          $attr     HTML attributes
     * @return Group $this
     */
    public function add($child, array $options=[], array $attr=[])
    {
        if (is_string($child) && $child[0] !== '<') {
            $child = $this->build($child, $options, $attr);
        }
        
        if ($child instanceof Element) $child->parent = $this;
        
        $this->children[] = $child;
        return $this;
    }
    
    /**
     * Add an child and return it.
     * 
     * @param Element|string $child
     * @param array          $options  Element options
     * @param array          $attr     HTML attributes
     * @return Element $child
     */
    public function begin($child, array $options=[], array $attr=[])
    {
        if (is_string($child) && $child[0] !== '<') {
            $child = $this->build($child, $options, $attr);
        }
        
        if (!$child instanceof Element) {
            throw new \InvalidArgumentException("To add a " . gettype($child) . " use the add() method");
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
            } elseif (isset($name) && $child instanceof Control && $child->getName() == $name) {
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
     * @return Control[]
     */
    public function getElements()
    {
        $elements = array();
        
        foreach ($this->children as $child) {
            if ($child instanceof Control) {
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
     * 
     * @return string
     */
    public function open()
    {
        $this->applyDecorators();
        return !empty($this->tagname) ? "<{$this->tagname} {$this->attr}>" : null;
    }

    /**
     * Render the closing tag
     * 
     * @return string
     */
    public function close()
    {
        return !empty($this->tagname) ? "</{$this->tagname}>" : null;
    }
    
    
    /**
     * Get content of the element.
     * 
     * @return string
     */
    final public function getContent()
    {
        $content = $this->renderContent();
        
        foreach ($this->getDecorators() as $decorator) {
            $content = $decorator->renderContent($this, $content);
        }
        
        return $content;
    }
    
    /**
     * Render the content of the HTML element.
     * 
     * @return string
     */
    protected function renderContent()
    {
        $items = [];
        
        foreach ($this->children as $child) {
            if (!isset($child) || ($child instanceof Element && !$child->getOption('render'))) continue;
            $items[] = (string)$child;
        }
        
        return join("\n", $items);
    }
    
    /**
     * Render the child to HTML.
     * 
     * @return string
     */
    protected function render()
    {
        return $this->open() . "\n" . $this->getContent() . "\n" . $this->close();
    }
}
