<?php
require_once 'vfsStream/vfsStream.php';
vfsStreamWrapper::register();

class Terminalor_ApplicationTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var Terminalor
     */
    private $_terminalor = null;

    public function  setUp()
    {
        $templateMock  = $this->getMock('Terminalor_DocParser_Template_Standard');
        $docParserMock = $this->getMock('Terminalor_DocParser', array(), array($templateMock));

        /* @var $entityMock PHPUnit_Framework_MockObject_MockObject */
        $entityMock = $this->getMock('Terminalor_Response_Styles_Entity',
            array('flush',
                'setProperties',
                'getId',
                'getBackgroundName',
                'getColorName', 
                'getBackgroundValue',
                'getColorValue'));

        $entityMock->expects($this->any())
            ->method('flush')
            ->will($this->returnSelf());

        $entityMock->expects($this->any())
            ->method('setProperties')
            ->with($this->isType('array'))
            ->will($this->returnSelf());

        $entityMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue('normal'));

        $styleMock = $this->getMock('Terminalor_Response_Styles', 
            array('getStyleByName', 'getStyleValues'),
            array($entityMock));
        
        $responseMock = $this->getMock('Terminalor_Response', 
            array('getInputStream', 'getOutputStream'),
            array($styleMock, $docParserMock));

        $root = vfsStream::newDirectory('streams');
        vfsStreamWrapper::setRoot($root);

        $inputStream  = vfsStream::newFile('input');
        $outputStream = vfsStream::newFile('output');
        $root->addChild($inputStream);
        $root->addChild($outputStream);

        $responseMock->expects($this->any())
            ->method('getInputStream')
            ->will($this->returnValue(vfsStream::url('streams/input')));

        $responseMock->expects($this->any())
            ->method('getOutputStream')
            ->will($this->returnValue(vfsStream::url('streams/output')));

        $routerMock = $this->getMock('Terminalor_Request_Router_Standard');

        $requestMock = $this->getMock('Terminalor_Request', array(), array($routerMock));
        $this->_terminalor = new Terminalor_Application($responseMock, $requestMock);
    }

    public function testGetResonseMethodReturnInstanceOfTerminalorResponseInterface()
    {
        $this->assertInstanceOf('Terminalor_Response_Interface', $this->_terminalor->getResponse());
    }
    
    public function testGetRequestMethodReturnInstanceOfTerminalorRequestInterface()
    {
        $this->assertInstanceOf('Terminalor_Request_Interface', $this->_terminalor->getRequest());
    }

    public function testGetCommandsMethodReturnEmptyArrayAfterInit()
    {
        $this->assertEmpty($this->_terminalor->getCommands());
    }

    public function testHelpMethodReturnNull()
    {
        $defStyle  = $this->_mockStyleEntity('normal', null, null, null, null);

        /* @var $styleMock PHPUnit_Framework_MockObject_MockObject */
        $styleMock = $this->_terminalor->getResponse()->getStyle();
        $styleMock->expects($this->atLeastOnce())
            ->method('getStyleValues')
            ->will($this->returnValue($defStyle));
        
        $this->assertNull($this->_terminalor->help());
    }
    /**
     * @expectedException InvalidArgumentException
     */
    public function testHelpMethodThrowExceptionIfGivenCommandDoesntExists()
    {
        $this->_terminalor->help('index');
    }
    
    public function testHelpMethodDisplayHelpForGivenCommand()
    {
        $docMock = $this->_terminalor->getResponse()->getDocParser();
        $docMock->expects($this->any())
            ->method('setCommandName')
            ->will($this->returnSelf());
        $docMock->expects($this->any())
            ->method('setRawDocComment')
            ->will($this->returnSelf());
        $docMock->expects($this->any())
            ->method('parseRawDocComment')
            ->will($this->returnSelf());
        $docMock->expects($this->any())
            ->method('buildHelp')
            ->will($this->returnValue('command help'));
        $filename = $this->_terminalor->getResponse()->getOutputStream();
        $this->_terminalor['index'] = function(){};
        $this->_terminalor->help('index');
        $message = file_get_contents($filename);
        $this->assertEquals('command help', $message);
    }

    public function testGetLibraryPathReturnLibraryFolder()
    {
        $path = $this->_terminalor->getLibraryPath();
        $spl = new SplFileInfo($path);
        $this->assertTrue($spl->isDir());
        $this->assertEquals('library', $spl->getFilename());
    }

    public function testOffsetSetMethodAddNewCommandWithUniqueName()
    {
        $this->_terminalor['commandname'] = function(){};
        $this->assertTrue(isset($this->_terminalor['commandname']));
    }

    public function testOffsetGetMethodReturnClouseForExistingCommand()
    {
        $this->_terminalor['commandname'] = function(){};
        $this->assertInstanceOf('Closure', $this->_terminalor['commandname']);
    }

    public function testOffsetSetMethodDontOverwriteExistingCommands()
    {
        $errorStyle  = $this->_mockStyleEntity();
        /* @var $styleMock PHPUnit_Framework_MockObject_MockObject */
        $styleMock = $this->_terminalor->getResponse()->getStyle();
        $styleMock->expects($this->atLeastOnce())
            ->method('getStyleValues')
            ->with($this->equalTo('error'))
            ->will($this->returnValue($errorStyle));

        $f  = function(){ return 'a';};
        $f2 = function(){ return 'b';};
        $this->_terminalor['commandname'] = $f;
        $this->_terminalor['commandname'] = $f2;
        $closure = $this->_terminalor['commandname'];
        $this->assertEquals($f(), $closure());

        $filename = $this->_terminalor->getResponse()->getOutputStream();
        $actual = file_get_contents($filename);
        $this->assertRegExp('/already exists\.$/', $this->_clearMessage($actual));
    }

    public function testOffsetSetMethodDontAddNewCommandIfItIsNotClosure()
    {
        $errorStyle  = $this->_mockStyleEntity();
        /* @var $styleMock PHPUnit_Framework_MockObject_MockObject */
        $styleMock = $this->_terminalor->getResponse()->getStyle();
        $styleMock->expects($this->atLeastOnce())
            ->method('getStyleValues')
            ->with($this->equalTo('error'))
            ->will($this->returnValue($errorStyle));

        $this->_terminalor['commandname'] = null;
        $this->assertFalse(isset($this->_terminalor['commandname']));

        $filename = $this->_terminalor->getResponse()->getOutputStream();
        $actual = file_get_contents($filename);
        $this->assertRegExp('/not closure\.$/', $this->_clearMessage($actual));
    }

    public function testOffsetUnsetMethodRemoveExistingCommand()
    {
        $this->_terminalor['commandname'] = function(){};
        unset($this->_terminalor['commandname']);
        $this->assertFalse(isset($this->_terminalor['commandname']));
    }
    
    public function testOffsetUnsetMethodDisplayErrorMessageIfGivenCommandNotExists()
    {
        $errorStyle  = $this->_mockStyleEntity();
        /* @var $styleMock PHPUnit_Framework_MockObject_MockObject */
        $styleMock = $this->_terminalor->getResponse()->getStyle();
        $styleMock->expects($this->atLeastOnce())
            ->method('getStyleValues')
            ->with($this->equalTo('error'))
            ->will($this->returnValue($errorStyle));
        
        unset($this->_terminalor['unknowncommand']);
        $filename = $this->_terminalor->getResponse()->getOutputStream();
        $actual = file_get_contents($filename);
        $this->assertRegExp('/It doesn\'t exists\.$/', $this->_clearMessage($actual));
    }

    public function testOffsetGetDisplayErrorIfNotExistingCommandNameGiven()
    {
        $errorStyle  = $this->_mockStyleEntity();
        /* @var $styleMock PHPUnit_Framework_MockObject_MockObject */
        $styleMock = $this->_terminalor->getResponse()->getStyle();
        $styleMock->expects($this->atLeastOnce())
            ->method('getStyleValues')
            ->with($this->equalTo('error'))
            ->will($this->returnValue($errorStyle));

        $a = $this->_terminalor['unknowncommand'];

        $filename = $this->_terminalor->getResponse()->getOutputStream();
        $actual = file_get_contents($filename);
        $this->assertRegExp('/It doesn\'t exists\.$/', $this->_clearMessage($actual));
    }

    public function testOffsetUnsetDisplayErrorIfNotExistingCommandNameGiven()
    {
        $errorStyle  = $this->_mockStyleEntity();
        /* @var $styleMock PHPUnit_Framework_MockObject_MockObject */
        $styleMock = $this->_terminalor->getResponse()->getStyle();
        $styleMock->expects($this->atLeastOnce())
            ->method('getStyleValues')
            ->with($this->equalTo('error'))
            ->will($this->returnValue($errorStyle));

        $a = $this->_terminalor['unknowncommand'];

        $filename = $this->_terminalor->getResponse()->getOutputStream();
        $actual = file_get_contents($filename);
        $this->assertRegExp('/It doesn\'t exists\.$/', $this->_clearMessage($actual));
    }

    public function testCountMethodReturnNumberOfInstalledCommands()
    {
        $this->_terminalor['one'] = function(){};
        $this->_terminalor['two'] = function(){};
        $this->assertEquals(2, count($this->_terminalor));
    }

    public function testTerminalorIsTraversable()
    {
        $this->assertInstanceOf('Traversable', $this->_terminalor);
    }

    public function testFlushMethodReturnSelf()
    {
        $this->assertInstanceOf('Terminalor_Application_Interface',
            $this->_terminalor->flush());
    }

    public function testFlushDestroyAllDefinedCommands()
    {
        $this->_terminalor['one'] = function(){};
        $this->_terminalor['two'] = function(){};
        $this->assertEquals(2, count($this->_terminalor));
        $this->_terminalor->flush();
        $this->assertEquals(0, count($this->_terminalor));
    }

    public function testIteratorMethodsReturnEqualClosuresToArrayAccsess()
    {
        $f = function(){ return 'one'; };
        $f2 = function(){ return 'two'; };
        $this->_terminalor['one'] = $f;
        $this->_terminalor['two'] = $f2;
        foreach ($this->_terminalor as $name => $command) {
            $this->assertEquals($this->_terminalor[$name](), $command());
        }
    }

    public function testToStringMethodReturnNull()
    {
        $errorStyle  = $this->_mockStyleEntity();
        /* @var $styleMock PHPUnit_Framework_MockObject_MockObject */
        $styleMock = $this->_terminalor->getResponse()->getStyle();
        $styleMock->expects($this->atLeastOnce())
            ->method('getStyleValues')
            ->with($this->equalTo('error'))
            ->will($this->returnValue($errorStyle));

        $this->assertNull($this->_terminalor->__toString());
    }

    public function testToStringDisplayHelpIfBadFunctionCallExceptionIfFetched()
    {
        $errorStyle  = $this->_mockStyleEntity();
        /* @var $styleMock PHPUnit_Framework_MockObject_MockObject */
        $styleMock = $this->_terminalor->getResponse()->getStyle();
        $styleMock->expects($this->atLeastOnce())
            ->method('getStyleValues')
            ->with($this->equalTo('error'))
            ->will($this->returnValue($errorStyle));
        
        $requestMock = $this->_terminalor->getRequest();
        $requestMock
            ->expects($this->atLeastOnce())
            ->method('dispatch')
            ->will($this->throwException(new BadFunctionCallException));

        $docMock = $this->_terminalor->getResponse()->getDocParser();
        $docMock->expects($this->any())->method('buildHelp')
            ->will($this->returnValue('bad command'));
        $this->_terminalor->__toString();
         $filename = $this->_terminalor->getResponse()->getOutputStream();
         $actual = file_get_contents($filename);

         $this->assertEquals('bad command', $actual);
    }

    private function _mockStyleEntity($id = 'error', $colorName = 'white', $backgroundName = 'red' ,
        $colorValue = '1;37', $backgroundValue = '41')
    {
        $entityMock = $this->_terminalor
            ->getResponse()
            ->getStyle()
            ->getEntity();

        /* @var $errorStyle PHPUnit_Framework_MockObject_MockObject */
        $errorStyle = clone $entityMock;

        $errorStyle->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($id));

        $errorStyle->expects($this->any())
            ->method('getColorName')
            ->will($this->returnValue($colorName));

        $errorStyle->expects($this->any())
            ->method('getBackgroundName')
            ->will($this->returnValue($backgroundName));

        $errorStyle->expects($this->any())
            ->method('getColorValue')
            ->will($this->returnValue($colorValue));

        $errorStyle->expects($this->any())
            ->method('getBackgroundValue')
            ->will($this->returnValue($backgroundValue));

        return $errorStyle;
    }

    private function _clearMessage($message)
    {
        return str_replace(array(
            "\n",
            "\033",
            "[0m",
        ), '', $message);
    }
    
    public function  tearDown()
    {
        $this->_terminalor = null;
    }
}