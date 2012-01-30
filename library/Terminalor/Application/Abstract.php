<?php
/*
 * This file is part of Terminalor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Terminalor core base application class provide global application methods
 * and getters for the Request, Response objects.
 *
 * @author      Bernard Baltrusaitis <bernard@runawaylover.info>
 * @package     Terminalor
 * @subpackage  Application
 * @link        http://terminalor.runawaylover.info
 */
abstract class Terminalor_Application_Abstract implements Terminalor_Application_Interface
{
    /**
     * Response object
     *
     * @var Terminalor_Response_Interface 
     */
    protected $_response = null;

    /**
     * Request object
     * 
     * @var Terminalor_Request_Interface 
     */
    protected $_request = null;

    /**
     * Collection of user defined commands
     * 
     * @var array
     */
    protected $_commands = array();
    
    public function  __construct(Terminalor_Response_Interface $response,
        Terminalor_Request_Interface $request)
    {
        $this->_response = $response;
        $this->_request  = $request;
    }
    
    /**
     * Get response object responsible for user interactions such as
     * displaying messages, promt, confirm. And related functionality.
     * 
     * @return Terminalor_Response_Interface
     */
    public function getResponse()
    {
        return $this->_response;
    }
    
    /**
     * Get request object responsible for commands dispatching and providing
     * access to user specified arguments
     * 
     * @return Terminalor_Request_Interface
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Get assoc array of defined commands
     *
     * @return array array of defined commands
     */
    public function getCommands()
    {
        return $this->_commands;
    }
    
    /**
     * Dispatches application and generates user output, works only under
     * cli interface, displays help if BadFunctionCallException exception is
     * catched, or --help argument is passed, help can be displayed for all
     * available commands or for specified command
     * 
     * @example ./filename commandname --help will display help for given command
     * @example ./filename --help will display help for all available commands
     * @return null
     */
    public function __toString()
    {
        // @codeCoverageIgnoreStart
        if (($interfaceType = php_sapi_name()) != 'cli') {
            user_error(sprintf('Invalid interface type `%s`', $interfaceType),
                E_USER_ERROR);
        }
        // @codeCoverageIgnoreEnd
        
        try {
            if (!$this->getRequest()->dispatch($this->getCommands())) {
                $this->help($this->getRequest()->getArgument('command'));
            }
        } catch(BadFunctionCallException $e) {
            $this->getResponse()->message($e->getMessage(), 'error');
            $this->help();
        }
    }

    /**
     * Display help for command with given $commandName if $commandName is null
     * help will be displayed for all available commands
     * 
     * @throws InvalidArgumentException if given command doesn't exists
     * @param string $commandName command name
     * @return null
     */
    public function help($commandName = null)
    {
        if (is_null($commandName)) {
            $commands = $this->getCommands();
        } else {
            if (!isset($this[$commandName])) {
                throw new InvalidArgumentException(
                    sprintf('Can\'t display help `%s` doesn\'t exists', $commandName));
            }
            $commands = array($commandName => $this[$commandName]);
        }

        $this->getResponse()->displayHelp($commands);
    }

    /**
     * Terminalor autoload function, loads related classes
     *
     * @see autoloadRegister()
     * @param string $class class name to include
     * @return null
     */
    static private function _autoload($class)
    {
        $filename  = sprintf('%s/%s.php',
            self::getLibraryPath(),
            str_replace('_', '/', $class));

        if (is_readable($filename)) {
            include($filename);
        }
    }
    
    /**
     * Register Terminalor autoloader
     * 
     * @see _autoload()
     * @return null
     */
    static public function autoloadRegister()
    {
        spl_autoload_register(array(__CLASS__, '_autoload'));
    }

    /**
     * Get library absolute pathname
     * 
     * @return string library path
     */
    static public function getLibraryPath()
    {
        return realpath(dirname(__FILE__) . '/../../');
    }
}
