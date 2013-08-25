<?php

namespace Jasny\FormBuilder\Bootstrap;

/**
 * Tests <form> for Bootstrap Form generator
 */
class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Form
     */
    protected $form;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->form = new Form();
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
        $form = new Form(array('red'=>'brown'), array('foo'=>'bar'));
        
        $this->assertSame('brown', $form->getAttr('red'));
        $this->assertSame('POST', $form->getAttr('method'));
        $this->assertSame('bar', $form->getOption('foo'));
    }
    
    
    /**
     * Set setting single attributes
     */
    public function testSetAttr()
    {
        $this->form->setAttr('foo', 'bar');
        $this->form->setAttr('zoo', 123);
        $attr = $this->form->getAttrs(false);

        $this->assertArrayHasKey('method', $attr);
        $this->assertSame('POST', $attr['method']);
        
        $this->assertArrayHasKey('foo', $attr);
        $this->assertSame('bar', $attr['foo']);
         
        $this->assertArrayHasKey('zoo', $attr);
        $this->assertSame(123, $attr['zoo']);
    }

    /**
     * Test setting multiple attributes
     */
    public function testSetAttr_Array()
    {
        $this->form->setAttr(array(
            'foo'=> 'bar',
            'zoo'=> 123
        ));
        $attr = $this->form->getAttrs(false);
        
        $this->assertArrayHasKey('method', $attr);
        $this->assertSame('POST', $attr['method']);
        
        $this->assertArrayHasKey('foo', $attr);
        $this->assertSame('bar', $attr['foo']);
         
        $this->assertArrayHasKey('zoo', $attr);
        $this->assertSame(123, $attr['zoo']);
    }

    /**
     * Test getting an attribute
     */
    public function testGetAttr()
    {
        $this->form->setAttr('foo', 'bar');
        $this->assertSame('bar', $this->form->getAttr('foo'));
    }
    
    
    /**
     * Test setting single options
     */
    public function testSetOption()
    {
        $this->form->setOption('foo', 'bar');
        $this->form->setOption('zoo', false);
        $options = $this->form->getOptions(false);
        
        $this->assertArrayHasKey('foo', $options);
        $this->assertSame('bar', $options['foo']);
         
        $this->assertArrayHasKey('zoo', $options);
        $this->assertFalse($options['zoo']);
        
        $this->form->setOption('foo', null);
        $options = $this->form->getOptions();
        
        $this->assertArrayNotHasKey('foo', $options);
        $this->assertArrayHasKey('zoo', $options);
    }

    /**
     * Test setting multiple options
     */
    public function testSetOption_Array()
    {
        $this->form->setOption(['foo' => 'bar', 'zoo' => false]);
        $options = $this->form->getOptions();
        
        $this->assertArrayHasKey('foo', $options);
        $this->assertSame('bar', $options['foo']);
         
        $this->assertArrayHasKey('zoo', $options);
        $this->assertSame(false, $options['zoo']);
        
        $this->form->setOption(array('foo'=>null));
        $options = $this->form->getOptions();
        
        $this->assertArrayNotHasKey('foo', $options);
        $this->assertArrayHasKey('zoo', $options);
    }

    /**
     * Test getting an option
     */
    public function testGetOption()
    {
        $this->form->setOption('foo', 'bar');
        $this->assertSame('bar', $this->form->getOption('foo'));
        
        $this->assertNull($this->form->getOption('non-existent'));
    }
    
    /**
     * Test getting single options with bubble
     * @depends testGetOption
     */
    public function testGetOption_Bubble()
    {
        $this->form->setOption('zoo', 999);
        $this->form->setOption('foo', 'bar');
        $this->form->setOption('error:min', 'minimal');
        
        $element = $this->getMockBuilder('Jasny\FormBuilder\Element')->getMockForAbstractClass();
        $element->setOption('zoo', 123);
        $this->form->add($element);
        
        $this->assertSame(123, $element->getOption('zoo'));
        $this->assertSame('bar', $element->getOption('foo'));
        $this->assertSame('minimal', $element->getOption('error:min'));
        $this->assertSame(Form::$defaults['error:max'], $element->getOption('error:max'));

        $this->assertNull($element->getOption('non-existent'));
    }
    
    /**
     * Test getting options with defaults
     */
    public function testGetOptions()
    {
        $options = ['foo'=>'bar', 'error:min'=>'minimal'] + Form::$defaults + ['container' => true, 'label' => true];
        
        $this->form->setOption('foo', 'bar');
        $this->form->setOption('error:min', 'minimal');
        
        $this->assertSame($options, $this->form->getOptions());
    }
    
    /**
     * Test getting options with bubble
     * @depends testGetOptions
     */
    public function testGetOptions_Bubble()
    {
        $options = array('zoo'=>123, 'foo'=>'bar', 'error:min'=>'minimal')
            + Form::$defaults  + ['container' => true, 'label' => true];

        $this->form->setOption('zoo', 999);
        $this->form->setOption('foo', 'bar');
        $this->form->setOption('error:min', 'minimal');
        
        $element = $this->getMockBuilder('Jasny\FormBuilder\Element')->getMockForAbstractClass();
        $element->setOption('zoo', 123);
        $this->form->add($element);
        
        $this->assertSame($options, $element->getOptions());
    }
    
    /**
     * Test getting the id
     */
    public function testGetId()
    {
        $this->form->setAttr('id', 'foobar');
        $this->assertSame('foobar', $this->form->getId());
        
        $this->form->setAttr('id', null);
        $id = $this->form->getId();
        $this->form->setAttr('name', 'test');
        $this->assertSame($id, $this->form->getId());
        
        $this->form->setAttr('id', null);
        $this->form->setAttr('name', 'test');
        $id = $this->form->getId();
        $this->assertSame('test-form', $this->form->getId());
    }
    
    
    /**
     * Test adding HTML to a form
     */
    public function testAdd()
    {
        $div = '<div>Test</div>';
        $form = $this->form->add($div);
        
        $this->assertSame($this->form, $form);
        $this->assertContains($div, $this->form->getElements());
    }
    
    /**
     * Test adding an element to a form
     */
    public function testAdd_Element()
    {
        $element = $this->getMockBuilder('Jasny\FormBuilder\Element')->getMockForAbstractClass();
        $form = $this->form->add($element);
        
        $this->assertSame($this->form, $form);
        $this->assertContains($element, $this->form->getElements());
        $this->assertSame($this->form, $element->getParent());
    }
    
    
    /**
     * Test rendering a form
     */
    public function testRender()
    {
        $html = <<<HTML
<form method="POST">
</form>
HTML;
        
        $this->assertSame($html, (string)$this->form);
    }

    /**
     * Test rendering a form with attributes
     * @depends testRender
     */
    public function testRender_withAttr()
    {
        $html = <<<HTML
<form method="GET" action="test.php" class="form-horizontal" data-foo="data-foo">
</form>
HTML;

        $this->form->setAttr(array(
            'method' => 'GET',
            'action' => 'test.php',
            'class' => 'form-horizontal',
            'data-foo' => true,
            'data-bar' => false
        ));
        
        $this->assertSame($html, (string)$this->form);
    }

    /**
     * Test rendering a form with elements
     * @depends testRender
     */
    public function testRender_withElements()
    {
        $html = <<<HTML
<form method="POST">
<div>Test</div>
<p>foobar</p>
</form>
HTML;
        
        $this->form->add('<div>Test</div>');
        
        $element = $this->getMockBuilder('Jasny\FormBuilder\Element')->getMockForAbstractClass();
        $element->expects($this->once())->method('render')->will($this->returnValue('<p>foobar</p>'));
        $this->form->add($element);
        
        $this->assertSame($html, (string)$this->form);
    }

    
    /**
     * Test validate
     */
    public function testIsValid()
    {
        $this->form->add('<div>Test</div>');
        
        $element_valid = $this->getMockBuilder('Jasny\FormBuilder\Element')->getMockForAbstractClass();
        $element_valid->expects($this->exactly(2))->method('isValid')->will($this->returnValue(true));
        $element_invalid = $this->getMockBuilder('Jasny\FormBuilder\Element')->getMockForAbstractClass();
        $element_invalid->expects($this->once())->method('isValid')->will($this->returnValue(false));
        
        $this->form->add($element_valid);
        $this->assertTrue($this->form->isValid());
        
        $this->form->add($element_invalid);
        $this->assertFalse($this->form->isValid());
    }
}
