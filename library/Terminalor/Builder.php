<?php
/*
 * This file is part of Terminalor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Terminator Builder class responsible for genereting single portable php cli
 * file by merging declared classes and interfaces into single file
 *
 * @author      Bernard Baltrusaitis <bernard@runawaylover.info>
 * @package     Terminalor
 * @subpackage  Builder
 * @link        http://terminalor.runawaylover.info
 */
include dirname(__FILE__).'/Builder/Abstract.php';

class Terminalor_Builder extends Terminalor_Builder_Abstract
{
    /**
     * Builds protable php cli file, method will run all commands
     * with specified params in $commands, all files included in build will be
     * minimized if @see isMinimizeSource() is true, all static includes will be
     * commented out. If default params are not specified in $commands null value
     * will be sent. You can change behavior of your command using _TERMINALOR_BUILDER_
     * constant which is true using building process.
     *
     * @throws UnexpectedValueException if source is not readable
     * @param array $commands
     * @return null
     */
    public function build(array $commands = null)
    {
        define('_TERMINALOR_BUILDER_', true);

        if (!is_readable($this->getSourceFilename())) {
            throw new UnexpectedValueException(
                sprintf('Build source file `%s` doesn\'t exists', $this->getSourceFilename()));
        }
        
        include $this->getSourceFilename();
        
        /* @var $terminalor Terminalor_Application_Interface */
        $terminalor = Terminalor_Container::getInstance()
            ->get('Terminalor.Application');
        
        foreach ($terminalor as $name => $closure) {
            $closure = $terminalor[$name];
            $values  = (isset($commands[$name])
                ? $commands[$name] : array());

            $params  = $this->_assembleClosureParams($closure, (array)$values);
            call_user_func_array($closure, $params);
        }

        $classesToLoad = array();
        
        foreach (get_declared_classes() as $class) {
            $interfaces   = class_implements($class);
            $interfaces[] = $class;
            foreach ($interfaces as $interface) {
                if (!in_array($interface, $classesToLoad)) {
                    $classesToLoad[] = $interface;
                }
            }
        }

        foreach ($classesToLoad as $class) {
            /* @var $reflection ReflectionClass */
            $reflection = new ReflectionClass($class);

            if ($this->classIsAllowed($reflection->getName())) {
                $this->includeFileForBuild($reflection->getFileName());
            }
        }
        
        $this->appendBuildSource($this->getBootstrapString());
        $this->includeFileForBuild($this->getSourceFilename(), false);
        $this->prependBuildSource($this->getOpenPhpTag());
        $this->prependBuildSource($this->getBuildHeader());
        $this->saveBuildSource();
    }

    /**
     * Save build content
     *
     * @throws UnexpectedValueException if build target is not writable
     * @return null
     */
    public function saveBuildSource()
    {
        if (!is_null($this->getTargetFilename())) {
            if (!is_writable($this->getTargetFilename())) {
                throw new UnexpectedValueException(
                    sprintf('Build target file `%s` is not writable', $this->getTargetFilename()));
            }
        } else {
            $basename = basename($this->getSourceFilename());
            $filename = str_replace($basename,
                sprintf('build.%s', $basename), $this->getSourceFilename());

            $this->setTargetFilename($filename);
        }

        $f = @fopen($this->getTargetFilename(), 'w');
        fwrite($f, $this->getBuildSource());
        fclose($f);
    }

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
    public function includeFileForBuild($filename, $minimizeSource = null)
    {
        $source = $this->getFileSource($filename, $minimizeSource);
        $this->getSourceIncludes($source, true);
        $source = $this->stripPhpTags($source);
        $this->appendBuildSource(trim($source));
    }

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
        $minimizeSource = null)
    {
        /* @var $path DirectoryIterator*/
        foreach ($iterator as $path) {
            if ($path->isFile()) {
                $this->includeFileForBuild($path->getPathname(), $minimizeSource);
            }
        }
    }
}
