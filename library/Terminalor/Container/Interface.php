<?php
/**
 * This file is part of Terminalor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Terminalor Container dependency injection container
 *
 * @package    Terminalor
 * @subpackage Container
 * @author     Bernard Baltrusaitis <bernard@runawaylover.info>
 * @link       http://terminalor.runawaylover.info
 */
interface Terminalor_Container_Interface
{
    /**
     * Get defined instances
     * 
     * @return array array of defined instances
     */
    public function getInstances();
    
    /**
     * Register new class in container
     *
     * @param string $alias class alias
     * @param string $class full class name
     * @param array $arguments class constructor args
     * @return Terminalor_Container_Interface
     */
    public function add($alias, $class, array $arguments = null);

    /**
     * Get class instance by alias, make new instance if it doesn't exists
     *
     * @throws InvalidArgumentException if class with given alias doesn't exists in dependency map
     * @param string $alias class alias
     * @param boolean $asNewInstance create new instance
     * @return object instance of class
     */
    public function get($alias, $asNewInstance = false);

    /**
     * Execute all registred dependencies
     *
     * @return null
     */
    public function executeInstances();

    /**
     * Register and execute default dependencies
     *
     * @return Terminalor_Container_Interface
     */
    public function setDefaultDependencies();
}
