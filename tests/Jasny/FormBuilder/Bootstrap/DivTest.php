<?php

namespace Jasny\FormBuilder\Bootstrap;

/**
 * Tests for <div> in Bootstrap Form generator
 */
class DivTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Div
     */
    protected $div;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->div = new Div();
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
        $div = new Div(array('red'=>'brown'), array('foo'=>'bar'));
        
        $this->assertSame('brown', $div->getAttr('red'));
        $this->assertSame('bar', $div->getOption('foo'));
    }
    
    
    /**
     * Test getting an attribute
     */
    public function testSetAttr()
    {
        $this->div->setAttr('foo', 'bar');
        $this->assertSame('bar', $this->div->getAttr('foo'));
    }
    
    /**
     * Test getting an option
     */
    public function testSetOption()
    {
        $this->div->setOption('foo', 'bar');
        $this->assertSame('bar', $this->div->getOption('foo'));
    }

    
    /**
     * Test adding a child div
     */
    public function testAdd()
    {
        $child = new Div();
        $div = $this->div->add($child);
        
        $this->assertSame($this->div, $div);
        $this->assertContains($child, $this->div->getElements());
        $this->assertSame($this->div, $child->getParent());
    }
    
    /**
     * Test method end
     * @depends testAdd
     */
    public function testEnd()
    {
        $child = new Div();
        $this->div->add($child);
        
        $this->assertSame($this->div, $child->end());
    }
    
    /**
     * Test rendering a div
     */
    public function testRender()
    {
        $html = <<<HTML
<div>
<div>Test</div>
<p>foobar</p>
</div>
HTML;
        
        $this->div->add('<div>Test</div>');
        
        $element = $this->getMockBuilder('Jasny\FormBuilder\Element')->getMockForAbstractClass();
        $element->expects($this->once())->method('render')->will($this->returnValue('<p>foobar</p>'));
        $this->div->add($element);
        
        $this->assertSame($html, (string)$this->div);
    }

    /**
     * Test rendering a div with subdivs.
     * @depends testRender
     */
    public function testRender_withSubdivs()
    {
        $html = <<<HTML
<div>
<div>Test</div>
<div class="subdiv">
<div>Child</div>
<p>foobar</p>
</div>
</div>
HTML;
        
        $this->div->add('<div>Test</div>');
        
        $child = new Div(array('class'=>'subdiv'));
        $child->add('<div>Child</div>');
        $this->div->add($child);
        
        $element = $this->getMockBuilder('Jasny\FormBuilder\Element')->getMockForAbstractClass();
        $element->expects($this->once())->method('render')->will($this->returnValue('<p>foobar</p>'));
        $child->add($element);
        
        $this->assertSame($html, (string)$this->div);
    }

    /**
     * Test rendering a div with HTML tidy.
     * @depends testRender_withSubdivs
     */
    public function testRender_tidy()
    {
        if (!class_exists('tidy')) $this->markTestSkipped("Tidy extension not available");
        
        $html = <<<HTML
<div>
  <div>
    Test
  </div>
  <div class="subdiv">
    <div>
      Child
    </div>
    <p>
      foobar
    </p>
  </div>
</div>
HTML;
        
        $this->div->setOption('tidy', array('indent'=>true));
        
        $this->div->add('<div>Test</div>');
        
        $child = new Div(array('class'=>'subdiv'));
        $child->add('<div>Child</div>');
        $this->div->add($child);
        
        $element = $this->getMockBuilder('Jasny\FormBuilder\Element')->getMockForAbstractClass();
        $element->expects($this->once())->method('render')->will($this->returnValue('<p>foobar</p>'));
        $child->add($element);
        
        $this->assertSame($html, (string)$this->div);
    }
}
