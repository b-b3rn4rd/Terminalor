<?php
require_once 'vfsStream/vfsStream.php';
vfsStreamWrapper::register();

class Terminalor_BuilderTest extends PHPUnit_Framework_TestCase
{
    protected $backupGlobalsBlacklist = array('closure');
    private $_sourceFilename = null;
    private $_targetFilename = null;
    private $_root = null;
    /**
     *
     * @var Terminalor_Builder_Interface
     */
    private $_build = null;
    
    public function setUp()
    {
        $this->_root = vfsStream::newDirectory('build');
        vfsStreamWrapper::setRoot($this->_root);
        
        $this->_root->addChild(vfsStream::newFile('source'));
        $this->_root->addChild(vfsStream::newFile('target'));

        $this->_sourceFilename = vfsStream::url('build/source');
        $this->_targetFilename = vfsStream::url('build/target');

        $this->_build = new Terminalor_Builder($this->_sourceFilename, $this->_targetFilename);
    }

    public function simpleClassProvider()
    {
        $content =
        "<?php

            include 'classes/b.php';
            require_once 'classes/c.php';
            
            class A {
                public function sayHello()
                {
                    return 'hi';
                }
                
                public function sayBye(\$message = 'dosvidanie')
                {
                    include_once 'classes/'.\$message.'.php';
                }
            }
        ?>";
        
        return array(array($content));
    }

    public function commentedClassProvider()
    {
        $content =
        "<?php

            /* include 'classes/b.php'; */null;
            /* require_once 'classes/c.php'; */null;

            class A {
                public function sayHello()
                {
                    return 'hi';
                }

                public function sayBye(\$message = 'dosvidanie')
                {
                    include_once 'classes/'.\$message.'.php';
                }
            }
        ?>";

        return array(array($content));
    }

    public function testGetOpenPhpTagReturnOpenTagEndingWithSpace()
    {
        $this->assertEquals('<?php ', $this->_build->getOpenPhpTag());
    }

    public function testGetBootstrapStringReturnBootstrapStringEndingWithSemicolon()
    {
        $this->assertEquals('Terminalor_Container::getInstance()->setDefaultDependencies();',
            $this->_build->getBootstrapString());
    }

    public function testGetSourceFilenameReturnSourceFilename()
    {
        $filename = $this->_build->getSourceFilename();
        $this->assertEquals($this->_sourceFilename, $filename);
    }

    public function testGetTargetFilenameReturnTargetFilename()
    {
        $filename = $this->_build->getTargetFilename();
        $this->assertEquals($this->_targetFilename, $filename);
    }

    public function testIsMinimizeSourceReturnTrueByDefault()
    {
        $this->assertTrue($this->_build->isMinimizeSource());
    }

    public function testGetBuildHeaderReturnHeaderString()
    {
        $this->assertEquals("#!/usr/bin/php\n",
            $this->_build->getBuildHeader());
    }

    public function testGetBuildSourceIsEmptyOnInit()
    {
        $this->assertEmpty($this->_build->getBuildSource());
    }

    public function testGetExcludeClassesPatternsReturnTerminalorBuilderPatternByDefault()
    {
        $patterns = $this->_build->getExcludeClassesPatterns();
        $this->assertThat($patterns, $this->isType('array'));
        $this->assertCount(1, $patterns);
        $this->assertEquals('/^Terminalor_Builder/', $patterns[0]);
    }

    public function testGetIncludeClassesPatternsReturnZendAndTerminalorPatternsByDefault()
    {
        $patterns = $this->_build->getIncludeClassesPatterns();
        $this->assertThat($patterns, $this->isType('array'));
        $this->assertCount(2, $patterns);
        $this->assertContains('/^Zend_/', $patterns);
        $this->assertContains('/^Terminalor/', $patterns);
    }

    public function testSetBuildHeaderReturnSelf()
    {
        $actual = $this->_build->setBuildHeader('');
        $this->assertInstanceOf('Terminalor_Builder_Interface', $actual);
    }

    public function testSetBuildHeaderReplaceHeader()
    {
        $actual = $this->_build->setBuildHeader('new header')
            ->getBuildHeader();
        $this->assertEquals('new header', $actual);
    }
    
    public function testSetTargetFilenameReturnSelf()
    {
        $actual = $this->_build->setTargetFilename('');
        $this->assertInstanceOf('Terminalor_Builder_Interface', $actual);
    }

    public function testSetTargetFilenameChangeTargetFilename()
    {
        $actual = $this->_build->setTargetFilename('newfilename')->getTargetFilename();
        $this->assertEquals('newfilename', $actual);
    }
    
    public function testPrependBuildSourceReturnSelf()
    {
        $actual = $this->_build->prependBuildSource('');
        $this->assertInstanceOf('Terminalor_Builder_Interface', $actual);
    }

    public function testPrependBuildSourcePrependBuildSource()
    {
        $this->_build->setBuildSource('world');
        $this->_build->prependBuildSource('hello ');
        $this->assertEquals('hello world', $this->_build->getBuildSource());
    }

