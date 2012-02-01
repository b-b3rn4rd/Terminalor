<?php
/**
 * This file is part of Terminalor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Terminator Response Styles Entity class stores style data and provides
 * gettors and settors for it's properties
 *
 * @package    Terminalor
 * @subpackage Response
 * @author     Bernard Baltrusaitis <bernard@runawaylover.info>
 * @link       http://terminalor.runawaylover.info
 */
class Terminalor_Response_Styles_Entity implements Terminalor_Response_Styles_Entity_Interface
{
    /**
     * Color id
     *
     * @var string 
     */
    private $_id = null;
    /**
     * Color name
     * 
     * @var string 
     */
    private $_colorName = null;
    /**
     * Color bash value
     * 
     * @var mixed 
     */
    private $_colorValue = null;
    /**
     * Background name
     * 
     * @var string 
     */
    private $_backgroundName = null;
    /**
     * Background color value
     * 
     * @var mixed 
     */
    private $_backgroundValue = null;
    /**
     * Shows text in bold
     * 
     * @var boolean
     */
    private $_bold = null;
    /**
     * Underline text
     * 
     * @var boolean 
     */
    private $_underline = null;

    public function  __construct(array $propeties = null)
    {
        if (is_array($propeties)) {
            $this->setProperties($propeties);
        }

    }

    /**
     * Set style id
     * 
     * @param string $id style id
     * @return Terminalor_Response_Styles_Entity_Interface
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    /**
     * Set style color name
     * 
     * @param string $colorName style color name
     * @return Terminalor_Response_Styles_Entity_Interface
     */
    public function setColorName($colorName)
    {
        $this->_colorName = $colorName;
        return $this;
    }

    /**
     * Set style color value
     * 
     * @param string $colorValue style color value
     * @return Terminalor_Response_Styles_Entity_Interface
     */
    public function setColorValue($colorValue)
    {
        $this->_colorValue = $colorValue;
        return $this;
    }

    /**
     * Set style background name
     * 
     * @param string $backgrondName style background name
     * @return Terminalor_Response_Styles_Entity_Interface
     */
    public function setBackgroundName($backgrondName)
    {
        $this->_backgroundName = $backgrondName;
        return $this;
    }

    /**
     * Set style background value
     * 
     * @param string $backgrondValue style background value
     * @return Terminalor_Response_Styles_Entity_Interface
     */
    public function setBackgroundValue($backgrondValue)
    {
        $this->_backgroundValue = $backgrondValue;
        return $this;
    }

    /**
     * Set style font bold
     * 
     * @param boolean $isBold true if bold
     * @return Terminalor_Response_Styles_Entity_Interface
     */
    public function setBold($isBold)
    {
        $this->_bold = (boolean)$isBold;
        return $this;
    }

    /**
     * Set style text underline
     * 
     * @param boolean $isUnderlined true if underline
     * @return Terminalor_Response_Styles_Entity_Interface
     */
    public function setUnderline($isUnderlined)
    {
        $this->_underline = (boolean)$isUnderlined;
        return $this;
    }

    /**
     * Get style id
     * 
     * @return string
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Get style color name
     * 
     * @return string 
     */
    public function getColorName()
    {
        return $this->_colorName;
    }

    /**
     * Get style color value
     * 
     * @return string
     */
    public function getColorValue()
    {
        return $this->_colorValue;
    }

    /**
     * Get style background name
     * 
     * @return string 
     */
    public function getBackgroundName()
    {
        return $this->_backgroundName;
    }

    /**
     * Get style background value
     * 
     * @return string
     */
    public function getBackgroundValue()
    {
        return $this->_backgroundValue;
    }

    /**
     * Check if style font is bold
     * 
     * @return boolean true if bold
     */
    public function hasBold()
    {
        return $this->_bold;
    }

    /**
     * Check if style text is underlined
     * 
     * @return boolean true if underlined
     */
    public function hasUnderline()
    {
        return $this->_underline;
    }

    /**
     * Check if style has color and background values
     * 
     * @return boolean true if style has values
     */
    public function hasValues()
    {
        return (!is_null($this->_backgroundValue)
            && !is_null($this->_colorValue));
    }
    
    /**
     * Set style properties
     * <code>
     * $this->setProperties(array(
     *  'id'             => 'error',
     *  'colorName'      => 'white',
     *  'backgroundName' => 'red',
     *  'bold'           => true
     * ));
     * </code>
     * 
     * @throws InvalidArgumentException if unknown property given
     * @param array $properties style properties
     * @return Terminalor_Response_Styles_Entity 
     */
    public function setProperties(array $properties)
    {
        foreach ($properties as $name => $value) {
            $property = sprintf('_%s', $name);
            if (property_exists($this, $property)) {
                $this->{$property} = $value;
            } else {
                throw new InvalidArgumentException(
                    sprintf('Unknown style attribute `%s`', $property));
            }
        }

        return $this;
    }

    /**
     * Empty style properties
     * 
     * @return Terminalor_Response_Styles_Entity 
     */
    public function flush()
    {
        foreach ($this as $property => $value) {
            $this->{$property} = null;
        }

        return $this;
    }
}
