<?php
class Terminalor_DocParserTest extends PHPUnit_Framework_TestCase
{
    public function docCommentProvider()
    {
        return array(array(
           '/**
             * Sends email with given attributes using gmail
             * 
             * @author Bernard Baltrusaitis
             * @version 1.0
             * @param string $title email subject
             * @param string $to email to
             * @param string $body email body
             */'));
    }
    
    public function testGetTemplateReturnInstanceOfTemplateInterface()
    {
        $template = $this->getMock('Terminalor_DocParser_Template_Standard');
        $parser = new Terminalor_DocParser($template);
        $this->assertInstanceOf('Terminalor_DocParser_Template_Interface', $parser->getTemplate());
    }

    public function testGetParsedDocCommentReturnEmptyArrayOnInit()
    {
        $template = $this->getMock('Terminalor_DocParser_Template_Standard');
        $parser = new Terminalor_DocParser($template);
        $this->assertEmpty($parser->getParsedDocComment());
    }

    public function testGetRawDocCommentReturnNullOnInit()
    {
        $template = $this->getMock('Terminalor_DocParser_Template_Standard');
        $parser = new Terminalor_DocParser($template);
        $this->assertNull($parser->getRawDocComment());
    }

    public function testGetCommandNameReturnNullOnInit()
    {
        $template = $this->getMock('Terminalor_DocParser_Template_Standard');
        $parser = new Terminalor_DocParser($template);
        $this->assertNull($parser->getCommandName());
    }

    public function testGetParsedDocTagReturnNullIfTagIsNotFoundInParsedContent()
    {
        $template = $this->getMock('Terminalor_DocParser_Template_Standard');
        $parser = new Terminalor_DocParser($template);
        $this->assertNull($parser->getParsedDocTag('unknownTagName'));
    }

    public function testGetDocTagsAliasesReturnArrayWithOneElementOnInit()
    {
        $template = $this->getMock('Terminalor_DocParser_Template_Standard');
        $parser = new Terminalor_DocParser($template);
        $this->assertThat($parser->getDocTagsAliases(), $this->isType('array'));
        $this->assertCount(1, $parser->getDocTagsAliases());
    }

    public function testGetDocTagsAliasesReturnParamAliasByDefault()
    {
        $template = $this->getMock('Terminalor_DocParser_Template_Standard');
        $parser = new Terminalor_DocParser($template);
        $actual = $parser->getDocTagsAliases();
        $this->arrayHasKey('param', $actual);
    }

    public function testGetDocTagAliasReturnArgumentsValueForParamTag()
    {
        $template = $this->getMock('Terminalor_DocParser_Template_Standard');
        $parser = new Terminalor_DocParser($template);
        $actual = $parser->getDocTagAlias('param');
        $this->assertEquals('arguments', $actual);
    }

    public function testGetDocTagAliasReturnDefaultIfTagNotExists()
    {
        $template = $this->getMock('Terminalor_DocParser_Template_Standard');
        $parser = new Terminalor_DocParser($template);
        $actual = $parser->getDocTagAlias('tagNotExists', true);
        $this->assertEquals('tagNotExists', $actual);
    }

    public function testGetDocTagAliasReturnNullIfTagNotExistsAndReturnOriginalIsFalse()
    {
        $template = $this->getMock('Terminalor_DocParser_Template_Standard');
        $parser = new Terminalor_DocParser($template);
        $actual = $parser->getDocTagAlias('tagNotExists', false);
        $this->assertNull($actual);
    }

    /**
     * @dataProvider docCommentProvider
     */
    public function testSetRawDocCommentReturnSelf($docComment)
    {
        $template = $this->getMock('Terminalor_DocParser_Template_Standard');
        $parser = new Terminalor_DocParser($template);
        $actual = $parser->setRawDocComment($docComment);
        $this->assertInstanceOf('Terminalor_DocParser_Interface', $actual);
    }

    /**
     * @dataProvider docCommentProvider
     */
    public function testSetRawDocCommentAbleToSetDocComment($docComment)
    {
        $template = $this->getMock('Terminalor_DocParser_Template_Standard');
        $parser = new Terminalor_DocParser($template);
        $actual = $parser
            ->setRawDocComment($docComment)
            ->getRawDocComment();

        $this->assertEquals($docComment, $actual);
    }

    public function testSetCommandNameReturnSelf()
    {
        $template = $this->getMock('Terminalor_DocParser_Template_Standard');
        $parser = new Terminalor_DocParser($template);
        $actual = $parser->setCommandName('commandname');
        $this->assertInstanceOf('Terminalor_DocParser_Interface', $actual);
    }

    public function testSetCommandNameAbleToSetCommandName()
    {
        $template = $this->getMock('Terminalor_DocParser_Template_Standard');
        $parser = new Terminalor_DocParser($template);
        $actual = $parser->setCommandName('commandname')
            ->getCommandName();

        $this->assertEquals('commandname', $actual);
    }
    
    public function testSetDocTagAliasReturnSelf()
    {
        $template = $this->getMock('Terminalor_DocParser_Template_Standard');
        $parser = new Terminalor_DocParser($template);
        $actual = $parser->setDocTagAlias('example', 'usage');
        $this->assertInstanceOf('Terminalor_DocParser_Interface', $actual);
    }
    
    public function testSetDocTagAliasAbleToSetTagAlias()
    {
        $template = $this->getMock('Terminalor_DocParser_Template_Standard');
        $parser = new Terminalor_DocParser($template);
        $actual = $parser->setDocTagAlias('example', 'usage')
            ->getDocTagAlias('example');

        $this->assertEquals('usage', $actual);
    }
    
    public function testParseRawDocCommentReturnSelf()
    {
        $template = $this->getMock('Terminalor_DocParser_Template_Standard');
        $parser = new Terminalor_DocParser($template);
        $actual = $parser->parseRawDocComment();
        $this->assertInstanceOf('Terminalor_DocParser_Interface', $actual);
    }

    /**
     * @dataProvider docCommentProvider
     */
    public function testParseRawDocCommentConvertStringIntoArray($docComment)
    {
        $template = $this->getMock('Terminalor_DocParser_Template_Standard');
        $parser = new Terminalor_DocParser($template);
        $parser->setRawDocComment($docComment);
        $actual = $parser->parseRawDocComment()
            ->getParsedDocComment();
        $this->assertNotEmpty($actual);
    }
    
    /**
     * @dataProvider docCommentProvider
     */
    public function testParseRawDocCommentConvertTagsIntoParsedDocArrayKeys($docComment)
    {
        $template = $this->getMock('Terminalor_DocParser_Template_Standard');
        $parser   = new Terminalor_DocParser($template);
        $parser->setRawDocComment($docComment);
        $actual = $parser->parseRawDocComment()
            ->getParsedDocComment();

        $this->assertArrayHasKey('description', $actual);
        $this->assertArrayHasKey('arguments', $actual);
        $this->assertArrayHasKey('author', $actual);
        $this->assertArrayHasKey('version', $actual);
    }
    
    /**
     * @dataProvider docCommentProvider
     */
    public function testFlushMethodSetPropertiesToInitState($docComment)
    {
        $template = $this->getMock('Terminalor_DocParser_Template_Standard');
        $parser   = new Terminalor_DocParser($template);
        $parser->setRawDocComment($docComment);
        $parser->setCommandName('somecommand');
        $parser->parseRawDocComment();

        $parser->flush();

        $this->assertNull($parser->getRawDocComment());
        $this->assertNull($parser->getCommandName());
        $this->assertEmpty($parser->getParsedDocComment());
    }

    /**
     * @dataProvider docCommentProvider
     */
    public function testGetParsedDocTagReturnParsedTagValue($docComment)
    {
        $template = $this->getMock('Terminalor_DocParser_Template_Standard');
        $parser   = new Terminalor_DocParser($template);
        $parser->setRawDocComment($docComment);
        $parser->parseRawDocComment();
        $this->assertEquals('Bernard Baltrusaitis', $parser->getParsedDocTag('author'));
    }

    /**
     * @dataProvider docCommentProvider
     */
    public function testGetParsedDocTagReturnArrayIfTagHasMultipleValues($docComment)
    {
        $template = $this->getMock('Terminalor_DocParser_Template_Standard');
        $parser   = new Terminalor_DocParser($template);
        $parser->setRawDocComment($docComment);
        $parser->parseRawDocComment();
        $this->assertThat($parser->getParsedDocTag('arguments'), $this->isType('array'));
    }

    public function testBuildHelpMethodReturnResultOfTemplateBuildMethod()
    {
        $template = $this->getMock('Terminalor_DocParser_Template_Standard', array('build'));
        $template
            ->expects($this->any())
            ->method('build')
            ->will($this->returnValue('string from template'));

        $parser = new Terminalor_DocParser($template);
        $this->assertEquals('string from template', $parser->buildHelp());
    }

    public function test_setParsedDocTagReturnSelf()
    {
        $template = $this->getMock('Terminalor_DocParser_Template_Standard');
        $parser = new Terminalor_DocParser($template);
        $method = new ReflectionMethod($parser, '_setParsedDocTag');
        $method->setAccessible(true);
        $actual = $method->invoke($parser, 'version', '1.0');
        $this->assertInstanceOf('Terminalor_DocParser_Interface', $actual);
    }
    
    public function test_setParsedDocTagReplacesExistingSameTags()
    {
        $template = $this->getMock('Terminalor_DocParser_Template_Standard');
        $parser = new Terminalor_DocParser($template);
        $method = new ReflectionMethod($parser, '_setParsedDocTag');
        $method->setAccessible(true);
        $method->invoke($parser, 'version', '1.0');
        $method->invoke($parser, 'version', '2.0');
        $this->assertEquals('2.0', $parser->getParsedDocTag('version'));
    }
    
    public function test_addParsedDocTagReturnSelf()
    {
        $template = $this->getMock('Terminalor_DocParser_Template_Standard');
        $parser = new Terminalor_DocParser($template);
        $method = new ReflectionMethod($parser, '_addParsedDocTag');
        $method->setAccessible(true);
        $actual = $method->invoke($parser, 'version', '1.0');
        $this->assertInstanceOf('Terminalor_DocParser_Interface', $actual);
    }

    public function test_setParsedDocTagAppendSameTagsValues()
    {
        $template = $this->getMock('Terminalor_DocParser_Template_Standard');
        $parser = new Terminalor_DocParser($template);
        $method = new ReflectionMethod($parser, '_addParsedDocTag');
        $method->setAccessible(true);
        $method->invoke($parser, 'version', '1.0');
        $method->invoke($parser, 'version', '2.0');
        $this->assertThat($parser->getParsedDocTag('version'), $this->isType('array'));
        $this->assertContains('1.0', $parser->getParsedDocTag('version'));
        $this->assertContains('2.0', $parser->getParsedDocTag('version'));
    }
}