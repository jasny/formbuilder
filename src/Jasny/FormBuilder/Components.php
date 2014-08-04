<?php

namespace Jasny\FormBuilder;

/**
 * Element that exists of several components.
 */
trait Components
{
    /**
     * Components
     * @var Element[]
     */
    protected $components = [];
    
    
    /**
     * Initialise default components
     */
    protected function initComponents()
    {
        $this->newComponent('label', 'label')
            ->setAttr('for', function() {
                return $this->getOption('label') !== 'inside' ? $this->getId() : null;
            })
            ->setContent(function() {
                return htmlentities($this->getDescription());
            })
        ;
            
        $this->newComponent('prepend', 'span')->setContent(function() {
            return $this->getOption('prepend');
        });
        
        $this->newComponent('append', 'span')->setContent(function() {
            return $this->getOption('append');
        });
        
        $this->newComponent('error', 'span', [], ['class'=>'error'])->setContent(function() {
            return htmlentities($this->getError());
        });
    }
    
    /**
     * Create / init new component
     * 
     * @param string $name     Component name
     * @param string $type     Element type
     * @param array  $options  Element options
     * @param array  $attr     Element attr
     * @return Element
     */
    public function newComponent($name, $type, array $options=[], array $attr=[])
    {
        $options += ['id'=>false, 'decorate'=>false];
        $component = $this->build($type, $options, $attr)->asComponentOf($this);
        
        if (isset($name)) $this->components[$name] = $component;
        return $component;
    }
    
    /**
     * Get a component.
     * 
     * @param string $name
     * @return Element
     */
    public function getComponent($name)
    {
        if ($name === 'container') return $this->getContainer();
        
        return isset($this->components[$name]) ? $this->components[$name] : null;
    }
    
    
    /**
     * Get label component
     * 
     * @return Label
     */
    public function getLabel()
    {
        return $this->getComponent('label');
    }
    
    /**
     * Get the container component.
     * 
     * @return Group
     */
    public function getContainer()
    {
        $type = $this->getOption('container') ?: 'group';
        
        if (!isset($this->components['container'])) {
            $this->newComponent('container', $type);
        } else {
            $this->components['container'] = $this->components['container']->convertTo($type);
        }
        
        return $this->components['container'];
    }
    
    
    /**
     * Render the element to HTML
     * 
     * @return string
     */
    public function render()
    {
        $container = $this->getContainer()->setContent(null);
        
        // Label
        if ($this->getOption('label')) {
            if ($this->getOption('label') === 'inside') {
                $el = $this->getLabel()->setContent($this->renderElement());
            } else {
                $container->add($this->getLabel());
            }
        }
        
        // Prepend
        if ($this->getOption('prepend')) $container->add($this->getComponent('prepend'));
        
        // Element
        if (!isset($el)) $el = $this->renderElement();
        $container->add($el);
        
        // Append
        if ($this->getOption('append')) $container->add($this->getComponent('append'));
        
        // Error
        if (method_exists($this, 'getError') && $this->getError()) {
            $container->add($this->getComponent('error'));
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
}
