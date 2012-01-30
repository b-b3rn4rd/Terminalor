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
interface Terminalor_Request_Interface
{
    /**
     * Get router object responsible for parsing $_SERVER['argv'] into assoc array
     *
     * @return Terminalor_Request_Router_Interface
     */
    public function getRouter();

    /**
     * Get user argument value by argument name
     *
     * @param string $name
     * @param mixed $defaultValue if argument doesn't exists return $defaultValue
     * @return mixed argument value
     */
    public function getArgument($name, $defaultValue = null);

    /**
     * Get assoc. array of user provided arguments
     *
     * @return array
     */
    public function getArguments();
    
    /**
     * Set user argument value
     *
     * @param string $name
     * @param string $value
     * @return Terminalor_Request_Interface
     */
    public function setArgument($name, $value);

    /**
     * Set request default arguments values
     * 
     * <i>To set default arguments values this method should be executed
     * before @see dispatch()
     * </i>
     * @return Terminalor_Request_Interface
     */
    public function setArguments(array $arguments);

    /**
     * Dispatches application
     *
     * @throws BadMethodCallException if command with given name doesn't exists
     * @param array $commands array of defined commands
     * @return boolean false if help argument is passed
     */
    public function dispatch(array $commands);
}
