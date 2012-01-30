<?php

class Terminalor_Response_Styles_EntityTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var Terminalor_Response_Styles_Entity
     */
    private $_entity;
    
    public function  setUp()
    {
        $this->_entity = new Terminalor_Response_Styles_Entity();
    }

    public function  tearDown()
    {
        $this->_entity = null;
    }

    public function testEntityPropertiesCanBePassedByConstructor()
    {
        $entity = new Terminalor_Response_Styles_Entity(array(
            'id'             => 'error',
            'colorName'      => 'red',
            'backgroundName' => 'white',
            'bold'           => true,
            'underline'      => true,
        ));

        $this->assertEquals('error', $entity->getId());
        $this->assertEquals('red', $entity->getColorName());
        $this->assertEquals('white', $entity->getBackgroundName());
        $this->assertTrue($entity->hasBold());
        $this->assertTrue($entity->hasUnderline());
        
    }

    public function testSettersReturnSelf()
    {
        $this->assertInstanceOf('Terminalor_Response_Styles_Entity',
            $this->_entity->setId('error'));

        $this->assertInstanceOf('Terminalor_Response_Styles_Entity',
            $this->_entity->setBackgroundName('white'));

        $this->assertInstanceOf('Terminalor_Response_Styles_Entity',
            $this->_entity->setColorName('red'));

        $this->assertInstanceOf('Terminalor_Response_Styles_Entity',
            $this->_entity->setBold(true));

        $this->assertInstanceOf('Terminalor_Response_Styles_Entity',
            $this->_entity->setUnderline(true));
    }
    
    public function testGettersReturnEqualDataThatWasSet()
    {
        $this->_entity->setId('error')
            ->setBackgroundName('white')
            ->setColorName('red')
            ->setBold(true)
            ->setUnderline(true);

        $this->assertEquals('error', $this->_entity->getId());
        $this->assertEquals('red', $this->_entity->getColorName());
        $this->assertEquals('white', $this->_entity->getBackgroundName());
        $this->assertTrue($this->_entity->hasBold());
        $this->assertTrue($this->_entity->hasUnderline());
    }

    public function testFlushMethodUnsetEntityValuesAndReturnSelf()
    {
        $this->_entity->setId('error')
            ->setBackgroundName('white')
            ->setColorName('red')
            ->setBold(true)
            ->setUnderline(true);

        $response = $this->_entity->flush();

        $this->assertNull($this->_entity->getId());
        $this->assertNull($this->_entity->getColorName());
        $this->assertNull($this->_entity->getBackgroundName());
        $this->assertNull($this->_entity->hasBold());
        $this->assertNull($this->_entity->hasUnderline());

        $this->assertInstanceOf('Terminalor_Response_Styles_Entity', $response);
    }

    public function testSetPropertiesMethodCanSetPassedPropertiesAndReturnSelf()
    {
        $response = $this->_entity->setProperties(array(
            'id'             => 'error',
            'colorName'      => 'red',
            'backgroundName' => 'white',
            'bold'           => true,
            'underline'      => true
        ));

        $this->assertEquals('error', $this->_entity->getId());
        $this->assertEquals('red', $this->_entity->getColorName());
        $this->assertEquals('white', $this->_entity->getBackgroundName());
        $this->assertTrue($this->_entity->hasBold());
        $this->assertTrue($this->_entity->hasUnderline());

        $this->assertInstanceOf('Terminalor_Response_Styles_Entity', $response);
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetPropertiesMethodThrowsExceptionIfUnkownPropertyGiven()
    {
        $response = $this->_entity->setProperties(array(
            'unknownProperty' => 'error'
        ));
    }
}
