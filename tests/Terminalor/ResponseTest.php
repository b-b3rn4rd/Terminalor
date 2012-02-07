<?php
require_once 'vfsStream/vfsStream.php';
vfsStreamWrapper::register();

class Terminalor_ResponseTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var Terminalor_Response
     */
    private $_response = null;

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
                'getColorValue',
                'hasBold',
                'hasUnderline'));

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
        
        $this->_response = new Terminalor_Response($styleMock, $docParserMock);

        // vfs setup
        $root = vfsStream::newDirectory('streams');
        vfsStreamWrapper::setRoot($root);
        
        $inputStream  = vfsStream::newFile('input');
        $outputStream = vfsStream::newFile('output');
        $root->addChild($inputStream);
        $root->addChild($outputStream);
        
        $this->_response->setInputStream(vfsStream::url('streams/input'));
        $this->_response->setOutputStream(vfsStream::url('streams/output'));
    }

    public function testGetStyleReturnInstanceOfTerminalorResponseStylesInterface()
    {
        $this->assertInstanceOf('Terminalor_Response_Styles_Interface', 
            $this->_response->getStyle());
    }
    
    public function testgetDocParserReturnInstanceOfTerminalorDocParserInterface()
    {
        $this->assertInstanceOf('Terminalor_DocParser_Interface',
            $this->_response->getDocParser());
    }

    public function testGetInputStreamReturnReadableFilename()
    {
        $filename = $this->_response->getInputStream();
        $this->assertTrue(is_readable($filename));
    }

    public function testGetOutputStreamReturnWritableFilename()
    {
        $filename = $this->_response->getOutputStream();
        $this->assertTrue(is_writable($filename));
    }

    public function testSetInputStreamReturnSelf()
    {
        $this->assertInstanceOf('Terminalor_Response_Interface',
            $this->_response->setInputStream(''));
    }

    public function testSetOutputStreamReturnSelf()
    {
        $this->assertInstanceOf('Terminalor_Response_Interface',
            $this->_response->setOutputStream(''));
    }

    public function testSendRawMessageReturnFalseIfMessageIsNotString()
    {
        $errorStyle = $this->_mockStyleEntity();
        /* @var $styleMock PHPUnit_Framework_MockObject_MockObject */
        $styleMock = $this->_response->getStyle();
        $styleMock->expects($this->atLeastOnce())
            ->method('getStyleValues')
            ->with($this->equalTo('error'))
            ->will($this->returnValue($errorStyle));

        $actual = $this->_response->sendRawMessage(null);
        $this->assertFalse($actual);
    }

    public function testSendRawMessageDisplayErrorIfMessageIsNotString()
    {
        $errorStyle = $this->_mockStyleEntity();
        /* @var $styleMock PHPUnit_Framework_MockObject_MockObject */
        $styleMock = $this->_response->getStyle();
        $styleMock->expects($this->atLeastOnce())
            ->method('getStyleValues')
            ->with($this->equalTo('error'))
            ->will($this->returnValue($errorStyle));

        $this->_response->sendRawMessage(null);
        $filename = $this->_response->getOutputStream();
        $message = file_get_contents($filename);
        $message = str_replace("\033", '', $message);
        $this->assertRegExp('/string expected$/', trim($message, "[0m\n"));
    }
    
    public function testSendRawMessageReturnTrueIfMessageIsString()
    {
        $actual   = $this->_response->sendRawMessage('string message');
        $this->assertTrue($actual);
    }

    public function testSendRawMessageOutputMessageWithOutModifications()
    {
       $message = 'string message';
       $this->_response->sendRawMessage($message);
       $filename = $this->_response->getOutputStream();
       $actual = file_get_contents($filename);
       $this->assertEquals($message, $actual);
    }

    public function testMessageReturnTrueIfMessageWasSent()
    {
        $defStyle = $this->_mockStyleEntity('normal', null, null, null, null);
        
        /* @var $styleMock PHPUnit_Framework_MockObject_MockObject */
        $styleMock = $this->_response->getStyle();
        $styleMock->expects($this->atLeastOnce())
            ->method('getStyleValues')
            ->with($this->isNull())
            ->will($this->returnValue($defStyle));

        $actual = $this->_response->message('string message');
        $this->assertTrue($actual);
    }

    public function testMessageOutputMessageEndingWithNewLineChar()
    {
        $defStyle   = $this->_mockStyleEntity('normal',null,null,null,null);

        /* @var $styleMock PHPUnit_Framework_MockObject_MockObject */
        $styleMock = $this->_response->getStyle();
        $styleMock->expects($this->atLeastOnce())
            ->method('getStyleValues')
            ->with($this->isNull())
            ->will($this->returnValue($defStyle));

        $this->_response->message('string message');
        $filename = $this->_response->getOutputStream();
        $this->assertRegExp('/\n$/', file_get_contents($filename));
    }

    public function testMessageOutputTableIfTraversableGiven()
    {
        $array = array(array('id' => 1, 'name' => 'bernard', 'email' => 'bernard@runawaylover.info'));
        $defStyle = $this->_mockStyleEntity('normal', null, null, null, null);

        /* @var $styleMock PHPUnit_Framework_MockObject_MockObject */
        $styleMock = $this->_response->getStyle();
        $styleMock->expects($this->atLeastOnce())
            ->method('getStyleValues')
            ->with($this->isNull())
            ->will($this->returnValue($defStyle));

        $this->_response->message($array);
        $filename = $this->_response->getOutputStream();
        $table = file_get_contents($filename);
        $this->assertEquals($this->_response->createTableFromArray($array),
            trim($table, "\033[0m\n"));
    }

    public function testCreateTableFromArrayAbleToAppendShortestRowsWithValidKeys()
    {
        $array = array(
            array('id' => 1, 'name' => 'bernard'),
            array('id' => 2, 'name' => 'john', 'age' => 25),
        );
        $keys = array_keys($array[1]);

        $table  = $this->_response->createTableFromArray($array);
        $rows   = explode("\n", $table);
        $header = explode(' | ', trim($rows[1], '|'));
        foreach($keys as $i => $key) {
            $this->assertEquals($key, trim($header[$i]));
        }
    }

    public function testCreateTableFromArrayAbleToLimitArrayUpTo2ndLevel()
    {
        $options = array('age' => '25', 'sex' => 'male');
        $array = array(
            array('id' => 1, 'name' => 'bernard', 'options' => $options),
            array('id' => 2, 'name' => 'john'),
        );
        $table = $this->_response->createTableFromArray($array);
        $rows  = explode("\n", $table);
        $row   = explode(' | ', trim($rows[3], '|'));
        $this->assertEquals(implode(', ', $options), trim($row[2]));
    }

    public function testMessageOutputMessageWithGivenStyle()
    {
        $style = $this->_mockStyleEntity('success', 'white', 'green', '1;37', '42');
        /* @var $styleMock PHPUnit_Framework_MockObject_MockObject */
        $styleMock = $this->_response->getStyle();
        $styleMock->expects($this->atLeastOnce())
            ->method('getStyleValues')
            ->with($this->equalTo('success'))
            ->will($this->returnValue($style));

        $this->_response->message('string message', 'success');
        $filename = $this->_response->getOutputStream();
        $actual = file_get_contents($filename);
        
        $this->assertRegExp('/^\033\[1;37m\033\[42mstring message\033\[0m$/', $actual);
        
    }

    public function testPromtOutputPromtSignIfItsNotNullOnNextLine()
    {
        $promtSign = '> ';
        $defStyle  = $this->_mockStyleEntity('normal', null, null, null, null);

        /* @var $styleMock PHPUnit_Framework_MockObject_MockObject */
        $styleMock = $this->_response->getStyle();
        $styleMock->expects($this->atLeastOnce())
            ->method('getStyleValues')
            ->with($this->isNull())
            ->will($this->returnValue($defStyle));

        $this->_response->promt('string message', null, $promtSign);
        $filename = $this->_response->getOutputStream();
        $actual = file_get_contents($filename);
        $this->assertEquals($promtSign, $actual);
    }

    public function testPromtReturnStringFromInputStream()
    {
        $defStyle  = $this->_mockStyleEntity('normal', null, null, null, null);

        /* @var $styleMock PHPUnit_Framework_MockObject_MockObject */
        $styleMock = $this->_response->getStyle();
        $styleMock->expects($this->atLeastOnce())
            ->method('getStyleValues')
            ->with($this->isNull())
            ->will($this->returnValue($defStyle));

        $filename = $this->_response->getInputStream();
        file_put_contents($filename, 'response message');
        $actual = $this->_response->promt('string message');
        
        $this->assertEquals('response message', $actual);
    }

    public function testConfirmMethodReturnTrueForYInInputStream()
    {
        $defStyle  = $this->_mockStyleEntity('normal', null, null, null, null);

        /* @var $styleMock PHPUnit_Framework_MockObject_MockObject */
        $styleMock = $this->_response->getStyle();
        $styleMock->expects($this->atLeastOnce())
            ->method('getStyleValues')
            ->with($this->isNull())
            ->will($this->returnValue($defStyle));

        $filename = $this->_response->getInputStream();
        file_put_contents($filename, 'y');

        $actual = $this->_response->confirm('string message', null, null);
        $this->assertTrue($actual);
    }

    public function testConfirmMethodIsCaseInsensitive()
    {
        $defStyle  = $this->_mockStyleEntity('normal', null, null, null, null);

        /* @var $styleMock PHPUnit_Framework_MockObject_MockObject */
        $styleMock = $this->_response->getStyle();
        $styleMock->expects($this->atLeastOnce())
            ->method('getStyleValues')
            ->with($this->isNull())
            ->will($this->returnValue($defStyle));

        $filename = $this->_response->getInputStream();
        file_put_contents($filename, 'Y');

        $actual = $this->_response->confirm('string message', null, null);
        $this->assertTrue($actual);
    }

    public function testConfirmMethodReturnFalseForNInInputStream()
    {
        $defStyle  = $this->_mockStyleEntity('normal', null, null, null, null);

        /* @var $styleMock PHPUnit_Framework_MockObject_MockObject */
        $styleMock = $this->_response->getStyle();
        $styleMock->expects($this->atLeastOnce())
            ->method('getStyleValues')
            ->with($this->isNull())
            ->will($this->returnValue($defStyle));

        $filename = $this->_response->getInputStream();
        file_put_contents($filename, 'n');

        $actual = $this->_response->confirm('string message', null, null);
        $this->assertFalse($actual);
    }

    public function testApplyStyleToMessageDisplayErrorMessageIfMessageIsNotString()
    {
        $errorStyle = $this->_mockStyleEntity();
        $defStyle  = $this->_mockStyleEntity('normal', null, null, null, null);

        /* @var $styleMock PHPUnit_Framework_MockObject_MockObject */
        $styleMock = $this->_response->getStyle();
        $styleMock->expects($this->at(0))
            ->method('getStyleValues')
            ->with($this->equalTo('error'))
            ->will($this->returnValue($errorStyle));
        
        $styleMock->expects($this->at(1))
            ->method('getStyleValues')
            ->with('normal')
            ->will($this->returnValue($defStyle));

        $this->_response->applyStyleToMessage(null);

        $filename = $this->_response->getOutputStream();
        $message = file_get_contents($filename);
        $message = str_replace("\033", '', $message);
        $this->assertRegExp('/string expected$/', trim($message, "[0m\n"));
    }

    public function testApplyStyleToMessageReturnGivenMessageWithAppliedStyle()
    {
        $style = $this->_mockStyleEntity('success', 'white', 'green', '1;37', '42', true, true);
        /* @var $styleMock PHPUnit_Framework_MockObject_MockObject */
        $styleMock = $this->_response->getStyle();
        $styleMock->expects($this->atLeastOnce())
            ->method('getStyleValues')
            ->with($this->equalTo('success'))
            ->will($this->returnValue($style));
        
        $message = $this->_response->applyStyleToMessage('string message', 'success');
        $this->assertRegExp('/^\033\[1;37m\033\[42m\033\[1m\033\[4mstring message\033\[0m$/', $message);
    }

    public function testApplyStyleToMessageReturnGivenMessageWithAppliedStyleOptions()
    {
        $styleOptions = array(
                'colorName'      => 'white',
                'backgroundName' => 'green'
        );
        $style = $this->_mockStyleEntity('success', 'white', 'green', '1;37', '42');
        /* @var $styleMock PHPUnit_Framework_MockObject_MockObject */
        $styleMock = $this->_response->getStyle();
        $styleMock->expects($this->atLeastOnce())
            ->method('getStyleValues')
            ->with($this->equalTo($styleOptions))
            ->will($this->returnValue($style));

        $message = $this->_response->applyStyleToMessage('string message', $styleOptions);
        $this->assertRegExp('/^\033\[1;37m\033\[42mstring message\033\[0m$/', $message);
    }

    public function testApplyStylePlaceholdersToMessageReturnFalseIfMessageIsNotString()
    {
        $errorStyle = $this->_mockStyleEntity();
        /* @var $styleMock PHPUnit_Framework_MockObject_MockObject */
        $styleMock = $this->_response->getStyle();
        $styleMock->expects($this->atLeastOnce())
            ->method('getStyleValues')
            ->with($this->equalTo('error'))
            ->will($this->returnValue($errorStyle));

        $actual = $this->_response->applyStylePlaceholdersToMessage(null);
        $this->assertFalse($actual);
    }

    public function testApplyStylePlaceholdersReturnMessageWithAppliedStyle()
    {
        $tag = Terminalor_Response_Styles::STYLE_TAG;
        $style = $this->_mockStyleEntity('success', 'white', 'green', '1;37', '42');
        /* @var $styleMock PHPUnit_Framework_MockObject_MockObject */
        $styleMock = $this->_response->getStyle();
        $styleMock->expects($this->atLeastOnce())
            ->method('getStyleValues')
            ->with($this->equalTo('success'))
            ->will($this->returnValue($style));

        $message = $this->_response->applyStylePlaceholdersToMessage("<{$tag} class=\"success\">string</{$tag}> message");
        $this->assertRegExp('/^\033\[1;37m\033\[42mstring\033\[0m message$/', $message);
    }

    public function testCreateTableFromArrayReturnEmptyIfArrayIsEmpty()
    {
        $this->assertEmpty($this->_response
            ->createTableFromArray(array()));
    }

    public function testCreateTableFromArrayReturnString()
    {
        $this->assertEmpty($this->_response
            ->createTableFromArray(array()));
    }

    public function testCreateTableFromArrayConvertArrayKeysIntoTableHeader()
    {
        $array = array(
            array('id' => '1', 'name' => 'bernard', 'email' => 'bernard@runawaylover.info'),
            array('id' => '2', 'name' => 'bernard2', 'email' => 'bernard2@runawaylover.info'),
        );
        
        $table = $this->_response->createTableFromArray($array);

        $table = explode("\n", $table);
        $headers = trim($table[1], '|');

        $headers = explode(' | ', $headers);
        $expected = array_keys($array[0]);
        foreach ($headers as $i => $header) {
            $this->assertEquals($expected[$i], trim($header));
        }
    }

    public function testCreateTableFromArrayConvertArrayNumberKeysIntoTableHeader()
    {
        $array = array(
            array('1', 'bernard', 'bernard@runawaylover.info'),
            array('2', 'bernard2', 'bernard2@runawaylover.info'),
        );

        $table = $this->_response->createTableFromArray($array);

        $table   = explode("\n", $table);
        $headers = trim($table[1], '|');

        $headers = explode(' | ', $headers);
        $expected = array_keys($array[0]);
        foreach ($headers as $i => $header) {
            $this->assertEquals($expected[$i], trim($header));
        }
    }

    public function testCreateTableFromArrayApplyClosureToEachTableCell()
    {
        $array = array(
            array('1', 'bernard', 'bernard@runawaylover.info')
        );

        $f = function($value){
            return ucfirst($value);
        };

        $table = $this->_response->createTableFromArray($array, $f);

        $table = array_slice(explode("\n", $table), 3, 1);
        foreach ($table as $row) {
            $row = trim($row, '|');
            $cells = explode(' | ', $row);
            foreach($cells as $i => $cell) {
                $this->assertEquals($f($array[0][$i]), trim($cell));
            }
        }
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testCreateTableFromArrayThrowsExceptionIfArgumentIsNotArray()
    {
        $this->_response->createTableFromArray(null);
    }
    
    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testDisplayHelpThrowsExceptionIfArgumentIsNotArray()
    {
        $this->_response->displayHelp(null);
    }

    public function testDisplayHelpShowHelpForGivenCommands()
    {
        $errorStyle  = $this->_mockStyleEntity();

        /* @var $styleMock PHPUnit_Framework_MockObject_MockObject */
        $styleMock = $this->_response->getStyle();
        $styleMock->expects($this->any())
            ->method('getStyleValues')
            ->will($this->returnValue($errorStyle));

        
        $docMock = $this->_response->getDocParser();
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

        $commands = array(
            'one' =>
            /**
             * Command one
             */
            function() {},
            'two' =>
            /**
             * Command two
             */
            function() {}
        );
        $this->_response->displayHelp($commands);
        $filename = $this->_response->getOutputStream();
        $message = file_get_contents($filename);
        $this->assertEquals('command help', $message);
    }

    private function _mockStyleEntity($id = 'error', $colorName = 'white', $backgroundName = 'red' ,
        $colorValue = '1;37', $backgroundValue = '41', $bold = null, $underline = null)
    {
        $entityMock = $this->_response->getStyle()->getEntity();
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

        $errorStyle->expects($this->any())
            ->method('hasBold')
            ->will($this->returnValue($bold));

        $errorStyle->expects($this->any())
            ->method('hasUnderline')
            ->will($this->returnValue($underline));

        return $errorStyle;
    }

    public function  tearDown()
    {
        $this->_response = null;
    }
}
