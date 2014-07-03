<?php

namespace Jasny\FormBuilder;

use Jasny\FormBuilder;

/**
 * Render only part of the element
 */
trait RenderPartial
{
    /** @var Span */
    protected $prepend;
    
    /** @var Span */
    protected $append;
    
    /** @var Label */
    protected $label;

    /** @var Group */
    protected $container;
    
    
    /**
     * Get the append component.
     * 
     * @return Span
     */
    public function getPrepend()
    {
        if (!isset($this->prepend)) $this->prepend = $this->build('span');
        return $this->prepend;
    }
    
    /**
     * Get the append component.
     * 
     * @return Span
     */
    public function getAppend()
    {
        if (!isset($this->append)) $this->append = $this->build('span');
        return $this->append;
    }
    
    /**
     * Get the label component.
     * 
     * @return Label
     */
    public function getLabel()
    {
        if (!isset($this->label)) $this->label = $this->build('label');
        return $this->label;
    }
    
    /**
     * Get the container component.
     * 
     * @return Group
     */
    public function getContainer()
    {
        $type = $this->getOption('container') ?: 'group';
        
        if (!isset($this->container)) {
            $this->container = $this->build($type);
        } else {
            $class = FormBuilder::$elements[$type][0];
            if (!is_a($this->container, $class)) {
                $this->container = FormBuilder::convert($this->container, $type);
            }
        }
        
        return $this->container;
    }

    /**
     * Get element content
     * 
     * @return string|null
     */
    public function getContent()
    {
        $content = $this->renderContent();
        if (!isset($content)) return null;
        
        foreach ($this->getDecorators() as $decorator) {
            $content = $decorator->renderContent($this, $content);
        }
        
        return (string)$content;
    }
    
    
    /**
     * Render the element to HTML
     * 
     * @return string
     */
    public function render()
    {
        $container = $this->getContainer()->clear();
        
        // Label
        if ($this->getOption('label')) {
            if ($this->getOption('label') === 'inside') {
                $element = $this->getLabel()->setAttr('for', null)->setContent($this->renderElement());
            } else {
                $label = $this->getLabel()->setContent($this->getDescription());
                $label->setAttr('for', $this->getId());
            }
        }
        if (isset($label)) $container->add($label);
        
        // Prepend
        $prepend = $this->getOption('prepend');
        if ($prepend) $container->add($this->getAppend()->setContent($prepend));
        
        // Element
        if (!isset($element)) $element = $this->renderElement();
        $container->add($element);
        
        // Append
        $append = $this->getOption('append');
        if ($append) $container->add($this->getAppend()->setContent($append));
        
        // Error
        if (method_exists($this, 'getError')) {
            $error = $this->getError();
            if ($error) $this->begin('span', [], ['class'=>'error'])->setContent($error);
        }
        
        // Validation script
        if (method_exists($this, 'getValidationScript')) {
            $container->add($this->getValidationScript());
        }
        
        return (string)$container;
    }
    
    /**
     * Render to base HTML element.
     * 
     * @return string
     */
    abstract public function renderElement();
    
    /**
     * Render the element content.
     * 
     * @return string|null
     */
    protected function renderContent()
    {
        return null;
    }
}
