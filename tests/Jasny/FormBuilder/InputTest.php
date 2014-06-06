<?php

namespace Jasny\FormBuilder\Bootstrap;

/**
 * Tests for <input> in Bootstrap Form generator
 */
class InputTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Input
     */
    protected $input;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->input = new Input();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    
    /**
     * Test the form constructor
     */
    public function testConstruct()
    {
        $input = new Input('test', 'A Test', ['class'=>'subinput'], ['foo'=>'bar']);
        
        $attrs = ['name'=>'test', 'class'=>'subinput', 'type'=>'text', 'placeholder'=>'A Test'];
        $this->assertSame($attrs, $input->getAttrs(false));
        $this->assertSame('bar', $input->getOption('foo'));
        $this->assertSame('A Test', $input->getDescription());
    }
    
    
    /**
     * Test setting and getting an attribute
     */
    public function testGetAttr()
    {
        $ret = $this->input->setAttr('foo', 'bar');
        $this->assertSame($this->input, $ret);
        $this->assertSame('bar', $this->input->getAttr('foo'));
    }
    
    /**
     * Test setting and getting an attribute as date
     */
    public function testGetAttr_DateTime()
    {
        $date = new \DateTime('now');
        $this->input->setAttr('min', $date);
        $this->assertSame($date, $this->input->getAttr('min', false));
        $this->assertSame($date->format('c'), $this->input->getAttr('min'));
    }
    
    /**
     * Test setting and getting an attribute as object (cast to string)
     */
    public function testGetAttr_Object()
    {
        $control = $this->getMock('stdClass', ['__toString']);
        $control->expects($this->once())->method('__toString')->will($this->returnValue('bar'));
        
        $this->input->setAttr('foo', $control);
        $this->assertSame($control, $this->input->getAttr('foo', false));
        $this->assertSame('bar', $this->input->getAttr('foo'));
    }
    
    /**
     * Test setting and getting an attribute an array and value object
     */
    public function testGetAttr_Json()
    {
        $this->input->setAttr('foo', [10, 52]);
        $this->assertSame([10, 52], $this->input->getAttr('foo', false));
        $this->assertSame(json_encode([10, 52]), $this->input->getAttr('foo'));
 
        $val = (object)['alpha'=>'lima', 'beta'=>'mike'];
        $this->input->setAttr('charlie', $val);
        $this->assertSame($val, $this->input->getAttr('charlie', false));
        $this->assertSame(json_encode($val), $this->input->getAttr('charlie'));
    }
    
    /**
     * Test setting and getting an attribute as control
     */
    public function testGetAttr_Control()
    {
        $date = new \DateTime('now');
        
        $control = $this->getMockBuilder('Jasny\FormBuilder\Control')->getMockForAbstractClass();
        $control->expects($this->exactly(3))->method('getValue')->will($this->onConsecutiveCalls(42, 51, $date));
        
        $this->input->setAttr('min', $control);
        $this->assertSame($control, $this->input->getAttr('min', false));
        $this->assertSame(42, $this->input->getAttr('min'));
        $this->assertSame(51, $this->input->getAttr('min'));
        $this->assertSame($date->format('c'), $this->input->getAttr('min'));
    }
    
    /**
     * Test setting and getting an attribute as date
     */
    public function testGetAttrs_DateTime()
    {
        $date = new \DateTime('now');
        $this->input->setAttr('min', $date);
        $this->assertSame(['type'=>'text', 'min'=>$date], $this->input->getAttrs(false));
        $this->assertSame(['type'=>'text', 'min'=>$date->format('c')], $this->input->getAttrs());
    }
    
    /**
     * Test setting and getting an attribute as object (cast to string)
     */
    public function testGetAttrs_Object()
    {
        $control = $this->getMock('stdClass', ['__toString']);
        $control->expects($this->once())->method('__toString')->will($this->returnValue('bar'));
        
        $this->input->setAttr('foo', $control);
        $this->assertSame(['type'=>'text', 'foo'=>$control], $this->input->getAttrs(false));
        $this->assertSame(['type'=>'text', 'foo'=>'bar'], $this->input->getAttrs());
    }
    
    /**
     * Test setting and getting an attribute an array and value object
     */
    public function testGetAttrs_Json()
    {
        $this->input->setAttr('foo', [10, 52]);
        
        $val = (object)['alpha'=>'lima', 'beta'=>'mike'];
        $this->input->setAttr('charlie', $val);
        
        $this->assertSame(['type'=>'text', 'foo'=>[10, 52], 'charlie'=>$val], $this->input->getAttrs(false));
        $this->assertSame(['type'=>'text', 'foo'=>json_encode([10, 52]), 'charlie'=>json_encode($val)], $this->input->getAttrs());
    }
    
    /**
     * Test setting and getting an attribute as control
     */
    public function testGetAttrs_Control()
    {
        $control_min = $this->getMockBuilder('Jasny\FormBuilder\Control')->getMockForAbstractClass();
        $control_min->expects($this->once())->method('getValue')->will($this->returnValue(42));
        $this->input->setAttr('min', $control_min);
        
        $date = new \DateTime('now');
        $control_date = $this->getMockBuilder('Jasny\FormBuilder\Control')->getMockForAbstractClass();
        $control_date->expects($this->once())->method('getValue')->will($this->returnValue($date));
        $this->input->setAttr('date', $control_date);
        
        $this->assertSame(['type'=>'text', 'min'=>$control_min, 'date'=>$control_date], $this->input->getAttrs(false));
        $this->assertSame(['type'=>'text', 'min'=>42, 'date'=>$date->format('c')], $this->input->getAttrs());
    }
    

    /**
     * Test setting and getting an option
     */
    public function testGetOption()
    {
        $ret = $this->input->setOption('foo', 'bar');
        $this->assertSame($this->input, $ret);
        $this->assertSame('bar', $this->input->getOption('foo'));
    }
    
    /**
     * Test setting and getting the name
     */
    public function testGetName()
    {
        $ret = $this->input->setName('foo');
        $this->assertSame($this->input, $ret);
        $this->assertSame('foo', $this->input->getName());
        $this->assertSame('foo', $this->input->getAttr('name'));
    }

    /**
     * Test setting and getting the value
     */
    public function testGetValue()
    {
        $ret = $this->input->setValue('bar99');
        $this->assertSame($this->input, $ret);
        $this->assertSame('bar99', $this->input->getValue());
        $this->assertSame('bar99', $this->input->getAttr('value'));
        
        $val = (object)['foo'=>'bar'];
        $this->input->setValue($val);
        $this->assertSame($this->input, $ret);
        $this->assertSame($val, $this->input->getValue());
        $this->assertSame($val, $this->input->getAttr('value', false));
    }
    
    /**
     * Test setting and getting the value as control
     */
    public function testGetValue_Control()
    {
        $control = $this->getMockBuilder('Jasny\FormBuilder\Control')->getMockForAbstractClass();
        $control->expects($this->exactly(2))->method('getValue')->will($this->onConsecutiveCalls(42, 51));
        
        $this->input->setValue($control);
        $this->assertSame(42, $this->input->getValue());
        $this->assertSame(51, $this->input->getValue());
    }
    
    /**
     * Test setting and getting the description
     */
    public function testGetDescription()
    {
        $ret = $this->input->setDescription('Foo Bar');
        $this->assertSame($this->input, $ret);
        $this->assertSame('Foo Bar', $this->input->getDescription());
    }
    
    
    /**
     * Test rendering a text input
     */
    public function testRender()
    {
        $html = <<<HTML
<input type="text">
HTML;
        
        $this->input->setOption('container', false);
        $this->input->setOption('label', false);
        $this->assertSame($html, (string)$this->input);
    }
    
    /**
     * Test rendering a text input with control group
     */
    public function testRender_ControlGroup()
    {
        echo "\n";
        
        $html = <<<HTML
<div class="control-group">
<label class="control-label" for="inputEmail">Email</label>
<div class="controls">
<input type="text" id="inputEmail">
</div>
</div>
HTML;
        
        $this->input->setOption('container', true);
        $this->input->setOption('label', true);
        $this->input->setDescription('Email');
        $this->input->setAttr('id', 'inputEmail');
        
        $rendered = (string)$this->input;
        $this->assertSame($html, $rendered);
    }
}