    public function testAppendBuildSourceReturnSelf()
    {
        $actual = $this->_build->appendBuildSource('');
        $this->assertInstanceOf('Terminalor_Builder_Interface', $actual);
    }

    public function testAppendBuildSourceAppendBuildSource()
    {
        $this->_build->setBuildSource('hello');
        $this->_build->appendBuildSource(' world');
        $this->assertEquals('hello world', $this->_build->getBuildSource());
    }

    public function testSetBuildSourceReturnSelf()
    {
        $actual = $this->_build->setBuildSource('');
        $this->assertInstanceOf('Terminalor_Builder_Interface', $actual);
    }

    public function testSetBuildSourceReplaceBuildSource()
    {
        $this->_build->setBuildSource('new content');
        $actual = $this->_build->setBuildSource('old content')->getBuildSource();
        $this->assertEquals('old content', $actual);
    }

    public function testAddIncludeClassPatternReturnSelf()
    {
        $actual = $this->_build->addIncludeClassPattern('');
        $this->assertInstanceOf('Terminalor_Builder_Interface', $actual);
    }

    public function testAddIncludeClassPatternAppendIncludeClassPattern()
    {
        $actual = $this->_build->addIncludeClassPattern('newpattern')
            ->getIncludeClassesPatterns();
        $this->assertContains('newpattern', $actual);
    }

    public function testAddExcludeClassPatternReturnSelf()
    {
        $actual = $this->_build->addExcludeClassPattern('');
        $this->assertInstanceOf('Terminalor_Builder_Interface', $actual);
    }

    public function testAddExcludeClassPatternAppendExcludeClassPattern()
    {
        $actual = $this->_build->addExcludeClassPattern('newpattern')
            ->getExcludeClassesPatterns();
        $this->assertContains('newpattern', $actual);
    }

    public function testClassIsAllowedReturnFalseIfClassNameIsExcluded()
    {
        $this->_build->addExcludeClassPattern('/^My_/');
        $this->assertFalse($this->_build->classIsAllowed('My_Class'));
    }
    
    public function testClassIsAllowedReturnFalseIfClassNameIsExcludedAndIncluded()
    {
        $this->_build->addExcludeClassPattern('/^My_File_/');
        $this->_build->addIncludeClassPattern('/^My_/');
        $this->assertFalse($this->_build->classIsAllowed('My_File_One'));
    }

    public function testClassIsAllowedReturnFalseIfClassNameIsNotIncludeNorExcluded()
    {
        $this->assertFalse($this->_build->classIsAllowed('Some_Class'));
    }

    public function testClassIsAllowedReturnTrueIfClassNameIsIncluded()
    {
        $this->_build->addIncludeClassPattern('/^My_/');
        $this->assertTrue($this->_build->classIsAllowed('My_File_One'));
    }

    public function testStripPhpTagsReturnStringWithRemovedOpenAndCloseTags()
    {
        $actual = $this->_build->stripPhpTags('<?php "hello world"; ?>');
        $this->assertEquals(' "hello world"; ', $actual);
    }

