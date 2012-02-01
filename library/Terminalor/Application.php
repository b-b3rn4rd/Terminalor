<?php
/**
 * This file is part of Terminalor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Terminalor core application class responsible for managing user defined
 * commands.
 *
 * @package    Terminalor
 * @subpackage Application
 * @author     Bernard Baltrusaitis <bernard@runawaylover.info>
 * @link       http://terminalor.runawaylover.info
 */
class Terminalor_Application extends Terminalor_Application_Abstract
{
    /**
     * Check if command with given name exists
     * 
     * @param string $offset user defined command name
     * @return boolean 
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->_commands);
    }

    /**
     * Get command's body by given name
     *
     * @param string $offset command name
     * @return Closure command body
     */
    public function offsetGet($offset)
    {
        try {
            if ($this->offsetExists($offset)) {
                $command = $this->_commands[$offset];
                return $command;
            } else {
                throw new InvalidArgumentException(
                    sprintf('Can\'t execute `%s` command. It doesn\'t exists.', $offset));
            }
        } catch (InvalidArgumentException $e) {
            $this->getResponse()->message($e->getMessage(), 'error');
        }
    }

    /**
     * Register new command
     * <code>
     * $terminalor['command_name'] = function(Terminalor_Application_Interface $terminalor){
     *  $terminalor->getResponse()->message('Hello world');
     * };
     * </code>
     *
     * @param string  $offset command name
     * @param Closure $value  command body
     * @return null
     */
    public function offsetSet($offset, $value)
    {
        try {
            if ($this->offsetExists($offset)) {
                throw new InvalidArgumentException(
                    sprintf('Can\'t create `%s` command. It\'s already exists.',
                        $offset));
            }

            if (!($value instanceof Closure)) {
                throw new InvalidArgumentException(
                    sprintf('Can\'t create `%s` command. Value `%s` is not closure.',
                        $offset, $value));
            }

            $this->_commands[$offset] = $value;
        } catch (InvalidArgumentException $e) {
            $this->getResponse()->message($e->getMessage(), 'error');
        }
    }

    /**
     * Remove command with given name
     *
     * @param string $offset command name
     * @return null
     */
    public function offsetUnset($offset)
    {
        try {
            if (!$this->offsetExists($offset)) {
                throw new InvalidArgumentException(
                    sprintf('Can\'t delete `%s` command. It doesn\'t exists.', $offset));
            }
            unset($this->_commands[$offset]);
            
        } catch (InvalidArgumentException $e) {
            $this->getResponse()->message($e->getMessage(), 'error');
        }
    }
    
    /**
     * Calculate number of defined commands
     * 
     * @return int number of commands
     */
    public function count()
    {
        return count($this->_commands);
    }

    /**
     * Retriew current command
     * 
     * @return Closure 
     */
    public function current()
    {
        return current($this->_commands);
    }

    /**
     * Retriew next command
     * 
     * @return Closure 
     */
    public function next()
    {
        return next($this->_commands);
    }

    /**
     * Get current command name
     * 
     * @return string 
     */
    public function key()
    {
        return key($this->_commands);
    }

    /**
     * Check if command with given key exists
     *
     * @return boolean true if command exists
     */
    public function valid()
    {
        $offset = $this->key();
        return $this->offsetExists($offset);
    }

    /**
     * Set internal commands pointer to the first command
     *
     * @return null
     */
    public function rewind()
    {
        reset($this->_commands);
    }

    /**
     * Remove all defined commands
     * 
     * @return Terminalor_Application_Interface
     */
    public function flush()
    {
        $this->_commands = array();
        return $this;
    }
    
    /**
     * Dispatches application
     * 
     * @return null
     */
    public function  __toString()
    {
        if (!defined('_TERMINALOR_BUILDER_')) {
            parent::__toString();
        }
    }
}


