<?php
/**
 * This file is part of Terminalor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Terminator Builder class responsible for genereting single portable php cli
 * file by merging declared classes and interfaces into single file
 *
 * @package    Terminalor
 * @subpackage Builder
 * @author     Bernard Baltrusaitis <bernard@runawaylover.info>
 * @link       http://terminalor.runawaylover.info
 */
interface Terminalor_Builder_Interface
{
    /**
     * Get open php tag
     *
     * @return string open php tag
     */
    public function getOpenPhpTag();

    /**
     * Get bootstrap string
     *
     * @return string bootstrap
     */
    public function getBootstrapString();

    /**
     * Get build source filename
     *
     * @return string source filename
     */
    public function getSourceFilename();

    /**
     * Get build target filename
     *
     * @return string target filename
     */
    public function getTargetFilename();

    /**
     * Indicates if source needs to be minimized in build
     *
     * @return boolean true if minimize source
     */
    public function isMinimizeSource();

    /**
     * Get file header
     *
     * @return string file header
     */
    public  function getBuildHeader();

    /**
     * Get build source
     *
     * @return string build source
     */
    public function getBuildSource();

    /**
     * Get exclude classes patterns
     *
     * @return array array of patterns
     */
    public function getExcludeClassesPatterns();

    /**
     * Get included classes patterns
     *
     * @return array array of patterns
     */
    public function getIncludeClassesPatterns();

    
    /**
     * Find all includes in source, if $removeIncludes is true
     * then it also removes includes from given source
     *
     * @param string $source file source
     * @param boolean $removeIncludes
     * @return array array of includes found in source
     */
    public function getSourceIncludes(&$source, $removeIncludes = false);
    
    
    /**
     * Get given file source if $minimizeSource is true then
     * source will be minimized
     *
     * @param string $filename file location
     * @param boolean $minimizeSource minimize or not file source
     * @return string file source
     */
    public function getFileSource($filename, $minimizeSource = null);
    
    /**
     * Set file header
     *
     * @param string $header file header
     * @return Terminalor_Builder_Interface
     */
    public function setBuildHeader($header);

    /**
     * Specify target filename
     *
     * @param string $filename target filename
     * @return Terminalor_Builder_Interface
     */
    public function setTargetFilename($filename);
    
    /**
     * Prepend build source with $content
     *
     * @see setBuildSource() how to replace build source
     * @param string $content
     * @return Terminalor_Builder_Interface
     */
    public function prependBuildSource($content);

    /**
     * Append build source with $content
     *
     * @see setBuildSource() how to replace build source
     * @param string $content
     * @return Terminalor_Builder_Interface
     */
    public function appendBuildSource($content);

    /**
     * Set build source
     *
     * @param string $source build source
     * @return Terminalor_Builder_Interface
     */
    public function setBuildSource($source);

    /**
     * Add include class pattern
     * <code>
     * $this->addIncludeClassPattern('/^Zend_/');
     * </code>
     *
     * @param string $pattern regular expression
     * @return Terminalor_Builder_Interface
     */
    public function addIncludeClassPattern($pattern);

    /**
     * Add exclude class pattern
     * <code>
     * $this->addExcludeClassPattern('/^Terminalor_Builder_/');
     * </code>
     *
     * @param string $pattern regular expression
     * @return Terminalor_Builder_Interface
     */
    public function addExcludeClassPattern($pattern);

    /**
     * Check if given class name is allowed to be included in build
     *
     * @param string $className class name
     * @return boolean true if file is allowed
     */
    public function classIsAllowed($className);

    /**
     * Include file for build, file source open/close tags will be stripped
     * all includes commented out if $minimizeSource is true then file source
     * will be minimized
     * 
     * @see getBuildSource() to check if file source was included
     * @param string $filename
     * @param boolean $minimizeSource
     * @return null
     */
    public function includeFileForBuild($filename, $minimizeSource = null);

    /**
     * Include files in directory for build, this method is wrapper for
     * includeFileForBuild()
     *
     * @see includeFileForBuild() how file is included
     * @param RecursiveIteratorIterator $iterator
     * @param boolean $minimizeSource
     * @return null
     */
    public function includeDirectoryForBuild(RecursiveIteratorIterator $iterator,
        $minimizeSource = null);

    /**
     * Save build content
     *
     * @return null
     */
    public function saveBuildSource();

    /**
     * Strip open/close php tags from the source
     *
     * @param string $source file source
     * @return string source without php tags
     */
    public function stripPhpTags($source);
}