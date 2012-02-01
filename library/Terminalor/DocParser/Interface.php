<?php
/**
 * This file is part of Terminalor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Terminalor DocParser class responsible for parsing given php doc comments into
 * assoc array where doc tags becomes array keys.
 *
 * @package    Terminalor
 * @subpackage DocParser
 * @author     Bernard Baltrusaitis <bernard@runawaylover.info>
 * @link       http://terminalor.runawaylover.info
 */
interface Terminalor_DocParser_Interface
{
    /**
     * Get template object responsible for rendering parsed php doc tags
     *
     * @return Terminalor_DocParser_Template_Interface
     */
    public function getTemplate();

    /**
     * Get parsed doc comment
     *
     * @see parseRawDocComment() how to parse doc comment
     * @return array parsed doc comment as assoc array
     */
    public function getParsedDocComment();

    /**
     * Get un-parsed doc comment
     *
     * @return string un-parsed (raw) doc comment
     */
    public function getRawDocComment();

    /**
     * Get given command name
     *
     * @return string command name
     */
    public function getCommandName();

    /**
     * Get parsed tag value by tag name, returns null if tag with given name
     * is not found in getParsedDocComment().
     * <b>Tags are stores agains aliases</b>
     *
     * @see getDocTagsAliases() to get all available aliases
     * @see getParsedDocComment() to get parsed tags
     * @param string $tagName tag name
     * @return string|array|null tag value
     */
    public function getParsedDocTag($tagName);

    /**
     * Get available tags aliases
     *
     * @return array array of tags aliases
     */
    public function getDocTagsAliases();

    /**
     * Get tag alias (if exists) if $returnOriginal is true and tag alias is not found
     * method will return value $tagName
     *
     * @param string $tagName tag name
     * @param boolean $returnOriginal return $tagName on failure
     * @return string|null tag alias
     */
    public function getDocTagAlias($tagName, $returnOriginal = true);

    /**
     * Set un-parsed (raw) doc comment
     *
     * @param string $rawDocComment un-parsed doc comment
     * @return Terminalor_DocParser_Interface
     */
    public function setRawDocComment($rawDocComment);

    /**
     * Specify command name
     *
     * @param string $commandName command name
     * @return Terminalor_DocParser_Interface
     */
    public function setCommandName($commandName);

    /**
     * Specify alias for given tag
     * <code>
     * $this->setDocTagAlias('param', 'arguments');
     * </code>
     *
     * @param string $tagName tag original name
     * @param string $tagAlias tag alias
     * @return Terminalor_DocParser_Interface
     */
    public function setDocTagAlias($tagName, $tagAlias);

    /**
     * Parse un-parsed doc comment into assoc array and pass this array to the template
     *
     * @see setRawDocComment() how to specify doc comment for this method
     * @see getParsedDocComment how to get result of this method
     * @return Terminalor_DocParser_Interface
     */
    public function parseRawDocComment();

    /**
     * Build commands help using specified doc comments
     *
     * @return string commands help with style placeholders
     */
    public function buildHelp();

    /**
     * Set object properties to the initial state
     *
     * @return Terminalor_DocParser_Interface
     */
    public function flush();
}
