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
class Terminalor_Response_Styles implements Terminalor_Response_Styles_Interface
{
    /**
     * Last used style name
     */
    const LAST_STYLE_NAME = 0001;
    /**
     * Style tag used as style placeholder
     */
    const STYLE_TAG = 'terminalor';
    /**
     * Storage of defined styles
     *
     * @var array 
     */
    private $_styles = array();
    /**
     * Storage of defined colors
     * 
     * @var array
     */
    private $_colors = array();
    /**
     * Storage of defined backgrounds
     * 
     * @var array
     */
    private $_backgrounds = array();
    /**
     * Style entity
     * 
     * @var Terminalor_Response_Styles_Entity_Interface
     */
    private $_entity = null;
    /**
     * Default style name
     *
     * @var string
     */
    private $_defaultStyleName = 'normal';
    /**
     * Default color name
     * @var string 
     */
    private $_defaultColorName = null;
    /**
     * Default style name
     * @var string 
     */
    private $_defaultBackgroundName = null;
    
    public function  __construct(Terminalor_Response_Styles_Entity_Interface $entity)
    {
        $this->_entity = $entity;
        $this->_installDefaults();
    }
    
    
    /**
     * Get array of defined colors
     * 
     * @return array defined colors 
     */
    public function getColors()
    {
        return $this->_colors;
    }

    /**
     * Get array of defined backgrounds
     * 
     * @return array defined backgrounds 
     */
    public function getBackgrounds()
    {
        return $this->_backgrounds;
    }

    /**
     * Get style entity object
     * 
     * @return Terminalor_Response_Styles_Entity_Interface
     */
    public function getEntity()
    {
        return $this->_entity;
    }

    /**
     * Get array od defined styles
     * 
     * @return array defined styles
     */
    public function getStyles()
    {
        return $this->_styles;
    }
    
    /**
     * Get style object by name
     * 
     * @param string $styleName
     * @throws InvalidArgumentException if style with given name doesnt exists
     * @return Terminalor_Response_Styles_Entity_Interface
     */
    public function getStyleByName($styleName)
    {
        if ($this->styleExists($styleName)) {
            $style = $this->_styles[$styleName];
            return $style;
        } else {
            throw new InvalidArgumentException(
                sprintf('Style `%s` doesn\'t exists', $styleName));
        }
    }

    /**
     * Install or replace color, $colorValue mask is \d;\d
     * <code>
     * $this->installColor('red', '0;31');
     * </code>
     * 
     * @throws InvalidArgumentException if $colorValue has invalid value
     * @param string $colorName new color name
     * @param string $colorValue new color value
     * @return Terminalor_Response_Styles_Interface
     */
    public function installColor($colorName, $colorValue)
    {
        if (!preg_match('/^[\d]+\;[\d]+$/', $colorValue)) {
            throw new InvalidArgumentException(
                sprintf('Color `%s` has invalid value %s', $colorName, $colorValue));
        }
        
        $this->_colors[$colorName] = $colorValue;
        return $this;
    }

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
    public function installBackground($backgroundName, $backgroundValue)
    {
        $this->_backgrounds[$backgroundName] = $backgroundValue;
        return $this;
    }

    /**
     * Get defined color value by name
     * 
     * @throws InvalidArgumentException if color with given name doesnt exists
     * @param string $colorName
     * @return string color value
     */
    public function getColorByName($colorName)
    {
        if ($this->colorExists($colorName)) {
            return $this->_colors[$colorName];
        } else {
            throw new InvalidArgumentException(
                sprintf('Color name `%s` doesn\'t exists', $colorName));
        }
    }

    /**
     * Get defined background by name
     * 
     * @throws InvalidArgumentException if given background doesnt exists
     * @param string $backgroundName
     * @return string background value
     */
    public function getBackgroundByName($backgroundName)
    {
        if ($this->backgroundExists($backgroundName)) {
            return $this->_backgrounds[$backgroundName];
        } else {
            throw new InvalidArgumentException(
                sprintf('Background name `%s` doesn\'t exists', $backgroundName));
        }
    }

    /**
     * Install or replace style
     * 
     * @throws InvalidArgumentException if given style id eq. null
     * @param Terminalor_Response_Styles_Entity_Interface $style new style
     * @return Terminalor_Response_Styles_Interface
     */
    public function addStyle(Terminalor_Response_Styles_Entity_Interface $style)
    {
        if (is_null($style->getId())) {
            throw new InvalidArgumentException(
                sprintf('Can\'t install `%s` style with null id', json_encode($style)));
        }
        $this->_styles[$style->getId()] = $style;
        return $this;
    }

    /**
     * Remove defined style by name
     * 
     * @throws InvalidArgumentException if given style doesnt exists
     * @param string $styleName
     * @return Terminalor_Response_Styles_Interface
     */
    public function removeStyle($styleName)
    {
        if ($this->styleExists($styleName)) {
            unset($this->_styles[$styleName]);
        } else {
            throw new InvalidArgumentException(
                sprintf('Can\'t delete style, `%s` doesn\'t exists', $styleName));
        }

        return $this;
    }

