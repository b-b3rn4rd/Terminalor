<?php
class Terminalor_Request_Router_StandardTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var Terminalor_Request_Router_Standard
     */
    private $_router = null;

    public function  setUp()
    {
        $this->_router = new Terminalor_Request_Router_Standard();
    }

    public function testByDefaultCommandNameIsPassedReturnFalse()
    {
        $this->assertFalse($this->_router->commandNameIsPassed());
    }

    public function testByDefaultGetDefaultCommandNameReturnIndex()
    {
        $this->assertEquals('index', $this->_router->getDefaultCommandName());
    }

    /**
     * @expectedException BadMethodCallException
     */
    public function testSetDefaultCommandNameThrowsExceptionIfCommandNameisNotStringOrNull()
    {
        $this->_router->setDefaultCommandName(2);
    }

    public function testSetDefaultCommandNameChangeDefaultCommandName()
    {
        $this->_router->setDefaultCommandName('notIndex');
        $this->assertEquals('notIndex', $this->_router->getDefaultCommandName());
    }

    public function testSetDefaultCommandNameReturnSelf()
    {
        $return = $this->_router->setDefaultCommandName('notIndex');
        $this->assertInstanceOf('Terminalor_Request_Router_Standard', $return);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testParseMethodArgumensParamShouldBeArray()
    {
        $this->_router->parse('notAnArray');
    }

    public function testParseMethodArgumentsParamPresendInReturn()
    {
        $params = $this->_router->parse(array(
            'name' => 'bernard'
        ));

        $this->assertArrayHasKey('name', $params);
        $this->assertContains('bernard', $params);
    }

    public function testParseMethodArgumentsParamReplacedByARGV()
    {
        $_SERVER['argv'] = array(
            'somefilename.php',
            '--name=notbernard'
        );
        
        $params = $this->_router->parse(array(
            'name' => 'bernard'
        ));

        $this->assertEquals('notbernard', $params['name']);
    }

    public function testParseMethodExpectFirstWordToBeCommandName()
    {
        $_SERVER['argv'] = array(
            'somefilename.php',
            'mycommandname',
            '--name=notbernard'
        );

        $params = $this->_router->parse();
        $this->assertEquals('mycommandname', $params['command']);
    }

    public function testParseMethodExpectExactlyTwoMinisesForArgument()
    {
        $_SERVER['argv'] = array(
            'somefilename.php',
            'mycommandname',
            '--name=bernard',
            '-aname=bernard',
            '---bname=bernard'
        );

        $params = $this->_router->parse();

        $this->assertArrayHasKey('name', $params);
        $this->assertArrayNotHasKey('aname', $params);
        $this->assertArrayNotHasKey('bname', $params);
    }

    public function testCommandNameIsPassedTrueIfCommandIsPassed()
    {
        $_SERVER['argv'] = array(
            'somefilename.php',
            'mycommandname'
        );

        $params = $this->_router->parse();
        $this->assertTrue($this->_router->commandNameIsPassed());
    }

    public function testParseMethodParsesArgumnetValueWithQuotes()
    {
        $_SERVER['argv'] = array(
            'somefilename.php',
            '--text="hello world"'
        );

        $params = $this->_router->parse();
        $this->assertEquals('hello world', $params['text']);
    }

    public function testParseMethodReturnDefaultCommandNameIfNameNotSpecified()
    {
        $_SERVER['argv'] = array();

        $params = $this->_router->parse();
        $this->assertEquals($this->_router->getDefaultCommandName(),
            $params['command']);
    }
 }
