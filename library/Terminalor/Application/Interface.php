<?php
/*
 * This file is part of Terminalor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * The application core interface of Terminalor
 *
 * @author      Bernard Baltrusaitis <bernard@runawaylover.info>
 * @package     Terminalor
 * @subpackage  Application
 * @link        http://terminalor.runawaylover.info
 */
interface Terminalor_Application_Interface extends ArrayAccess, Countable, Iterator
{
    /**
     * Get response object responsible for user interactions such as
     * displaying messages, promt, confirm. And related functionality.
     *
     * @return Terminalor_Response_Interface
     */
    public function getResponse();

    /**
     * Get request object responsible for commands dispatching and providing
     * access to user specified arguments
     *
     * @return Terminalor_Request_Interface
     */
    public function getRequest();

    /**
     * Get array of user defined commands
     *
     * @return array user defined commands
     */
    public function getCommands();

    /**
     * Dispatches application and generates user output,
     * works only under cli interface, displays help if
     * BadFunctionCallException exception is catched, or
     * --help argument is passed, help can be displayed for all
     * available commands or for given command
     *
     * @example ./filename commandname --help will display help for given command
     * @example ./filename --help will display help for all available commands
     * @return null
     */
    public function __toString();

    /**
     * Shows help for all available commands if $commandName is null
     * display help for given $commandName otherwise
     * 
     * @throws InvalidArgumentException if given command doesn't exists
     * @param string $commandName defined command name
     * @return null
     */
    public function help($commandName = null);

    /**
     * Remove all defined commands
     *
     * @return Terminalor_Application_Interface
     */
    public function flush();

    /**
     * Get library absolute path with "/" on the end
     *
     * @return string library path
     */
    static public function getLibraryPath();
}