    /**
     * Get default style name
     * 
     * @return string style name 
     */
    public function getDefaultStyleName()
    {
        return $this->_defaultStyleName;
    }


    /**
     * Get default color name
     *
     * @return string color name
     */
    public function getDefaultColor()
    {
        return $this->_defaultColorName;
    }

    /**
     * Get default background name
     *
     * @return string background name
     */
    public function getDefaultBackground()
    {
        return $this->_defaultBackgroundName;
    }

        /**
     * Get style object with values by given name
     *
     * @param string $styleName style name
     * @return Terminalor_Response_Styles_Entity_Interface style object
     */
    public function getStyleValues($styleName)
    {
        if (is_null($styleName)) {
            $styleName = $this->getDefaultStyleName();
        }

        if (is_array($styleName)) {
            $styleName = $this->_assebleStyleFromOptions($styleName);
        }

        $style = $this->getStyleByName($styleName);

        if (!$style->getColorName()) {
            $style->setColorName($this->getDefaultColor());
        }

        if ($style->getColorName()) {
            $style->setColorValue($this->
                getColorByName($style->getColorName()));
        }

        if (!$style->getBackgroundName()) {
            $style->setBackgroundName($this->getDefaultBackground());
        }

        if ($style->getBackgroundName()) {
            $style->setBackgroundValue($this->
                getBackgroundByName($style->getBackgroundName()));
        }

        return $style;
    }

    /**
     * Set default style name
     * 
     * @throws InvalidArgumentException if given style name doesnt exists
     * @param string $styleName style
     * @return Terminalor_Response_Styles_Interface
     */
    public function setDefaultStyle($styleName)
    {
        if (!array_key_exists($styleName, $this->_styles)) {
            throw new InvalidArgumentException(
                sprintf('Can\'t set default style `%s` is not installed', $styleName));
        }

        $this->_defaultStyleName = $styleName;
        return $this;
    }

    /**
     * Set default color
     * 
     * @param string $colorName color name
     * @return Terminalor_Response_Styles_Interface
     */
    public function setDefaultColor($colorName)
    {
        $this->_defaultColorName = $colorName;
        return $this;
    }
    
    /**
     * Set default background name
     * 
     * @param string $backgroundName background name
     * @return Terminalor_Response_Styles_Interface
     */
    public function setDefaultBackground($backgroundName)
    {
        $this->_defaultBackgroundName = $backgroundName;
        return $this;
    }

    /**
     * Check if color with given name is defined
     * 
     * @param string $colorName color name
     * @return boolean 
     */
    public function colorExists($colorName)
    {
        return array_key_exists((string)$colorName, $this->_colors);
    }

    /**
     * Check if style with given name is defined
     * 
     * @param string $styleName style name
     * @return boolean 
     */
    public function styleExists($styleName)
    {
        return array_key_exists((string)$styleName, $this->_styles);
    }

    /**
     * Check if background with given name is defined
     * 
     * @param string $backgroundName
     * @return boolean 
     */
    public function backgroundExists($backgroundName)
    {
        return array_key_exists((string)$backgroundName, $this->_backgrounds);
    }

    /**
     * Asseble temporary style from custom options
     * <code>
     * $styleName = $this->_assebleStyleFromOptions('colorName' => 'red', 'bold' => true);
     * </code>
     *
     * @param array $styleOptions style options
     * @return string style name
     */
    private function _assebleStyleFromOptions(array $styleOptions)
    {
        $styleName = self::LAST_STYLE_NAME;
        $styleOptions['id'] = $styleName;
        $style = clone $this->_entity;
        $style->flush()->setProperties($styleOptions);
        $this->addStyle($style);

        return $styleName;
    }

    /**
     * Install default colors, styles
     *
     * @return null
     */
    protected function _installDefaults()
    {
        $defaultColors = array(
            'black'  => '0;30',
            'red'    => '0;31',
            'green'  => '0;32',
            'purple' => '0;35',
            'yellow' => '0;33',
            'blue'   => '0;34',
            'white'  => '0;37',
        );

        foreach ($defaultColors as $colorName => $colorValue) {
            strtok($colorValue, ';');
            $backgroundValue = (int)strtok(';')+10;

            $this->installColor($colorName, $colorValue);
            $this->installBackground($colorName, $backgroundValue);
        }

        $_defaultStyles = array(
            'error' => array(
                'colorName'      => 'white',
                'backgroundName' => 'red'
            ),
            'success' => array(
                'colorName'      => 'white',
                'backgroundName' => 'green'
            ),
            'notice' => array(
                'colorName'      => 'white',
                'backgroundName' => 'purple'
            ),
            'command' => array(
                'colorName'      => 'green',
                'bold'           => true,
                'underline'      => true
            ),
            'info' => array(
                'colorName'      => 'green',
                'bold'           => true
            ),

            $this->_defaultStyleName => array(
                'colorName'      => null,
                'backgroundName' => null
            )
        );

        foreach ($_defaultStyles as $styleName => $styleAttributes) {
            $styleAttributes['id'] = $styleName;
            $style = clone $this->_entity;
            $style->flush()->setProperties($styleAttributes);
            $this->addStyle($style);
        }
    }
}
