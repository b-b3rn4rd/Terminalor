<?php
class Bar
{

}

class Foo
{
    public $bar = null;

    public function  __construct($bar = null)
    {
        $this->bar = $bar;
    }
}

class Terminalor_ContainerTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var Terminalor_Container
     */
    private $_container;

    public function setUp()
    {
        $this->_container = new Terminalor_Container();
    }

    public function testByDefaultInstancesAreEmpty()
    {
        $this->assertEmpty($this->_container->getInstances());
    }

    public function testSetDefaultDependenciesInitTerminalor()
    {
        $this->_container->setDefaultDependencies();
        $terminalor = $this->_container->get('Terminalor.Application');
        $this->assertInstanceOf('Terminalor_Application_Interface', $terminalor);
    }

    public function testAddAndRetriewSameObject()
    {
        $this->_container->add('testFoo', 'Foo');
        $this->_container->get('testFoo');
        $this->assertInstanceOf('Foo', $this->_container->get('testFoo'));
    }

    public function testAddObjectWithStringParams()
    {
        $this->_container->add('testFoo', 'Foo', array('bar'));
        $testFoo = $this->_container->get('testFoo');
        
        $this->assertEquals('bar', $testFoo->bar);
    }

    public function testAddObjectWithObjectParams()
    {
        $this->_container->add('testBar', 'Bar');
        $this->_container->executeInstances();
        $this->_container->add('testFoo', 'Foo', array('testBar'));
        $testFoo = $this->_container->get('testFoo');
        
        $this->assertInstanceOf('Bar', $testFoo->bar);
    }

    /**
     * @expectedException  InvalidArgumentException
     */
    public function testGetUnregistredObjectThrowException()
    {
        $this->_container->get('ObjectDoesntExist');
    }

    public function testInstanceMethodShouldReturnNullIfObjectExists()
    {
        $this->_container->add('testFoo', 'Foo');
        $this->_container->executeInstances();
        $method = new ReflectionMethod('Terminalor_Container', '_instance');
        $method->setAccessible(true);
        $this->assertNull($method->invokeArgs(
            $this->_container,
            array('testFoo', 'Bar')));
    }
    
    public function testInstanceMethodCanRecursivelyAssembleClassArguments()
    {
        $this->_container->add('testBar', 'Bar');
        $this->_container->add('testFoo', 'Foo', array('testBar'));
        
        $method = new ReflectionMethod('Terminalor_Container', '_instance');
        $method->setAccessible(true);
        $actual = $method->invokeArgs(
            $this->_container,
            array('testFoo', 'Foo', array('testBar')));
        $this->assertInstanceOf('Bar', $actual->bar);
    }

    public function testGetInstanceDontAllowToCreateNewInstance()
    {
        $terminalor = Terminalor_Container::getInstance();
        $terminalor->add('testBar', 'Bar');
        $terminalor = Terminalor_Container::getInstance();
        $this->assertInstanceOf('Bar', $terminalor->get('testBar'));
    }

    public function  tearDown()
    {
        $this->_container = null;
    }
}

