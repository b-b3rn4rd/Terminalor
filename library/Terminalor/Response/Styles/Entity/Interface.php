<?php
/*
 * This file is part of Terminalor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Terminator Response Styles Entity class stores style data and provides
 * gettors and settors for it's properties
 *
 * @author      Bernard Baltrusaitis <bernard@runawaylover.info>
 * @package     Terminalor
 * @subpackage  Response
 * @link        http://terminalor.runawaylover.info
 */
interface Terminalor_Response_Styles_Entity_Interface
{
    /**
     * Set style id
     *
     * @param string $id style id
     * @return Terminalor_Response_Styles_Entity_Interface
     */
    public function setId($id);

    /**
     * Set style color name
     *
     * @param string $colorName style color name
     * @return Terminalor_Response_Styles_Entity_Interface
     */
    public function setColorName($colorName);

    /**
     * Set style color value
     *
     * @param string $colorValue style color value
     * @return Terminalor_Response_Styles_Entity_Interface
     */
    public function setColorValue($colorValue);

    /**
     * Set style background name
     *
     * @param string $backgrondName style background name
     * @return Terminalor_Response_Styles_Entity_Interface
     */
    public function setBackgroundName($backgrondName);

    /**
     * Set style background value
     *
     * @param string $backgrondValue style background value
     * @return Terminalor_Response_Styles_Entity_Interface
     */
    public function setBackgroundValue($backgrondValue);

    /**
     * Set style font bold
     *
     * @param boolean $isBold true if bold
     * @return Terminalor_Response_Styles_Entity_Interface
     */
    public function setBold($isBold);

    /**
     * Set style text underline
     *
     * @param boolean $isUnderlined true if underline
     * @return Terminalor_Response_Styles_Entity_Interface
     */
    public function setUnderline($isUnderlined);

    /**
     * Get style id
     *
     * @return string
     */
    public function getId();

    /**
     * Get style color name
     *
     * @return string
     */
    public function getColorName();

    /**
     * Get style color value
     *
     * @return string
     */
    public function getColorValue();

    /**
     * Get style background name
     *
     * @return string
     */
    public function getBackgroundName();

    /**
     * Get style background value
     *
     * @return string
     */
    public function getBackgroundValue();

    /**
     * Check if style font is bold
     *
     * @return boolean true if bold
     */
    public function hasBold();

    /**
     * Check if style text is underlined
     *
     * @return boolean true if underlined
     */
    public function hasUnderline();

    /**
     * Check if style has color and background values
     *
     * @return boolean true if style has values
     */
    public function hasValues();

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
     * @return Terminalor_Response_Styles_Entity_Interface
     */
    public function setProperties(array $properties);

    /**
     * Empty style properties
     *
     * @return Terminalor_Response_Styles_Entity_Interface
     */
    public function flush();
}
