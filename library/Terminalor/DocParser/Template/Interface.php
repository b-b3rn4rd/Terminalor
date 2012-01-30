<?php
/*
 * This file is part of Terminalor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Terminalor DocParser Template Interface responsible
 * for rendering parsed php doc tags
 *
 * @author      Bernard Baltrusaitis <bernard@runawaylover.info>
 * @package     Terminalor
 * @subpackage  DocParser
 * @link        http://terminalor.runawaylover.info
 */
interface Terminalor_DocParser_Template_Interface
{
    /**
     * Check if given tag has a renderer
     *
     * @param string $tagName tag name
     * @return boolean true if tag has renderer
     */
    public function tagHasRenderer($tagName);

    /**
     * Get all registred renderers
     *
     * @return array array of tag renderers
     */
    public function getRenderers();

    /**
     * Get variables available to the template
     *
     * @return array array of template vars
     */
    public function getVars();

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
    public function setVar($commandName, $tagName, $tagValue);

    /**
     * Wrapper for setVar() method, allow to specify array of variables
     * for given command
     *
     * @see setVar how to set induvidual command related tag
     * @param string $commandName command name
     * @param array $variables array of variables
     * @return Terminalor_DocParser_Template_Interface
     */
    public function setVars($commandName, $variables);

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
    public function renderTag($commandName, $tagName, $tagValues, $tags);

    /**
     * Build commands help for each command using given variables, each tag
     * is parsed separetly using specified or default parser
     *
     * @see getRenderers() to get available renderers
     * @see _renderUnspecifiedTag() for tags withour renderers
     * @return string renderer help menu with style placeholders
     */
    public function build();
}
