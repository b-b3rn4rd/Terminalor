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

include dirname(__FILE__).'/Interface.php';

abstract class Terminalor_Builder_Abstract implements Terminalor_Builder_Interface
{
    /**
     * Storage of exclude classes patterns
     * 
     * @var array 
     */
    private $_excludeClassesPatterns = array(
        '/^Terminalor_Builder/',
    );

    /**
     * Storage of include classes patterns
     * 
     * @var array 
     */
    private $_includeClassesPatterns = array(
        '/^Terminalor/',
        '/^Zend_/'
    );
    
    /**
     * Flag indicates to minimize build source
     * 
     * @var boolean 
     */
    protected $_minimizeSource = null;
    /**
     * File header
     * 
     * @var string 
     */
    protected $_buildHeader = "#!/usr/bin/php\n";
    /**
     * Build file source
     * 
     * @var string
     */
    protected $_buildSource = '';

    /**
     * File to build
     * 
     * @var string
     */
    protected $_sourceFilename = null;

    /**
     * Target for build file
     * 
     * @var string 
     */
    protected $_targetFilename = null;
    
    /**
     * Builds protable php cli file, method will run all given commands
     * from $commands with specified params
     *
     * @param array $commands
     * @return null
     */
    abstract function build(array $commands = null);

    /**
     * Build given file into single portable file
     * 
     * @param string $sourceFilename source filename
     * @param string $targetFilename build target filename
     * @param boolean $minimizeSource true to minimize source 
     */
    public function __construct($sourceFilename, $targetFilename = null, $minimizeSource = true)
    {
        $this->_sourceFilename = $sourceFilename;
        $this->_targetFilename = $targetFilename;
        $this->_minimizeSource = $minimizeSource;
    }

    /**
     * Get open php tag
     *
     * @return string open php tag
     */
    public function getOpenPhpTag()
    {
        return '<?php ';
    }

    /**
     * Get bootstrap string
     *
     * @return string bootstrap
     */
    public function getBootstrapString()
    {
        return 'Terminalor_Container::getInstance()->setDefaultDependencies();';
    }

    /**
     * Get build source filename
     * 
     * @return string source filename 
     */
    public function getSourceFilename()
    {
        return $this->_sourceFilename;
    }

    /**
     * Get build target filename
     * 
     * @return string target filename 
     */
    public function getTargetFilename()
    {
        return $this->_targetFilename;
    }

    /**
     * Indicates if source needs to be minimized in build
     *
     * @return boolean true if minimize source
     */
    public function isMinimizeSource()
    {
        return $this->_minimizeSource;
    }

    /**
     * Get file header
     *
     * @return string file header
     */
    public  function getBuildHeader()
    {
        return $this->_buildHeader;
    }

    /**
     * Get build source
     *
     * @return string build source
     */
    public function getBuildSource()
    {
        return $this->_buildSource;
    }

    /**
     * Get exclude classes patterns
     *
     * @return array array of patterns
     */
    public function getExcludeClassesPatterns()
    {
        return $this->_excludeClassesPatterns;
    }

    /**
     * Get included classes patterns
     *
     * @return array array of patterns
     */
    public function getIncludeClassesPatterns()
    {
        return $this->_includeClassesPatterns;
    }

    /**
     * Set file header
     * 
     * @param string $header file header
     * @return Terminalor_Builder_Interface
     */
    public function setBuildHeader($header)
    {
        $this->_buildHeader = $header;
        return $this;
    }

    /**
     * Specify target filename
     * 
     * @param string $filename target filename
     * @return Terminalor_Builder_Interface
     */
    public function setTargetFilename($filename)
    {
        $this->_targetFilename = $filename;
        return $this;
    }

    /**
     * Prepend build source with $content
     * 
     * @see setBuildSource() how to replace build source
     * @param string $content
     * @return Terminalor_Builder_Interface
     */
    public function prependBuildSource($content)
    {
        $source = $this->getBuildSource();
        $source = $content . $source;
        $this->setBuildSource($source);
        return $this;
    }

    /**
     * Append build source with $content
     * 
     * @see setBuildSource() how to replace build source
     * @param string $content
     * @return Terminalor_Builder_Interface
     */
    public function appendBuildSource($content)
    {
        $source = $this->getBuildSource();
        $source = $source . $content;
        $this->setBuildSource($source);
        return $this;
    }

    /**
     * Set build source
     *
     * @param string $source build source
     * @return Terminalor_Builder_Interface
     */
    public function setBuildSource($source)
    {
        $this->_buildSource = $source;
        return $this;
    }

    /**
     * Add include class pattern
     * <code>
     * $this->addIncludeClassPattern('/^Zend_/');
     * </code>
     * 
     * @param string $pattern regular expression
     * @return Terminalor_Builder_Interface
     */
    public function addIncludeClassPattern($pattern)
    {
        $this->_includeClassesPatterns[] = $pattern;
        return $this;
    }

    /**
     * Add exclude file mask
     * <code>
     * $this->addExcludeClassPattern('/^Terminalor_Builder_/');
     * </code>
     * 
     * @param string $pattern regular expression
     * @return Terminalor_Builder_Interface
     */
    public function addExcludeClassPattern($pattern)
    {
        $this->_excludeClassesPatterns[] = $pattern;
        return $this;
    }

