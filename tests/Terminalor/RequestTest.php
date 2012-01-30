<?php
class Terminalor_RequestTest extends PHPUnit_Framework_TestCase
{
    public function testGetRouterReturnRouterInterface()
    {
        $router = $this->_getRouterMock();
        $request = new Terminalor_Request($router);
        $this->assertInstanceOf('Terminalor_Request_Router_Interface', $request->getRouter());
    }

    public function testGetArgumentsAfterInitReturnEmptyArray()
    {
        $router = $this->_getRouterMock();
        $request = new Terminalor_Request($router);
        $this->assertEmpty($request->getArguments());
    }

    public function testSetArgumentsReturnSelf()
    {
        $router = $this->_getRouterMock();
        $request = new Terminalor_Request($router);
        $this->assertInstanceOf('Terminalor_Request_Interface', $request->setArguments(array()));
    }

    public function testSetArgumentsAbleToSetDefaultArgumentsValues()
    {
        $router = $this->_getRouterMock(array('parse'));

        $router->expects($this->once())
            ->method('parse')
            ->will($this->returnValue(array(
                'command' => 'index'
            )))
            ->with($this->isNull());

        $request = new Terminalor_Request($router);
        $request->setArguments(array(
            'name' => 'bernard'
        ));
        $request->dispatch(array(
            'index' => function($name) {
                
            }
        ));
        $this->assertEquals('bernard', $request->getArgument('name'));
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testDispatchMethodExpectArray()
    {
        $router  = $this->_getRouterMock();
        $request = new Terminalor_Request($router);
        $request->dispatch('notAnArray');
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testDispatchMethodThrowExceptionIfCommandNotExists()
    {
        $router = $this->_getRouterMock(array('parse'));
        
        $router->expects($this->once())
            ->method('parse')
            ->will($this->returnValue(array(
                'command' => 'commandNotExists'
            )))
            ->with($this->isNull());

        $request = new Terminalor_Request($router);
        $request->dispatch(array(
            'index' => function() {}
        ));
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testDispatchMethodThrowExceptionIfDefaultCommandIsNotSpecified()
    {
        $router = $this->_getRouterMock(array('parse'));

        $router->expects($this->once())
            ->method('parse')
            ->will($this->returnValue(array(
                'command' => null
            )))
            ->with($this->isNull());

        $request = new Terminalor_Request($router);
        $request->dispatch(array(
            'index' => function() {}
        ));
    }

    public function testDispatchMethodReturnFalseIfHelpArgumentIsFound()
    {
        $router = $this->_getRouterMock(array('parse'));

        $router->expects($this->once())
            ->method('parse')
            ->will($this->returnValue(array(
                'command' => 'index',
                'help' => true
            )))
            ->with($this->isNull());

        $request = new Terminalor_Request($router);
        $return = $request->dispatch(array(
            'index' => function() {}
        ));

        $this->assertFalse($return);
    }

    public function testDispatchMethodMakeArgumentsAccessable()
    {
        $router = $this->_getRouterMock(array('parse'));

        $router->expects($this->once())
            ->method('parse')
            ->will($this->returnValue(array(
                'command' => 'index',
                'name'    => 'bernard'
            )))
            ->with($this->isNull());

        $request = new Terminalor_Request($router);
        $return = $request->dispatch(array(
            'index' => function() {}
        ));

        $this->assertEquals('bernard', $request->getArgument('name'));
    }

    public function testGetArgumentReturnDefaultValueIfNotExists()
    {
        $router  = $this->_getRouterMock();
        $request = new Terminalor_Request($router);
        $value = $request->getArgument('notExist', 'noValue');

        $this->assertEquals('noValue', $value);
    }

    public function testSetArgumentMethodCanSetArgument()
    {
        $router  = $this->_getRouterMock();
        $request = new Terminalor_Request($router);
        $request->setArgument('name', 'bernard');

        $this->assertEquals('bernard', $request->getArgument('name'));
    }

    public function testDispatchMethodReturnTrueIfDispatchedSuccesful()
    {
        $router = $this->_getRouterMock(array('parse'));

        $router->expects($this->once())
            ->method('parse')
            ->will($this->returnValue(array(
                'command' => 'index',
                'name'    => 'bernard'
            )))
            ->with($this->isNull());

        $request = new Terminalor_Request($router);
        $return = $request->dispatch(array(
            'index' => function() {}
        ));

        $this->assertTrue($return);
    }
    /**
     * @expectedException BadFunctionCallException
     */
    public function testAssembleFunctionArgsThrowsExceptionIfCommandArgumentsAreMissing()
    {
        $router = $this->_getRouterMock(array('parse'));

        $router->expects($this->once())
            ->method('parse')
            ->will($this->returnValue(array(
                'command' => 'index'
            )))
            ->with($this->isNull());

        $request = new Terminalor_Request($router);
        $request->dispatch(array(
            'index' => function($name){}
        ));
        $method = new ReflectionMethod('Terminalor_Request', '_assembleFunctionArgs');
        $method->setAccessible(true);
        $method->invokeArgs($request, array(
            new ReflectionFunction(function($name){}),
            'index'
        ));
    }

    public function testAssembleFunctionArgsUseDefaultArgumentsValueIfPossible()
    {
        $router = $this->_getRouterMock(array('parse'));

        $router->expects($this->once())
            ->method('parse')
            ->will($this->returnValue(array(
                'command' => 'index'
            )))
            ->with($this->isNull());

        $request = new Terminalor_Request($router);
        $request->dispatch(array(
            'index' => function($name = 'bernard'){}
        ));
        $method = new ReflectionMethod('Terminalor_Request', '_assembleFunctionArgs');
        $method->setAccessible(true);
        $args = $method->invokeArgs($request, array(
            new ReflectionFunction(function($name = 'bernard'){}),
            'index'
        ));
        
        $this->assertContains('bernard', $args);
    }

    public function testAssembleFunctionArgsAssingUserValuesToArgs()
    {
        $router = $this->_getRouterMock(array('parse'));

        $router->expects($this->once())
            ->method('parse')
            ->will($this->returnValue(array(
                'command' => 'index',
                'name'    => 'bernard'
            )))
            ->with($this->isNull());

        $request = new Terminalor_Request($router);
        $request->dispatch(array(
            'index' => function($name = 'bernard'){}
        ));
        $method = new ReflectionMethod('Terminalor_Request', '_assembleFunctionArgs');
        $method->setAccessible(true);
        $args = $method->invokeArgs($request, array(
            new ReflectionFunction(function($name){}),
            'index'
        ));

        $this->assertContains('bernard', $args);
    }

    /**
     *
     * @param array $methods
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    private function _getRouterMock($methods = array())
    {
        return $this->getMock('Terminalor_Request_Router_Standard', $methods);
    }
}
