<?php
class Terminalor_RegistryTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var Terminalor_Registry
     */
    private $_registry;

    public function setUp()
    {
        $this->_registry = new Terminalor_Registry();
    }

    public function testGetMethodReturnDefaultValueIfNotExists()
    {
        $this->assertEquals('notExists',
            $this->_registry->get('foo', 'notExists'));
    }
    
    public function testSetAllowToStoreValue()
    {
        $this->_registry->set('foo', 'baz');
        $this->assertEquals('baz', $this->_registry->get('foo'));
    }

    public function testSetAllowReplaceValueValue()
    {
        $this->_registry->set('foo', 'baz');
        $this->_registry->set('foo', 'bar');
        $this->assertEquals('bar', $this->_registry->get('foo'));
    }

    public function testHasMethodReturnTrueIfValueExists()
    {
        $this->assertTrue($this->_registry->has('foo'));
    }


    public function  tearDown()
    {
        $this->_registry = null;
    }

}

