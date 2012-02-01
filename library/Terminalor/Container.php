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
class Terminalor_Container implements Terminalor_Container_Interface
{
    /**
     * Singleton storage
     * 
     * @var Terminalor_Container_Interface
     */
    static private $_instance = null;
    /**
     * Storage of instances indexed
     * by class alias
     * 
     * @var array 
     */
    private $_instances = array();
    /**
     * Storage of classes configs
     * indexed by class alias
     * 
     * @var array
     */
    private $_dependencies = array();

    /**
     * Get defined instances
     * 
     * @return array array of defined instances
     */
    public function getInstances()
    {
        return $this->_instances;
    }

    /**
     * Register new class in dependencies
     * 
     * @param string $alias class alias
     * @param string $class full class name
     * @param array $arguments class constructor args
     * @return Terminalor_Container
     */
    public function add($alias, $class, array $arguments = null)
    {
        $this->_dependencies[$alias]['class']     = $class;
        $this->_dependencies[$alias]['arguments'] = $arguments;

        return $this;
    }

    /**
     * Get class instance by alias, make new instance if it doesn't exists
     * 
     * @throws InvalidArgumentException if class with given alias doesn't exists in dependency map
     * @param string $alias class alias
     * @param boolean $asNewInstance create new instance anyway
     * @return object instance of class
     */
    public function get($alias, $asNewInstance = false)
    {
        if (array_key_exists($alias, $this->_instances)
            && !$asNewInstance) {
            $class = $this->_instances[$alias];

            return $class;
        } else { // try to create instance for given class
            if (array_key_exists($alias, $this->_dependencies)) {
                $config = $this->_dependencies[$alias];
                return $this->_instance($alias, $config['class'], $config['arguments']);
            } else { //class is not registred
                throw new InvalidArgumentException(
                    sprintf('Class `%s` is not installed in Dependency map !', $alias));
            }
        }
    }
    
    /**
     * Create instance and register for given class method
     * can call itself recursevly to init. arguments
     * 
     * @param string $alias class alias
     * @param string $className full class name
     * @param array $argAliases class constructor args.
     * @return object instance of class
     */
    private function _instance($alias, $className, array $argAliases = null) {
        if (array_key_exists($alias, $this->_instances)) {
            return null;
        }
        
        $arguments = null;
        /* @var $reflection ReflectionClass */
        $reflection = new ReflectionClass($className);
        if (is_array($argAliases)) {
            // given class constructor arguments
            foreach ($argAliases as $argAlias) {
                if (array_key_exists($argAlias, $this->_instances)) {
                    $arguments[] = $this->_instances[$argAlias];
                } else { // argument is not ready, need to init it
                    if (array_key_exists($argAlias, $this->_dependencies)) {
                        $attributes = array_values($this->_dependencies[$argAlias]);
                        array_unshift($attributes, $argAlias);
                        $arguments[] = call_user_func_array(array($this, '_instance'),
                            $attributes);
                    } else {
                        $arguments[] = $argAlias;
                    }
                }
            }
        }
        // if class has constructor pass arguments, else just init class
        if ($reflection->getConstructor() && $arguments) {
            $instance = $reflection->newInstanceArgs($arguments);
        } else {
            $instance = $reflection->newInstance();
        }
        
        $this->_instances[$alias] = $instance;

        return $instance;
    }

    /**
     * Execute all registred dependencies
     *
     * @return null
     */
    public function executeInstances()
    {
        foreach ($this->_dependencies as $alias => $config) {
            if (!array_key_exists($alias, $this->_instances)) {
                $attributes = array_values($config);
                array_unshift($attributes, $alias);
                call_user_func_array(array($this, '_instance'), $attributes);
            }
        }
    }

    /**
     * Register and execute default dependencies
     *
     * @return Terminalor_Container_Interface
     */
    public function setDefaultDependencies()
    {
        $defaultDependencies = array(
            'Terminalor.Template'       => array('Terminalor_DocParser_Template_Standard'),
            'Terminalor.DocParser'      => array('Terminalor_DocParser',
                array('Terminalor.Template')),
            'Terminalor.Styles.Entity'  => array('Terminalor_Response_Styles_Entity'),
            'Terminalor.Styles'         => array('Terminalor_Response_Styles',
                array('Terminalor.Styles.Entity')),
            'Terminalor.Response'       => array('Terminalor_Response',
                array('Terminalor.Styles', 'Terminalor.DocParser')),
            'Terminalor.Router'         => array('Terminalor_Request_Router_Standard'),
            'Terminalor.Request'        => array('Terminalor_Request',
                array('Terminalor.Router')),
            'Terminalor.Application'    => array('Terminalor_Application',
                array('Terminalor.Response', 'Terminalor.Request'))
        );

        foreach ($defaultDependencies as $alias => $config) {
            $config = array_pad($config, 2, null);
            
            list($class, $arguments) = $config;
            $this->add($alias, $class, $arguments);
        }

        $this->executeInstances();

        return $this;
    }

    /**
     * Singleton
     * 
     * @return Terminalor_Container_Interface
     */
    static public function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
}
