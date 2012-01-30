<?php
/*
 * This file is part of Terminalor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Terminator Response Styles class responsible for managing colors and styles
 * which are used in decorating user messages
 *
 * @author      Bernard Baltrusaitis <bernard@runawaylover.info>
 * @package     Terminalor
 * @subpackage  Response
 * @link        http://terminalor.runawaylover.info
 */
interface Terminalor_Response_Styles_Interface
{
    /**
     * Get array of defined colors
     *
     * @return array defined colors
     */
    public function getColors();

    /**
     * Get array of defined backgrounds
     *
     * @return array defined backgrounds
     */
    public function getBackgrounds();

    /**
     * Get style entity object
     * 
     * @return Terminalor_Response_Styles_Entity_Interface
     */
    public function getEntity();

    /**
     * Get array of defined styles
     *
     * @return array defined styles
     */
    public function getStyles();

    /**
     * Get style object by name
     * 
     * @throws InvalidArgumentException if style with given name doesn't exists
     * @param string $styleName
     * @return Terminalor_Response_Styles_Entity_Interface
     */
    public function getStyleByName($styleName);

    /**
     * Install or replace color
     * <code>
     * $this->installColor('red', '0;31');
     * </code>
     * 
     * @throws InvalidArgumentException if $colorValue has invalid value
     * @param string $colorName new color name
     * @param string $colorValue new color value
     * @return Terminalor_Response_Styles_Interface
     */
    public function installColor($colorName, $colorValue);

    /**
     * Install or replace background color
     * <code>
     * $this->installBackground('red', '41');
     * </code>
     *
     * @param string $backgroundName new background name
     * @param string $backgroundValue new bacgroudn value
     * @return Terminalor_Response_Styles_Interface
     */
    public function installBackground($backgroundName, $backgroundValue);

    /**
     * Get defined color value by name
     * <b>This method returns color value</b>
     * <code>
     * echo $this->getColorByName('red');
     * // output 0;31
     * </code>
     *
     * @throws InvalidArgumentException if color with given name doesn't exists
     * @param string $colorName color name
     * @return string color value
     */
    public function getColorByName($colorName);

    /**
     * Get defined background by name
     * <b>This method returns background value</b>
     * 
     * @throws InvalidArgumentException if given background doesn't exists
     * @param string $backgroundName
     * @return string background value
     */
    public function getBackgroundByName($backgroundName);

    /**
     * Install or replace style
     * 
     * @throws InvalidArgumentException if given style id eq. null
     * @param Terminalor_Response_Styles_Entity_Interface $style new style
     * @return Terminalor_Response_Styles_Interface
     */
    public function addStyle(Terminalor_Response_Styles_Entity_Interface $style);

    /**
     * Remove defined style by name
     * 
     * @throws InvalidArgumentException if given style doesnt exists
     * @param string $styleName
     * @return Terminalor_Response_Styles_Interface
     */
    public function removeStyle($styleName);

    /**
     * Get default style name
     * 
     * @see setDefaultStyle how to specify default style
     * @return string style name
     */
    public function getDefaultStyleName();

    /**
     * Set default style name
     * 
     * @throws InvalidArgumentException if given style name doesnt exists
     * @param string $styleName style
     * @return Terminalor_Response_Styles_Interface
     */
    public function setDefaultStyle($styleName);

    /**
     * Set default color
     *
     * @param string $colorName color name
     * @return Terminalor_Response_Styles_Interface
     */
    public function setDefaultColor($colorName);

    /**
     * Set default background name
     *
     * @param string $backgroundName background name
     * @return Terminalor_Response_Styles_Interface
     */
    public function setDefaultBackground($backgroundName);

    /**
     * Get default color name
     * 
     * @see setDefaultColor() how to specify default color
     * @return string color name
     */
    public function getDefaultColor();

    /**
     * Get default background name
     *
     * @see setDefaultBackground() how to specify default background
     * @return string background name
     */
    public function getDefaultBackground();

    /**
     * Check if color with given name is defined
     *
     * @param string $colorName color name
     * @return boolean
     */
    public function colorExists($colorName);

    /**
     * Check if style with given name is defined
     *
     * @param string $styleName style name
     * @return boolean
     */
    public function styleExists($styleName);

    /**
     * Check if background with given name is defined
     *
     * @param string $backgroundName
     * @return boolean
     */
    public function backgroundExists($backgroundName);

    /**
     * Get style object with values by given name
     *
     * @param string $styleName style name
     * @return Terminalor_Response_Styles_Entity_Interface style object
     */
    public function getStyleValues($styleName);
}
