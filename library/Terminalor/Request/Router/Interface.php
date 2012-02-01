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
interface Terminalor_Request_Router_Interface
{
    /**
     * Check if user passed command name
     *
     * @return boolean
     */
    public function commandNameIsPassed();

    /**
     * Set default command name, if null is given no default command
     * 
     * @throws BadMethodCallException if $commandName is not string or null
     * @param string $commandName
     * @return Terminalor_Request_Router_Interface
     */
    public function setDefaultCommandName($commandName);

    /**
     * Get default command name
     *
     * @return string
     */
    public function getDefaultCommandName();

    /**
     * Parses $_SERVER['argv'] arguments into assoc array
     * each argument should have "--" prefix, first word supposed
     * to be command name. If argument doesnt have value if will be
     * true, if command name is missing default command name will
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
    public function parse(array $arguments = null);
}
