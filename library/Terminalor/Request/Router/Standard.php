<?php
/**
 * This file is part of Terminalor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Terminator Response Router Standard default router responsible for parsing
 * $_SERVER['argv'] var into assoc array.
 *
 * @package    Terminalor
 * @subpackage Response
 * @author     Bernard Baltrusaitis <bernard@runawaylover.info>
 * @link       http://terminalor.runawaylover.info
 */
class Terminalor_Request_Router_Standard implements Terminalor_Request_Router_Interface
{
    private $_commandNameIsPassed = false;
    
    private $_defaultArguments = array(
        'command' => 'index'
    );
    
    /**
     * Check if user passed command name
     * 
     * @return boolean
     */
    public function commandNameIsPassed()
    {
        return $this->_commandNameIsPassed;
    }

    /**
     * Set default command name, if null is given no default command
     * 
     * @throws BadMethodCallException if $commandName is not string or null
     * @param null|string $commandName
     * @return Terminalor_Request_Router_Interface
     */
    public function setDefaultCommandName($commandName)
    {
        if (!is_null($commandName) && !is_string($commandName)) {
            throw new BadMethodCallException(
                sprintf('Can\'t set default command name. `%s` is not string or null',
                    gettype($commandName)));
        }
        
        $this->_defaultArguments['command'] = $commandName;
        return $this;
    }

    /**
     * Get default command name
     * 
     * @return string 
     */
    public function getDefaultCommandName()
    {
        return $this->_defaultArguments['command'];
    }

    /**
     * Parses $_SERVER['argv'] arguments into assoc array
     * each argument should have "--" prefix, first word supposed
     * to be command name. If argument doesnt have value if will have
     * true value, if command name is missing default command name will
     * be used instead
     * <code>
     * // php filename.php adduser --name=bernard --home
     * array(
     *  'command' => 'adduser'
     *  'name'    => 'bernard',
     *  'home'    => true
     * );
     * </code>
     * 
     * @param array $arguments default arguments values
     * @return array parsed arguments
     */
    public function parse(array $arguments = null)
    {
        if (is_null($arguments)) {
            $arguments = $this->_defaultArguments;
        }
        
        $rawArguments = (array)$_SERVER['argv'];
        array_shift($rawArguments);

        if (is_array($rawArguments) && count($rawArguments)) {
            $argPattern = '([\w\-]+)';
            $valPattern = '\'?"?(.*?)"?\'?';
            
            foreach ($rawArguments as $index => $arg) {
                if (preg_match("/^\-\-{$argPattern}=?{$valPattern}$/i", $arg, $matches)) {
                    // check if argument value is passed, true value otherwise
                    $arguments[$matches[1]] = (isset($matches[2]) && !empty($matches[2])
                        ? $matches[2] : true);
                } else {
                    if (0 == $index) {
                        $this->_commandNameIsPassed = true;
                        $arguments['command'] = $arg;
                    }
                }
            }
        }
        
        return $arguments;
    }
}