    /**
     * Get given file source if $minimizeSource is true then
     * source will be minimized
     *
     * @todo find how to minimize content
     * @param string $filename file location
     * @param boolean $minimizeSource minimize or not file source
     * @return string file source
     */
    public function getFileSource($filename, $minimizeSource = null)
    {
        if (is_null($minimizeSource)) {
            $minimizeSource = $this->isMinimizeSource();
        }

        if ($minimizeSource) {
            return php_strip_whitespace($filename);
        } else {
            return file_get_contents($filename);
        }
    }

    /**
     * Check if given class name is allowed to be included in build
     * 
     * @param string $className class name
     * @return boolean true if file is allowed
     */
    public function classIsAllowed($className)
    {
        foreach ($this->getExcludeClassesPatterns() as $pattern) {
            if (preg_match($pattern, $className)) {
                return false;
            }
        }

        foreach ($this->getIncludeClassesPatterns() as $pattern) {
            if (preg_match($pattern, $className)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Strip open/close php tags from the source
     *
     * @param string $source file source
     * @return string source without php tags
     */
    public function stripPhpTags($source)
    {
        return str_replace(array('<?php', '<?', '?>'), '', $source);
    }

    /**
     * Assembles closure params from given $args
     * 
     * @param Closure $closure command closure
     * @param array $args user defined params
     * @return array array of closure params 
     */
    protected function _assembleClosureParams(Closure $closure, array $args)
    {
        $arguments = array();
        /* @var $reflection ReflectionFunction */
        $reflection = new ReflectionFunction($closure);

        /* @var $parameter ReflectionParameter */
        foreach ($reflection->getParameters() as $parameter) {
            // @codeCoverageIgnoreStart
            if (($class = $parameter->getClass())) {
                if ($class->name == 'Terminalor_Application_Interface') {
                    $arguments[] = Terminalor_Container::getInstance()
                        ->get('Terminalor.Application');
                    continue;
                }
            }
            // @codeCoverageIgnoreEnd
            if (isset($args[$parameter->name])) {
                $value = $args[$parameter->name];
            } else { // try to get default value if exists, error otherwise
                if ($parameter->isOptional()) {
                    $value = $parameter->getDefaultValue();
                } else {
                    $value = null;
                }
            }
            $arguments[] = $value;
        }

        return $arguments;
    }

        /**
     * Find all includes in source, if $removeIncludes is true
     * then it also removes includes from given source
     *
     * @param string $source file source
     * @param boolean $removeIncludes
     * @return array array of includes found in source
     */
    public function getSourceIncludes(&$source, $removeIncludes = false)
    {
        // indicates that include/require(_once) function is found
        $includeTagIsFound = false;
        // given source without includes
        $safeSource = '';
        // all found includes values
        $includeValues = '';

        foreach (token_get_all($source) as $token) {
            if (is_string($token)) {
                $safeSource .= $token;
                if ($includeTagIsFound) {
                    $includeValues .= $token;
                    if ($token == ';') {
                        $includeTagIsFound = false;
                        $safeSource = $this->_commentOutInclude($safeSource,
                            $includeIsDynamic);
                    }
                }
            } else {
                list($id, $string) = $token;
                switch ($id) {
                    case T_VARIABLE:
                        if ($includeTagIsFound) {
                            $includeIsDynamic = true;
                        }
                        break;
                    case T_INCLUDE:
                    case T_INCLUDE_ONCE:
                    case T_REQUIRE:
                    case T_REQUIRE_ONCE:
                        $includeTagIsFound = true;
                        $includeIsDynamic  = false;
                        $safeSource .= $this->_getIncludeBeginPlaceholder();
                        break;
                    default:
                        if ($includeTagIsFound) {
                            $includeValues .= $string;
                        }
                        break;
                }
                $safeSource .= $string;
            }
        }

        if ($removeIncludes) {
            $source = $safeSource;
        }

        return $this->_filterIncludeValues($includeValues);
    }

    /**
     * Get include function begin placeholder
     * 
     * @return string 
     */
    private function _getIncludeBeginPlaceholder()
    {
        return '%inc_open%';
    }

    /**
     * Get include function end placeholder
     * 
     * @return string
     */
    private function _getIncludeEndPlaceholder()
    {
        return '%inc_close%';
    }

    /**
     * Filter includes values in given string
     * 
     * @param string $includeValues includes values
     * @return array array of filtered includes values
     */
    private function _filterIncludeValues($includeValues)
    {
        $includeValues = explode(";", $includeValues);
        array_walk($includeValues, function(&$value){
            $value = str_replace(array('\'', '"', '\n'), '', trim($value));
        });

        return array_filter(array_unique($includeValues));
    }

    /**
     * Comment out or leave includes in given source. If $includeIsDynamic is
     * true then includes will be commented out
     * 
     * @param string $safeSource source
     * @param boolean $includeIsDynamic true to comment out includes
     * @return string source 
     */
    private function _commentOutInclude($safeSource, $includeIsDynamic)
    {
        $safeSource .= $this->_getIncludeEndPlaceholder();
        
        $search = array($this->_getIncludeBeginPlaceholder(),
            $this->_getIncludeEndPlaceholder());

        if ($includeIsDynamic) {
            $replace = '';
        } else {
            $replace = array('/* ', ' */null;');
        }

        return str_replace($search,
                $replace,
                $safeSource);
    }
}