    /**
     * @dataProvider simpleClassProvider
     */
    public function testGetFileSourceReturnMinimizedSourceIfGetMinimizeSourceIsTrue($content)
    {
        $file = 'includedfile';
        $filename = vfsStream::url("build/{$file}");
        $this->_root->addChild(vfsStream::newFile($file));
        file_put_contents($filename, $content);
        $actual = $this->_build->getFileSource($filename);
        $expected = php_strip_whitespace($filename);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider simpleClassProvider
     */
    public function testGetFileSourceReturnUnMinimizedSourceIfMinimizeSourceIsFalse($content)
    {
        $file = 'includedfile';
        $filename = vfsStream::url("build/{$file}");
        $this->_root->addChild(vfsStream::newFile($file));
        file_put_contents($filename, $content);
        $actual = $this->_build->getFileSource($filename, false);
        $expected = file_get_contents($filename);
        $this->assertEquals($expected, $actual);
    }

    /**
     * @dataProvider simpleClassProvider
     */
    public function testGetSourceIncludesReturnArrayOfFoundIncludeLinks($content)
    {
        $file = 'includedfile';
        $filename = vfsStream::url("build/{$file}");
        $this->_root->addChild(vfsStream::newFile($file));
        file_put_contents($filename, $content);
        $includes = $this->_build->getSourceIncludes(file_get_contents($filename));
        $this->assertCount(3, array_filter($includes));
    }
    
    /**
     * @dataProvider simpleClassProvider
     */
    public function testGetSourceIncludesCommentOutStatisIncludesAndLeaveDynamic($content)
    {
        $file = 'includedfile';
        $filename = vfsStream::url("build/{$file}");
        $this->_root->addChild(vfsStream::newFile($file));
        file_put_contents($filename, $content);
        $source = file_get_contents($filename);
        $this->_build->getSourceIncludes($source, true);
        file_put_contents($filename, $source);
        $includes = $this->_build->getSourceIncludes(file_get_contents($filename));
        $this->assertCount(1, $includes);
    }

    public function testIncludeFileForBuildAppendFileSourceToBuild()
    {
        $file = 'includedfile';
        $filename = vfsStream::url("build/{$file}");
        $this->_root->addChild(vfsStream::newFile($file));
        file_put_contents($filename, '<?php /* hello*/?>world');
        $this->_build->setBuildSource('hello ');
        $this->_build->includeFileForBuild($filename);
        $actual = $this->_build->getBuildSource();
        $this->assertEquals('hello world', $actual);
    }

    public function testincludeDirectoryForBuildIncludeGivenDirectoryFilesIntoBuild()
    {
        $dirname = 'library';
        $path = vfsStream::url("build/{$dirname}/");
        $directory = vfsStream::newDirectory($dirname);
        $this->_root->addChild($directory);

        $file = 'a.php';
        $filename = vfsStream::url("build/library/{$file}/");
        $directory->addChild(vfsStream::newFile($file));
        file_put_contents($filename, 'hello world');
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        
        $this->_build->includeDirectoryForBuild($iterator);
        $this->_build->saveBuildSource();
        $actual = file_get_contents($this->_build->getTargetFilename());
        $this->assertEquals('hello world', $actual);
    }

    public function testSaveBuildSourceSavesBuildInTargetFile()
    {
        $this->_build->setBuildSource('hello world');
        $this->_build->saveBuildSource();
        $actual = file_get_contents($this->_build->getTargetFilename());
        $this->assertEquals('hello world', $actual);
    }

    /**
     * @expectedException UnexpectedValueException
     */
    public function testSaveBuildThrowsExceptionIfTargetFileIsNotWriteable()
    {
        $file = 'includedfile';
        $filename = vfsStream::url("build/{$file}");
        $this->_root->addChild(vfsStream::newFile($file, 0444));
        $this->_build->setTargetFilename($filename);
        $this->_build->saveBuildSource();
    }
    
    public function testSaveBuildCreateTargerFileIsNotSpecified()
    {
        $file = 'build.source';
        $filename = vfsStream::url("build/{$file}");
        $this->_root->addChild(vfsStream::newFile($file));
        
        $this->_build->setTargetFilename(null);
        $this->_build->saveBuildSource();
        $filename = basename($this->_build->getTargetFilename());
        $this->assertEquals($file, $filename);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testBuildMethodThrowsExceptionIfArgumentIsNotArray()
    {
        $this->_build->build('string');
    }

    /**
     * @runInSeparateProcess
     * @expectedException UnexpectedValueException
     */
    public function testBuildMethodThrowsUnexpectedValueExceptionIfSourceFilenameIsNotReadable()
    {
        $build = new Terminalor_Builder(null);
        $build->build();
    }

    public function testAssembleClosureParamsMethodAssingValuesToArgs()
    {
        $method = new ReflectionMethod($this->_build, '_assembleClosureParams');
        $method->setAccessible(true);
        $args = $method->invoke($this->_build, function($title, $name){}, array(
            'title' => 'hello world',
            'name'  => 'bernard'
        ));
        
        $this->assertContains('hello world', $args);
        $this->assertContains('bernard', $args);
    }
    
    public function testAssembleClosureParamsMethodAssingDefaultValues()
    {
        $method = new ReflectionMethod($this->_build, '_assembleClosureParams');
        $method->setAccessible(true);
        $args = $method->invoke($this->_build, function($title, $name = 'bernard'){}, array(
            'title' => 'hello world'
        ));

        $this->assertContains('hello world', $args);
        $this->assertContains('bernard', $args);
    }

    public function testAssembleClosureParamsMethodAssingNullToUnspecifiedArgs()
    {
        $method = new ReflectionMethod($this->_build, '_assembleClosureParams');
        $method->setAccessible(true);
        $args = $method->invoke($this->_build, function($title, $name){}, array(
            'title' => 'hello world'
        ));

        $this->assertContains('hello world', $args);
        $this->assertContains(null, $args);
    }

    /**
     * @runInSeparateProcess
     */
    public function testBuildMethodBuildSourceFile()
    {
        $content =
        "<?php
            Terminalor_Application::autoloadRegister();
            Terminalor_Container::getInstance()
                ->setDefaultDependencies();

            \$terminalor = Terminalor_Container::getInstance()
                ->get('Terminalor.Application');

            \$terminalor['index'] =
            /**
             * Command says hello
             */
             function(Terminalor_Application_Interface \$terminalor, \$name) {
                //\$terminalor->getResponse()->message(\"Hello {\$name}\");
             } ?>";
        
        file_put_contents($this->_build->getSourceFilename(), $content);
        $this->_build->build(array(
            'index' => array('name' => 'john doh')
        ));
        
        $content = file_get_contents($this->_build->getTargetFilename());
    }
}