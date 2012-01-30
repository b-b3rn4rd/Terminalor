<?php
/*
 * This file is part of Terminalor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Terminalor DocParser Template Abstract class responsible
 * for rendering all tags without induvidual renderers.
 *
 * @author      Bernard Baltrusaitis <bernard@runawaylover.info>
 * @package     Terminalor
 * @subpackage  DocParser
 * @link        http://terminalor.runawaylover.info
 */
abstract class Terminalor_DocParser_Template_Abstract
    implements Terminalor_DocParser_Template_Interface
{
    /**
     * Template variables storage
     *
     * @var array 
     */
    protected $_variables = array();

    /**
     * Induvidual tags renderers storage
     * 
     * @var array 
     */
    protected $_renderers = array();

    public function  __construct()
    {
        $this->_installRenderers();
    }

    /**
     * Check if given tag has a renderer
     * 
     * @param string $tagName tag name
     * @return boolean true if tag has renderer
     */
    public function tagHasRenderer($tagName)
    {
        return array_key_exists($tagName, $this->getRenderers());
    }

    /**
     * Get all registred renderers
     * 
     * @return array array of tag renderers
     */
    public function getRenderers()
    {
        return $this->_renderers;
    }

    /**
     * Get variables available to the template
     * 
     * @return array array of template vars 
     */
    public function getVars()
    {
        return $this->_variables;
    }
    
    /**
     * Set variables available for given command
     * <code>
     * $this->setVar('index', 'version', 'Beta 1.0');
     * </code>
     *
     * @param string $commandName command name
     * @param string $tagName tag name
     * @param string|array $tagValue tag value
     * @return Terminalor_DocParser_Template_Interface
     */
    public function setVar($commandName, $tagName, $tagValue)
    {
        $this->_variables[$commandName][$tagName] = $tagValue;
        return $this;
    }
    
    /**
     * Wrapper for setVar() method, allow to specify array of variables
     * for given command
     * 
     * @see setVar how to set induvidual command related tag
     * @param string $commandName command name
     * @param array $variables array of variables
     * @return Terminalor_DocParser_Template_Interface
     */
    public function setVars($commandName, $variables)
    {
        foreach ($variables as $tagName => $tagValue) {
            $this->setVar($commandName, $tagName, $tagValue);
        }
        return $this;
    }

    /**
     * Render given tag for specified command if tag has an induvidual renderer
     * it will be parsed using it, otherwise default renderer _renderUnspecifiedTag() will be used
     *
     * @see _renderUnspecifiedTag() how to render tag without spec. renderer
     * @param string $commandName command name
     * @param string $tagName tag name
     * @param string|array $tagValues tag value
     * @param array $tags array of all available tags for given command
     * @return string result of tag renderer
     */
    public function renderTag($commandName, $tagName, $tagValues, $tags)
    {
        if ($this->tagHasRenderer($tagName)) {
            $renderers = $this->getRenderers();
            return call_user_func(array($this, $renderers[$tagName]),
                $commandName, $tags, $tagName);
        } else {
            return $this->_renderUnspecifiedTag($tagName, $tagValues, $tags);
        }
    }

    /**
     * Build commands help for each command using given variables, each tag
     * is parsed separetly using specified or default parser
     *
     * @see getRenderers() to get available renderers
     * @see _renderUnspecifiedTag() for tags withour renderers
     * @return string renderer help menu with style placeholders
     */
    public function build()
    {
        $return = '';
        
        foreach ($this->getVars() as $commandName => $tags) {
            foreach ($tags as $tagName => $tagValues) {
                $return .= $this->renderTag($commandName, $tagName, $tagValues, $tags);
            }
        }

        return $return;
    }

    /**
     * Install default tags renderers, to become a renderer method
     * should have name is such format: /^_render(:tagName\w+)Tag$/
     * where :tagName is specified tag name to render if tag has an alias
     * alias should be specified as tag name @see _renderArgumentsTag which uses
     * arguments as an alias for param tag
     *
     * @see _renderDescriptionTag() pre-defined description renderer
     * @see _renderArgumentsTag pre-defined arguments renderer using alias
     */
    private function _installRenderers()
    {
        $class = get_called_class();
        $reflection = new ReflectionClass($class);
        /* @var $method ReflectionMethod */
        foreach ($reflection->getMethods() as $method) {
            if (preg_match('/^_render(\w+?)Tag$/', $method->getName(), $matches)) {
                $tagName = strtolower($matches[1]);
                $this->_renderers[$tagName] = $method->getName();
            }
        }
    }
    
    /**
     * Find longest element in given array
     *
     * @param array $array array of strings
     * @return int longest element size
     */
    protected function _findLongestElementInArray(array $array)
    {
        $size = 0;

        foreach ($array as $value) {
            if (!is_scalar($value)) {
                continue;
            }
            
            if (strlen((string)$value) > $size) {
                $size = strlen($value);
            }
        }

        return $size;
    }

    /**
     * Render tag using default renderer, this method is used for all tags
     * withoud specified renderer
     * 
     * @param string $tagName tag name
     * @param string|array $tagValues tag value
     * @param array $tags array of availabe tags
     * @return string renderer tag
     */
    protected function _renderUnspecifiedTag($tagName, $tagValues, $tags)
    {
        $return  = '';
        $padding = $this->_findLongestElementInArray(array_keys($tags));
        $tagName = str_pad($tagName, $padding);

        foreach ((array)$tagValues as $tagValue) {
            $return  .= sprintf('  %s : %s', ucfirst($tagName),
                $tagValue) . "\n";
        }

        return $return;
    }
}
