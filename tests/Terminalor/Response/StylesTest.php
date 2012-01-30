<?php
class Terminalor_Response_StylesTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var Terminalor_Response_Styles_Entity
     */
    private $_style = null;

    private function _getEntityMockSetup()
    {
        $entityMock = $this->getMock('Terminalor_Response_Styles_Entity',
            array('flush','setProperties', 'getId', 'getColorName', 'getBackgroundName'));
        
        $entityMock->expects($this->any())
            ->method('flush')
            ->will($this->returnSelf());

        $entityMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue('error'));

        $entityMock->expects($this->any())
            ->method('getColorName')
            ->will($this->returnValue('white'));

        $entityMock->expects($this->any())
            ->method('getBackgroundName')
            ->will($this->returnValue('red'));
        
        $entityMock->expects($this->any())
            ->method('setProperties')
            ->with($this->isType('array'))
            ->will($this->returnSelf());
        
        return $entityMock;
    }
    
    public function setUp()
    {
        $this->_style = new Terminalor_Response_Styles($this->_getEntityMockSetup());
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function testAddStyleMethodExceptsInstanceOfEntityInterface()
    {
        $this->_style->addStyle(null);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddStyleThrowExceptionIfEntityWithoutId()
    {
        $this->_style->addStyle($this->getMock('Terminalor_Response_Styles_Entity'));
    }

    public function testAddStyleInstallCanNewStyle()
    {
        $newStyle = $this->getMock('Terminalor_Response_Styles_Entity');
        $newStyle->expects($this->any())
            ->method('getId')
            ->will($this->returnValue('newStyleName'));

        $this->_style->addStyle($newStyle);

        $this->assertInstanceOf('Terminalor_Response_Styles_Entity',
            $this->_style->getStyleByName('newStyleName'));
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testAddStyleThrowExceptionIfStyleNotExists()
    {
        $this->_style->getStyleByName('styleNotExists');
    }

    public function testGetColorsMethodReturnInstalledColors()
    {
        $colors = $this->_style->getColors();
        $this->assertArrayHasKey('red', $colors);
        $this->assertArrayHasKey('black', $colors);
        $this->assertArrayHasKey('green', $colors);
        $this->assertArrayHasKey('purple', $colors);
        $this->assertArrayHasKey('yellow', $colors);
        $this->assertArrayHasKey('blue', $colors);
        $this->assertArrayHasKey('white', $colors);
    }

    public function testGetBackgroundsMethodReturnInstalledBackgrounds()
    {
        $colors = $this->_style->getBackgrounds();
        $this->assertArrayHasKey('red', $colors);
        $this->assertArrayHasKey('black', $colors);
        $this->assertArrayHasKey('green', $colors);
        $this->assertArrayHasKey('purple', $colors);
        $this->assertArrayHasKey('yellow', $colors);
        $this->assertArrayHasKey('blue', $colors);
        $this->assertArrayHasKey('white', $colors);
    }

    public function testInstallColorCanInstallColor()
    {
        $this->_style->installColor('newColorName', '0;0');
        $this->assertEquals('0;0',
            $this->_style->getColorByName('newColorName'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testInstallColorShouldThrowExceptionIfValueIsInvalid()
    {
        $this->_style->installColor('newColorName', 'invalidValue');
    }

    public function testInstallBackgroundCanInstallBackground()
    {
        $this->_style->installBackground('NewBackgroundName', '10');
        $this->assertEquals('10',
            $this->_style->getBackgroundByName('NewBackgroundName'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetColorByNameThrowsExceptionIfColorNotExists()
    {
        $this->_style->getColorByName('colorNotExists');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testGetBackgroundByNameThrowsExceptionIfBackgroundNotExists()
    {
        $this->_style->getBackgroundByName('backgroundNotExists');
    }
    
    public function testRemoveStyleRemovesStyle()
    {
        $this->_style->removeStyle('error');
        $styles = $this->_style->getStyles();
        $this->assertArrayNotHasKey('error', $styles);
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testRemoveStyleThrowsExceptionIfStyleNotExists()
    {
        $this->_style->removeStyle('styleNotExists');
    }

    public function testGetDefaultStyleNameByDefaultReturnNormalString()
    {
        $this->assertEquals('normal',
            $this->_style->getDefaultStyleName());
    }

    public function testSetDefaultStyleCanSetDefaultStyle()
    {
        $this->_style->setDefaultStyle('error');
        $this->assertEquals('error',
            $this->_style->getDefaultStyleName());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetDefaultThrowsExceptionIfStyleNotExists()
    {
        $this->_style->setDefaultStyle('styleNotExists');
    }

    public function testSetDefaultColorChangesDefaultColors()
    {
        $this->_style->setDefaultStyle('error');
        $this->_style->setDefaultColor('green');
        $this->assertEquals('green', $this->_style->getDefaultColor());
    }
    
    public function testSetDefaultBackgroundChangesDefaultBackground()
    {
        $this->_style->setDefaultStyle('error');
        $this->_style->setDefaultBackground('green');
        $this->assertEquals('green', $this->_style->getDefaultBackground());
    }

    public function testColorExistsReturnTrueIfColorFound()
    {
        $this->assertTrue($this->_style->colorExists('red'));
    }

    public function testColorExistsReturnFaseIfColorNotFound()
    {
        $this->assertFalse($this->_style->colorExists('unknownColorName'));
    }

    public function testBackgroundExistsReturnTrueIfBackgroundFound()
    {
        $this->assertTrue($this->_style->backgroundExists('red'));
    }
    
    public function testBackgroundExistsReturnFalseIfBackgroundNotFound()
    {
        $this->assertFalse($this->_style->backgroundExists('unknownBackgroundName'));
    }

    public function testStyleExistsReturnTrueIfStyleFound()
    {
        $this->assertTrue($this->_style->styleExists('error'));
    }

    public function testStyleExistsReturnFalseIfStyleNotFound()
    {
        $this->assertFalse($this->_style->styleExists('unknownStyleName'));
    }

    public function testGetStyleValuesReturnEntityInterface()
    {
        $actual = $this->_style->getStyleValues('error');
        $this->assertInstanceOf('Terminalor_Response_Styles_Entity_Interface', $actual);
    }
    
    public function testGetStyleValuesFillEntityStyleWithColorAndBackgroundValues()
    {
        $style = $this->_style->getStyleByName('error');
        $this->assertFalse($style->hasValues());
        $style = $this->_style->getStyleValues('error');
        $this->assertTrue($style->hasValues());
    }

    public function testGetStyleValuesReturnValuesForDefaultStyleIfNullIsGiven()
    {
        $this->_style->setDefaultStyle('error');
        $style = $this->_style->getStyleValues(null);
        $this->assertEquals('error', $style->getId());
    }

    public function testGetStyleValuesReturnAssembledStyleNameIfStyleOptionsGiven()
    {
        $entity = $this->getMock('Terminalor_Response_Styles_Entity', array(
            'getId', 'getColorName', 'getBackgroundName', 'flush', 'setProperties'
        ));

        $entity->expects($this->any())
            ->method('flush')
            ->will($this->returnSelf());
        
        $entity->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(Terminalor_Response_Styles::LAST_STYLE_NAME));
        
        $style = new Terminalor_Response_Styles($entity);
        $newStyle = $style->getStyleValues(
            array('colorName' => 'red', 'backgroundName' => 'red'));
        $this->assertEquals(Terminalor_Response_Styles::LAST_STYLE_NAME, $newStyle->getId());

    }

    public function testGetStyleValuesFillValidValuesForColorAndBackground()
    {
        $style = $this->_style->getStyleValues('error');
        $this->assertEquals($this->_style->getColorByName('white'),
            $style->getColorValue());

        $this->assertEquals($this->_style->getBackgroundByName('red'),
            $style->getBackgroundValue());
    }

    public function testGetStyleValuesMethodFillNullColorWithDefaultColor()
    {
        $newStyle = $this->getMock('Terminalor_Response_Styles_Entity', array(
            'getId'
        ));

        $this->_style->setDefaultColor('green');
        
        $newStyle->expects($this->any())
            ->method('getId')
            ->will($this->returnValue('newStyleName'));
            
        $this->_style->addStyle($newStyle);
        $newStyle = $this->_style->getStyleValues('newStyleName');
        $this->assertEquals($this->_style->getColorByName($this->_style->getDefaultColor()) ,
            $newStyle->getColorValue());
    }

    public function testGetStyleValuesMethodFillNullBackgroundWithDefaultBackground()
    {
        $newStyle = $this->getMock('Terminalor_Response_Styles_Entity', array(
            'getId'
        ));

        $this->_style->setDefaultBackground('yellow');

        $newStyle->expects($this->any())
            ->method('getId')
            ->will($this->returnValue('newStyleName'));

        $this->_style->addStyle($newStyle);
        $newStyle = $this->_style->getStyleValues('newStyleName');
        $this->assertEquals($this->_style->getBackgroundByName($this->_style->getDefaultBackground()) ,
            $newStyle->getBackgroundValue());
    }

    public function testAssebleStyleFromOptionsReturnAssembledStyleName()
    {
        $method = new ReflectionMethod('Terminalor_Response_Styles', '_assebleStyleFromOptions');
        $method->setAccessible(true);
        $styleName = $method->invokeArgs($this->_style, array(array(
            'colorName' => 'red',
            'backgroundName' => 'blue',
            'bold' => true
        )));

        $this->assertEquals(Terminalor_Response_Styles::LAST_STYLE_NAME, $styleName);
        
    }
}
