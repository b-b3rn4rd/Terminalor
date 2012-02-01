<?php
/**
 * This file is part of Terminalor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Terminator Response class responsible for user interactions such as
 * displaying messages, promt, confirm. And related functionality.
 *
 * @package    Terminalor
 * @subpackage Response
 * @author     Bernard Baltrusaitis <bernard@runawaylover.info>
 * @link       http://terminalor.runawaylover.info
 */
interface Terminalor_Response_Interface
{
    /**
     * Get Style object responsible for managing colors and styles
     * which are used in decorating user messages
     *
     * @return Terminalor_Response_Styles_Interface
     */
    public function getStyle();

    /**
     * Get DocParser object responsible for parsing given php doc comment
     *
     * @return Terminalor_DocParser_Interface
     */
    public function getDocParser();

    /**
     * Get input stream
     *
     * @return string input stream
     */
    public function getInputStream();

    /**
     * Get output stream
     *
     * @return string output stream
     */
    public function getOutputStream();

    /**
     * Set input stream
     *
     * @param string $filename input stream
     * @return Terminalor_Response_Interface
     */
    public function setInputStream($filename);

    /**
     * Set output stream
     *
     * @param string $filename output stream
     * @return Terminalor_Response_Interface
     */
    public function setOutputStream($filename);

    /**
     * Output given $message, without any modification
     * 
     * @see message() to send message with styles
     * @param string $message string to display
     * @return boolean true if $message was sent, false otherwise
     */
    public function sendRawMessage($message);

    /**
     * Output given message, with style.
     * If $message is array or object it will be displayed as a table.
     * Using @see createTableFromArray() method.
     * $messageStyleName can be registred style name or array of style options.
     * If $function is provided and $message type is array it will be
     * applied to each array element.
     * <code>
     * // pass style options
     * $messageStyleName = array('colorName' => 'white',
     * 'backgroundName' => 'red', 'underline' => true);
     * $this->message('your message', $messageStyleName);
     * </code>
     * <code>
     * // pass defined style name
     * $this->message('your message', 'error');
     * </code>
     * <code>
     * // display array with $function applied to each element
     * $message = array(array('id' => 1, 'name' => 'john'),
     * array('id' => 2, 'name' => 'jack'));
     * $this->message($message, null, function($v){return ucfirst($v);});
     * </code>
     * @see createTableFromArray() how array is transformed
     * @see Terminalor_Response_Styles::getStyles() to get defined styles
     * @see Terminalor_Response_Styles::getColors() to get defined colors
     * @param array|string|object $message message to display
     * @param array|string $messageStyleName defined style name or array of style options
     * @param Closure $function callback function applies to each array element
     * @return boolean true if message was sent
     */
    public function message($message, $messageStyleName = null, Closure $function = null);

    /**
     * Ask's user to provide response for given message.
     * <code>
     * $response = $this->promt('How old are you ?');
     * </code>
     *
     * @see message() how to set $messageStyleName param
     * @param string|array $messageStyleName message style
     * @param string|null $promtSign terminal promt sign
     * @return string user response
     */
    public function promt($message, $messageStyleName = null, $promtSign = '> ');

    /**
     * Ask's user to confirm given message, $promtSign appends
     * tip to the message. Will continue to display
     * confirm message until user provides 'y' or 'n'.
     * <code>
     * // ask user to confirm given $message in green color,
     * // $agree is true if user inputs 'y', false if 'n'
     * $messageStyle = array('colorName' => 'green');
     * $agree = $this->confirm('Do you love beer ?', $messageStyle);
     * </code>
     * 
     * @see message() how to set $messageStyleName param
     * @param string $message user message to confirm
     * @param array|string $messageStyleName message style
     * @param string|null $promtSign append text with tip
     * @return boolean user choice true if user inputs y
     */
    public function confirm($message, $messageStyleName = null, $promtSign = '[Y|N] ');

    /**
     * Applies given style to the $message
     * <code>
     * // pass defined style
     * $styledMessage = $this->applyStyleToMessage('your message', 'success');
     * // pass style options
     * $messageStyleName = array('colorName' => 'red', 'bold' => true);
     * $styledMessage = $this->applyStyleToMessage('your message', $messageStyleName);
     * </code>
     *
     * @see message() how to set $messageStyleName param
     * @param string $message
     * @param string|array $messageStyleName style name to apply
     * @return string message with applied style
     */
    public function applyStyleToMessage($message, $messageStyleName = null);

    /**
     * Replace style placeholders in $message with actual styles
     * <code>
     * $tag = Terminalor_Response_Styles::STYLE_TAG;
     * $message = "hello <$tag class="success">world</$tag>";
     * $styledMessage = $this->applyStylePlaceholdersToMessage($message);
     * </code>
     * 
     * @param string $message with styles placeholders
     * @return string|boolean message with applied styles or false
     */
    public function applyStylePlaceholdersToMessage($message);

    /**
     * Display help for given user defined commands
     *
     * @param array $commands array of user defined command
     * @return null
     */
    public function displayHelp(array $commands);

    /**
     * Convert given array into table, array keys will become table column names
     * if $function is provided it will be applied to each array element in table.
     * array will be limited up to 2nd level.
     * <code>
     * $options = array('age' => '25', 'sex' => 'male');
     * $array = array(
     *   array('id' => 1, 'name' => 'bernard'),
     *   array('id' => 2, 'name' => 'john', 'options' => $options),
     * );
     * echo $this->createTableFromArray($array, function($v){return strtoupper($v);});
     * // produces
     * + -- + ------- + -------- +
     * | id | name    | options  |
     * + -- + ------- + -------- +
     * | 1  | BERNARD |          |
     * | 2  | JOHN    | 25, MALE |
     * + -- + ------- + -------- +
     * </code>
     *
     * @param array $array array to convert
     * @param Closure $function function to apply to each array element
     * @return string arrays data represented in the table
     */
    public function createTableFromArray(array $array, Closure $function = null);
}
