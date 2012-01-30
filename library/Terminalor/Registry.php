<?php
/*
 * This file is part of Terminalor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Terminalor Registry class responsible for managing global data
 *
 * @author      Bernard Baltrusaitis <bernard@runawaylover.info>
 * @package     Terminalor
 * @subpackage  Registry
 * @link        http://terminalor.runawaylover.info
 */
class Terminalor_Registry extends ArrayObject
{
    /**
     * Singletone storage
     * 
     * @var Terminalor_Registry  
     */
    private static $_instance = null;

    /**
     * Get registry instance
     * 
     * @return Terminalor_Registry 
     */
    static public function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
    
    /**
     * Get value by index, if value with given index
     * doesn't exists, return default value instead
     * 
     * @param string $index value index
     * @param mixed $default default value
     * @return mixed value
     */
    static public function get($index, $default = null)
    {
        $self = self::getInstance();
        
        if ($self->offsetExists($index)) {
            return $self->offsetGet($index);
        } else {
            return $default;
        }
    }

    /**
     * Check if value with given index exists
     * 
     * @param string $index value index
     * @return boolean true if value exists
     */
    static public function has($index)
    {
        return self::getInstance()->offsetExists($index);
    }

    /**
     * Set value by index
     * 
     * @param string $index
     * @param mixed $value
     * @return null
     */
    static public function set($index, $value)
    {
        self::getInstance()->offsetSet($index, $value);
    }
}
