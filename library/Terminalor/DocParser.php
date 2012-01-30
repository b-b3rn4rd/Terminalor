<?php
/*
 * This file is part of Terminalor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Terminalor DocParser class responsible for parsing given php doc comments into
 * assoc array where doc tags becomes array keys.
 *
 * @author      Bernard Baltrusaitis <bernard@runawaylover.info>
 * @package     Terminalor
 * @subpackage  DocParser
 * @link        http://terminalor.runawaylover.info
 */
class Terminalor_DocParser implements Terminalor_DocParser_Interface
{
    /**
     * 
     * @var Terminalor_DocParser_Template_Interface
     */
    private $_template = null;
    
    /**
     * Command name related to doc comment
     *
     * @var string 
     */
    private $_commandName = null;
    
    /**
     * Unparsed doc comment
     * 
     * @var string
     */
    private $_rawDocComment = null;
    
    /**
     * Parsed doc comment
     * @var array 
     */
    private $_parsedDocComment = array();
    
    /**
     * Storage of available tags aliases
     * @var array 
     */
    private $_docTagsAliases = array(
        'param' => 'arguments'
    );

    public function  __construct(Terminalor_DocParser_Template_Interface $template)
    {
        $this->_template = $template;
    }

    /**
     * Get current template
     * 
     * @return Terminalor_DocParser_Template_Interface
     */
    public function getTemplate()
    {
        return $this->_template;
    }

    /**
     * Get parsed doc comment
     * 
     * @see parseRawDocComment() how to parse doc comment
     * @return array parsed doc comment as assoc array
     */
    public function getParsedDocComment()
    {
        return $this->_parsedDocComment;
    }
    
    /**
     * Get unparsed doc comment
     * 
     * @return string unparsed (raw) doc comment
     */
    public function getRawDocComment()
    {
        return $this->_rawDocComment;
    }

    /**
     * Get given command name
     * 
     * @return string command name 
     */
    public function getCommandName()
    {
        return $this->_commandName;
    }

    
    /**
     * Get parsed tag value by tag name, returns null if tag with given name
     * is not found in getParsedDocComment().
     *
     * @see getParsedDocComment() to get parsed tags
     * @param string $tagName tag name
     * @return string|array|null tag value
     */
    public function getParsedDocTag($tagName)
    {
        $tags = $this->getParsedDocComment();
        if (array_key_exists($tagName, $tags)) {
            return $tags[$tagName];
        }

        return null;
    }

    /**
     * Get available tags aliases
     * 
     * @return array array of tags aliases
     */
    public function getDocTagsAliases()
    {
        return $this->_docTagsAliases;
    }

    /**
     * Get tag alias (if exists) if $returnOriginal is true and tag alias is not found
     * method will return value $tagName
     * 
     * @param string $tagName tag name
     * @param boolean $returnOriginal return $tagName on failure
     * @return string|null tag alias
     */
    public function getDocTagAlias($tagName, $returnOriginal = true)
    {
        $aliases = $this->getDocTagsAliases();
        
        if (array_key_exists($tagName, $aliases)) {
            return $aliases[$tagName];
        }

        if ($returnOriginal) {
            return $tagName;
        } else {
            return null;
        }
    }

    
    /**
     * Set unparsed (raw) doc comment
     * 
     * @param string $rawDocComment unparsed doc comment
     * @return Terminalor_DocParser_Interface
     */
    public function setRawDocComment($rawDocComment)
    {
        $this->_rawDocComment = $rawDocComment;
        return $this;
    }

    /**
     * Specify command name
     * 
     * @param string $commandName command name
     * @return Terminalor_DocParser 
     */
    public function setCommandName($commandName)
    {
        $this->_commandName = $commandName;
        return $this;
    }

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
    public function setDocTagAlias($tagName, $tagAlias)
    {
        $this->_docTagsAliases[$tagName] = strtolower($tagAlias);
        return $this;
    }
    
    /**
     * Parse unparsed raw doc comment into assoc array and pass this array to the template
     *
     * @see setRawDocComment() how to specify doc comment for this method
     * @see getParsedDocComment how to get result of this method
     * @return Terminalor_DocParser_Interface
     */
    public function parseRawDocComment()
    {
        // replace all * and / chars
        $comments = preg_replace('/([\*\/]+)/si', '', $this->getRawDocComment());
        // split string into rows
        $rows = preg_split('/\n/si', $comments);
        // remove empty rows, for ex: space between description and params
        $rows = array_filter($rows, function($row){
            return (trim($row) != '');
        });
        // separate doc tag and related text from row
        foreach ($rows as $row) {
            if (preg_match('/^\s*\@([\w]+)\s([^\n]*)/i', $row, $matches)) {
                list(, $tagName, $tagValue) = $matches;
                $this->_addParsedDocTag($tagName, $tagValue);
            } else {
                $this->_setParsedDocTag('description',
                    $this->getParsedDocTag('description') . ltrim($row));
            }
        }
        
        $this->_template->setVars($this->getCommandName(),
            $this->getParsedDocComment());
        
        return $this;
    }

    /**
     * Build commands help using specified doc comments
     * 
     * @return string commands help with style placeholders 
     */
    public function buildHelp()
    {
        return $this->_template->build();
    }

    /**
     * Set object properties to the initial state
     * 
     * @return Terminalor_DocParser_Interface
     */
    public function flush()
    {
        $this->_commandName      = null;
        $this->_parsedDocComment = array();
        $this->_rawDocComment    = null;

        return $this;
    }

    /**
     * Set parsed tag value, if tag with given name exists if will be overwritten
     * 
     * @param string $tagName tag name
     * @param string|array $tagValue tag value
     * @return Terminalor_DocParser_Interface
     */
    protected function _setParsedDocTag($tagName, $tagValue)
    {
        $tagName = $this->getDocTagAlias($tagName);
        $this->_parsedDocComment[$tagName] = $tagValue;
        return $this;
    }

    /**
     * Add parsed tag to the parsed doc comment if tag already exists
     * new value will be appended to the existing tag
     *
     * @param string $tagName tag name
     * @param mixed $tagValue tag value
     * @return Terminalor_DocParser_Interface
     */
    protected function _addParsedDocTag($tagName, $tagValue)
    {
        $parsedTags = $this->getParsedDocComment();
        $tagName    = $this->getDocTagAlias($tagName);
        
        if (array_key_exists($tagName, $parsedTags)) {
            $oldTagValue = (array)$parsedTags[$tagName];
            
            array_push($oldTagValue, $tagValue);
            $tagValue = $oldTagValue;
        }

        $this->_setParsedDocTag($tagName, $tagValue);

        return $this;
    }
}
