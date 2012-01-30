<?php
/*
 * This file is part of Terminalor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Terminator Request class responsible for commands dispatching and providing
 * access to user specified arguments
 *
 * @author      Bernard Baltrusaitis <bernard@runawaylover.info>
 * @package     Terminalor
 * @subpackage  Request
 * @link        http://terminalor.runawaylover.info
 */
class Terminalor_Request implements Terminalor_Request_Interface
{
    /**
     * Current router
     * 
     * @var Terminalor_Request_Router_Interface 
     */
    private $_router = null;

    /**
     * Assoc array of passed arguments
     * 
     * @var array
     */
    private $_arguments = array();

    public function  __construct(Terminalor_Request_Router_Interface $router)
    {
        $this->_router = $router;
    }

    /**
     * Get router object responsible for parsing $_SERVER['argv'] into assoc array
     * 
     * @return Terminalor_Request_Router_Interface 
     */
    public function getRouter()
    {
        return $this->_router;
    }

    /**
     * Get user argument value by argument name
     * 
     * @param string $name
     * @param mixed $defaultValue if argument doesn't exists return this value
     * @return mixed argument value
     */
    public function getArgument($name, $defaultValue = null)
    {
       $arguments = $this->getArguments();

       if (array_key_exists($name, $arguments)) {
            return $arguments[$name];
       } else {
           return $defaultValue;
       }
    }

    /**
     * Set user argument value
     * 
     * @param string $name
     * @param string $value
     * @return Terminalor_Request_Interface
     */
    public function setArgument($name, $value)
    {
        $this->_arguments[$name] = $value;
        return $this;
    }

    /**
     * Get assoc. array of user provided arguments
     * 
     * @return array 
     */
    public function getArguments()
    {
        return $this->_arguments;
    }

    /**
     * Set request default arguments values
     * 
     * <i>To set default arguments values this method should be executed
     * before @see dispatch()
     * </i>
     * @return Terminalor_Request_Interface
     */
    public function setArguments(array $arguments)
    {
        $this->_arguments = $arguments;
        return $this;
    }

    /**
     * Dispatches application
     *
     * @throws BadMethodCallException if command with given name doesn't exists
     * @param array $commands array of user defined commands
     * @return boolean false if help argument is passed
     */
    public function dispatch(array $commands)
    {
        $this->_mergeArguments((array)$this->_router->parse());
        $commandName = $this->getArgument('command');

        if (array_key_exists('help', $this->getArguments())) {
            if (!$this->getRouter()->commandNameIsPassed()) {
                $this->setArgument('command', null);
            }
            
            return false;
        }

        // check that given command exists
        if (isset($commands[$commandName])) {
            $reflection = new ReflectionFunction($commands[$commandName]);
            // assemble arguments for given command
            $arguments  = $this->_assembleFunctionArgs($reflection, $commandName);
            // execute given command
            if (count($arguments)) {
                $reflection->invokeArgs($arguments);
            } else {
                $reflection->invoke();
            }
            return true;
        } else {
            if (is_null($commandName)) {
                $message = 'Default command is not specified';
            } else {
                $message = sprintf('Command `%s` doesn\'t exists in Terminalor',
                    $commandName);
            }
            throw new BadMethodCallException($message);
        }
    }
    
    /**
     * Assemble arguments for given function
     * uses user provided arguments against reflection parameters
     * if argument is intanceof Terminalor_Application_Interface assign this
     * parameter value auto.
     * 
     * @throws BadFunctionCallException if command arguments are invalid
     * @param ReflectionFunction $reflection
     * @param string $commandName given command name
     * @return array arguments for function
     */
    private function _assembleFunctionArgs(ReflectionFunction $reflection, $commandName = null)
    {
        $arguments  = array();
        /* @var $parameter ReflectionParameter */
        foreach ($reflection->getParameters() as $parameter) {
            if (($class = $parameter->getClass())) {
                // @codeCoverageIgnoreStart
                // if given argument's instance is Terminalor_Application_Interface, assign value auto.
                if ($class->name == 'Terminalor_Application_Interface') {
                    $arguments[] = Terminalor_Container::getInstance()
                        ->get('Terminalor.Application');
                    continue;
                }
            }
            // @codeCoverageIgnoreEnd
            // if given argument is passed by user, get it value
            if (isset($this->_arguments[$parameter->name])) {
                $value = $this->_arguments[$parameter->name];
            } else { // try to get default value if exists, error otherwise
                if ($parameter->isOptional()) {
                    $value = $parameter->getDefaultValue();
                } else {
                    throw new BadFunctionCallException(sprintf('Argument `%s` is missing for `%s` command',
                        $parameter->name, $commandName));
                }
            }

            $arguments[] = $value;
        }

        return $arguments;
    }

    /**
     * Merge existing arguments with new ones, identical existing arguments
     * will be replaces with new values
     * 
     * @param array $arguments
     * @return null
     */
    private function _mergeArguments(array $arguments)
    {
        $newArguments = array_merge($this->getArguments(), $arguments);
        $this->setArguments($newArguments);
    }
}
