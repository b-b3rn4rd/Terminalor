<?php
class Terminalor_DocParser_Template_StandardTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var Terminalor_DocParser_Template_Standard
     */
    private $_template;

    public function setUp()
    {
        $this->_template = new Terminalor_DocParser_Template_Standard();
    }

    public function test_installRenderersMethosIsCalledInConstructor()
    {
        $mock = $this->getMock('Terminalor_DocParser_Template_Standard',
            array('_installRenderers'), array(), '', false);
        $mock->expects($this->never())
            ->method('_installRenderers')
            ->will($this->returnValue(null));
    }

    public function testGetRenderersReturnEmptyBeforeInstallRenderersMethod()
    {
        $mock = $this->getMock('Terminalor_DocParser_Template_Standard',
            array(), array(), '', false);
        $this->assertEmpty($mock->getRenderers());
    }
    
    public function test_installRenderersMethodInstallRendrersAfterInit()
    {
       $actual = $this->_template->getRenderers();
       $this->assertCount(3, $actual);
    }

    public function testGetVarsReturnEmptyArrayOnInit()
    {
        $this->assertEmpty($this->_template->getVars());
    }

    public function testSetVarSetsVariableForCommand()
    {
        $this->_template->setVar('command', 'author', 'bernard');
        $vars = $this->_template->getVars();

        $this->assertArrayHasKey('command', $vars);
        $this->assertArrayHasKey('author', $vars['command']);
    }

    public function testSetVarsDontReplaceOtherCommandVars()
    {
        $this->_template->setVar('command', 'author', 'bernard');
        $this->_template->setVars('command', array('copyright' => 'bernardltd'));

        $vars = $this->_template->getVars();
        $this->assertArrayHasKey('author', $vars['command']);
    }

    public function testSetVarsReplaceExistingTags()
    {
        $this->_template->setVar('command', 'author', 'bernard');
        $this->_template->setVars('command', array('author' => 'bernardltd'));

        $vars = $this->_template->getVars();
        $this->assertEquals($vars['command']['author'], 'bernardltd');
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testFindLongestElementInArrayThrowsExceptionIfNotArrayIsGiven()
    {
        $method = new ReflectionMethod('Terminalor_DocParser_Template_Standard', '_findLongestElementInArray');
        $method->setAccessible(true);
        $actual = $method->invoke($this->_template, 'notAnArray');
    }

    public function testFindLongestElementInArrayReturnZeroIfArrayIsEmpty()
    {
        $method = new ReflectionMethod('Terminalor_DocParser_Template_Standard', '_findLongestElementInArray');
        $method->setAccessible(true);
        $actual = $method->invoke($this->_template, array());
        $this->assertEquals(0, $actual);
    }

    public function testFindLongestElementInArrayCalculateLengthJustForScalar()
    {
        $method = new ReflectionMethod('Terminalor_DocParser_Template_Standard', '_findLongestElementInArray');
        $method->setAccessible(true);
        $actual = $method->invoke($this->_template, array('abc', array('hello world')));
        $this->assertEquals(3, $actual);
    }

    public function testFindLongestElementInArrayReturnLongestElementSizeItItsIsSecond()
    {
        $method = new ReflectionMethod('Terminalor_DocParser_Template_Standard', '_findLongestElementInArray');
        $method->setAccessible(true);
        $actual = $method->invoke($this->_template, array('1','333','22',));
        $this->assertEquals(3, $actual);
    }

    public function testFindLongestElementInArrayReturnLongestElementSizeItItsIsLast()
    {
        $method = new ReflectionMethod('Terminalor_DocParser_Template_Standard', '_findLongestElementInArray');
        $method->setAccessible(true);
        $actual = $method->invoke($this->_template, array('1','22','333'));
        $this->assertEquals(3, $actual);
    }

    public function testFindLongestElementInArrayReturnLongestElementSizeItItsIsFirst()
    {
        $method = new ReflectionMethod('Terminalor_DocParser_Template_Standard', '_findLongestElementInArray');
        $method->setAccessible(true);
        $actual = $method->invoke($this->_template, array('333', '1', '22'));
        $this->assertEquals(3, $actual);
    }
    
    public function test_renderDescriptionTagMethodExists()
    {
        $renderers = $this->_template->getRenderers();
        $this->assertContains('_renderDescriptionTag', $renderers);
    }
    
    public function test_renderDescriptionTagMethodReturnEmptyIfNoDescriptionFound()
    {
        $method = new ReflectionMethod('Terminalor_DocParser_Template_Standard', '_renderDescriptionTag');
        $method->setAccessible(true);
        $actual = $method->invoke($this->_template, 'somecommandname', array(), 'description');
        $this->assertEmpty($actual);
    }

    public function test_renderDescriptionTagDontReturnCommandNameCommandsCountIsOne()
    {
        $this->_template->setVars('somecommandname', array());
        $method = new ReflectionMethod('Terminalor_DocParser_Template_Standard', '_renderDescriptionTag');
        $method->setAccessible(true);
        $actual = $method->invoke($this->_template, 'somecommandname',
            array('description'=> 'some text'),
            'description');

        $this->assertNotTag(array(
            'tag'        => Terminalor_Response_Styles::STYLE_TAG,
            'attributes' => array('class' => 'command'),
            'content'    => 'somecommandname'
            ),
            $actual);
    }

    public function test_renderDescriptionTagReturnCommandNameBetweenTerminalorTagsWithCommandStyle()
    {
        $this->_template->setVars('somecommandname',
            array('description'=> 'some text'));
        $this->_template->setVars('othercommandname',
            array('description'=> 'other text'));

        $method = new ReflectionMethod('Terminalor_DocParser_Template_Standard', '_renderDescriptionTag');
        $method->setAccessible(true);
        $actual = $method->invoke($this->_template, 'somecommandname',
            array('description'=> 'some text'), 'description');
            
        $this->assertTag(array(
            'tag'        => Terminalor_Response_Styles::STYLE_TAG,
            'attributes' => array('class' => 'command'),
            'content'    => 'somecommandname'
            ),
            $actual);
    }

    public function test_renderDescriptionTagDescriptionReturnDescriptionBetweenTerminalorTagsWithInfoStyle()
    {
        $this->_template->setVars('somecommandname',
            array('description'=> 'some text'));
        
        $method = new ReflectionMethod('Terminalor_DocParser_Template_Standard', '_renderDescriptionTag');
        $method->setAccessible(true);
        $actual = $method->invoke($this->_template, 'somecommandname',
            array('description'=> 'some text'), 'description');

        $this->assertTag(array(
            'tag'        => Terminalor_Response_Styles::STYLE_TAG,
            'attributes' => array('class' => 'info'),
            'content'    => 'some text'
            ),
            $actual);
    }

    public function test_renderArgumentsTagReturnEmptyIfNoArgumentsKeyFoundInTagsParam()
    {
        $method = new ReflectionMethod('Terminalor_DocParser_Template_Standard', '_renderArgumentsTag');
        $method->setAccessible(true);
        $actual = $method->invoke($this->_template, 'somecommandname', array(), 'arguments');
        $this->assertEmpty($actual);
    }

    public function test_renderArgumentsTagReturnEachArgumentWithEqualLeftPaddingEqualsSizeArg()
    {
        $method = new ReflectionMethod('Terminalor_DocParser_Template_Standard', '_renderArgumentsTag');
        $method->setAccessible(true);
        $actual = $method->invoke($this->_template, 'somecommandname', array(
           'arguments' =>  array(
               'string $name your name',
               'string $age your age')), 'arguments');
        
        $params = explode("\n", $actual);
        $params = array_filter($params);
        
        foreach($params as $string) {
            $this->assertRegExp("/^  /", $string);
        }
    }
    
    public function test_renderArgumentsTagReturnFirstArgumentWithArgumentText()
    {
        $method = new ReflectionMethod('Terminalor_DocParser_Template_Standard', '_renderArgumentsTag');
        $method->setAccessible(true);
        $actual = $method->invoke($this->_template, 'somecommandname', array(
           'arguments' =>  array(
               'string $name your name',
               'string $age your age')), 'arguments');
               
        $params = explode("\n", $actual);
        $params = array_filter($params);
        $this->assertRegExp("/^  Arguments/i", reset($params));
        $this->assertRegExp("/^           /i", end($params));
    }

    public function test_renderUnspecifiedTagReturnAllTagsWithSameLengthEqualsSizeArg()
    {
        $method = new ReflectionMethod('Terminalor_DocParser_Template_Standard', '_renderUnspecifiedTag');
        $method->setAccessible(true);
        $actual = $method->invoke($this->_template, 'author', array(
            'bernard'
        ), array(
            'author' => array('bernard')
        ));
        
        $params = explode("\n", $actual);
        $params = array_filter($params);
        $prefixAndSuffixLength = 3;
        foreach ($params as $string) {
            $string = strtok($string, ':');
            $this->assertEquals(strlen('author'), strlen($string)-$prefixAndSuffixLength);
        }
    }

    public function testBuildMethodReturnEmptyStringIfSetVarsExecutedWithEmptyArray()
    {
        $this->_template->setVars('somecommandname', array());
        $actual = $this->_template->build();
        $this->assertEmpty($actual);
    }

    public function testBuildMethodReturnDecoratedStringWithStylePlaceholders()
    {
        $this->_template->setVars('index', array(
                'description' => 'index command description',
                'arguments'   => array('name' => 'string $name your name'),
                'author'      => 'Bernard Baltrusaitis'
            ));
        
        $this->_template->setVars('index2', array(
                'description' => 'index2 command description',
                'arguments'   => array('surname' => 'string $surname your surname'),
                'copyright'   => array('Bernard Pty Ltd')
            ));

        $actual = $this->_template->build();
        
        # index command name exists and wrapped in tag with command style
        $this->assertTag(array(
            'tag'        => Terminalor_Response_Styles::STYLE_TAG,
            'attributes' => array('class' => 'command'),
            'content'    => 'index'
        ), $actual);

        $this->assertTag(array(
            'tag'        => Terminalor_Response_Styles::STYLE_TAG,
            'attributes' => array('class' => 'command'),
            'content'    => 'index2'
        ), $actual);

        # command description wrapped in tag with info style
        $this->assertTag(array(
            'tag'        => Terminalor_Response_Styles::STYLE_TAG,
            'attributes' => array('class' => 'info'),
            'content'    => 'index command description'
            ),
            $actual);

        $this->assertTag(array(
            'tag'        => Terminalor_Response_Styles::STYLE_TAG,
            'attributes' => array('class' => 'info'),
            'content'    => 'index2 command description'
            ),
            $actual);

        # params
        $this->assertRegExp('/\-\-name\=<string>: your name/', $actual);
        $this->assertRegExp('/\-\-surname\=<string>: your surname/', $actual);
        $this->assertRegExp("/".str_pad('copyright', strlen('description'))." : Bernard Pty Ltd/i", $actual);
        $this->assertRegExp("/".str_pad('author', strlen('description'))." : Bernard Baltrusaitis/i", $actual);
    }
}
