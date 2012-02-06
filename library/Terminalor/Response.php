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
class Terminalor_Response implements Terminalor_Response_Interface
{
    /**
     * Style object
     * 
     * @var Terminalor_Response_Styles_Interface
     */
    private $_style = null;
    /**
     * DocParser object
     * 
     * @var Terminalor_DocParser_Interface
     */
    private $_docParser = null;

    /**
     * Input stream name
     * 
     * @var string input stream 
     */
    private $_inputStream  = 'php://stdin';

    /**
     * Output stream name
     * 
     * @var string output stream 
     */
    private $_outputStream = 'php://stdout';
    
    public function  __construct(Terminalor_Response_Styles_Interface $styles,
        Terminalor_DocParser_Interface $docParser)
    {
        $this->_style     = $styles;
        $this->_docParser = $docParser;
    }

    /**
     * Get Style object responsible for managing colors and styles
     * which are used in decorating user messages
     * 
     * @return Terminalor_Response_Styles_Interface 
     */
    public function getStyle()
    {
        return $this->_style;
    }

    /**
     * Get DocParser object responsible for parsing given php doc comment
     * 
     * @return Terminalor_DocParser_Interface
     */
    public function getDocParser()
    {
        return $this->_docParser;
    }

    /**
     * Get input stream
     * 
     * @return string input stream 
     */
    public function getInputStream()
    {
        return $this->_inputStream;
    }

    /**
     * Get output stream
     * 
     * @return string output stream 
     */
    public function getOutputStream()
    {
        return $this->_outputStream;
    }

    /**
     * Set input stream
     * 
     * @param string $filename input stream
     * @return Terminalor_Response_Interface
     */
    public function setInputStream($filename)
    {
        $this->_inputStream = $filename;
        return $this;
    }

    /**
     * Set output stream
     * 
     * @param string $filename output stream
     * @return Terminalor_Response_Interface
     */
    public function setOutputStream($filename)
    {
        $this->_outputStream = $filename;
        return $this;
    }

    /**
     * Output given message, without any modification
     * 
     * @param string $message string to display
     * @return boolean true if $message was sent, false otherwise
     */
    public function sendRawMessage($message)
    {
        try {
            $this->_messageIsString($message);
            $f = fopen($this->getOutputStream(), 'w');
            fputs($f, $message);
            fclose($f);
            return true;
        } catch (InvalidArgumentException $e) {
            $this->message($e->getMessage(), 'error');
            return false;
        }
    }
    
    /**
     * Output given message, with style. If $message
     * is array or object it will be displayed as a table. Where array
     * keys will become column names. $messageStyleName
     * can be registred style name or array of style options.
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
     * // display array
     * $message = array(array('id' => 1, 'name' => 'john'),
     * array('id' => 2, 'name' => 'jack'));
     * $this->message($message, null, function($v){return ucfirst($v);});
     * </code>
     *
     * @see Terminalor_Response_Styles::getStyles() to get defined styles
     * @see Terminalor_Response_Styles::getColors() to get defined colors
     * @param array|string|object $message message to display
     * @param array|string $messageStyleName defined style name or array of style options
     * @param Closure $function callback function applies to each array element
     * @return boolean true if message was sent
     */
    public function message($message, $messageStyleName = null, Closure $function = null)
    {
        if (is_array($message) || is_object($message)) {
            $message = $this->createTableFromArray((array)$message, $function);
        }
        
        $styledMessage = $this->applyStyleToMessage((string)$message, $messageStyleName);

        return $this->sendRawMessage($styledMessage."\n");
    }

    /**
     * Ask's user to provide response for given message.
     * <code>
     * $response = $this->promt('How old are you ?');
     * </code>
     *
     * @see message() how to set $messageStyleName param
     * @param string $message user message
     * @param string|array $messageStyleName message style
     * @param string|null $promtSign terminal promt sign
     * @return string user response text
     */
    public function promt($message, $messageStyleName = null, $promtSign = '> ')
    {
        $this->message($message, $messageStyleName);

        if (!is_null($promtSign)) {
            $this->sendRawMessage($promtSign);
        }

        $f = fopen($this->getInputStream(), 'r');
        $response = trim(fgets($f));
        fclose($f);

        return $response;
    }

    /**
     * Ask's user to confirm given message, $promtSign appends
     * tip to the message. Will continue to display
     * confirm until user provides 'y' or 'n'.
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
    public function confirm($message, $messageStyleName = null, $promtSign = '[Y|N] ')
    {
        do {
            $response = $this->promt($message, $messageStyleName, $promtSign);
        } while (!preg_match('/(y|n)/i', $response, $matches));

        $answer = $matches[1];
        return ($answer == 'y' ? true : false);
    }

    /**
     * Apply given style to message
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
    public function applyStyleToMessage($message, $messageStyleName = null)
    {
        $return = '';

        try {
            $this->_messageIsString($message);
            $messageStyle = $this->getStyle()
                ->getStyleValues($messageStyleName);
        } catch (InvalidArgumentException $e) {
            $this->message($e->getMessage(), 'error');
            $messageStyle = $this->getStyle()
                ->getStyleValues($this->getStyle()
                    ->getDefaultStyleName());
        } 
        
        if (!is_null($messageStyle->getColorName())) {
            $return .= "\033[{$messageStyle->getColorValue()}m";
        }
        
        if (!is_null($messageStyle->getBackgroundName())) {
            $return .= "\033[{$messageStyle->getBackgroundValue()}m";
        }
        
        if ($messageStyle->hasBold()) {
            $return .= "\033[1m";
        }
        
        if ($messageStyle->hasUnderline()) {
            $return .= "\033[4m";
        }
        
        $return .= $message . "\033[0m";
        
        return $return;
    }

    /**
     * Replace style placeholders in message with actual styles
     * <code>
     * $tag = Terminalor_Response_Styles::STYLE_TAG;
     * $message = "hello <$tag class="success">world</$tag>";
     * $styledMessage = $this->applyStylePlaceholdersToMessage($message);
     * </code>
     * 
     * @param string $message with styles placeholders
     * @return string|boolean message with applied styles or false
     */
    public function applyStylePlaceholdersToMessage($message)
    {
        try {
            $this->_messageIsString($message);
            $tag     = Terminalor_Response_Styles::STYLE_TAG;
            $pattern = '/<'.$tag.'\s{1,}class="([^"]+)">([^<]+)<\/'.$tag.'>/e';
            $replace = "call_user_func(array(\$this, 'applyStyleToMessage'), '\\2', '\\1')";
            $message = preg_replace($pattern, $replace, $message);
            return $message;
        } catch (InvalidArgumentException $e) {
            $this->message($e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * Display help for given user defined commands
     * 
     * @param array $commands array of user defined command
     * @return null
     */
    public function displayHelp(array $commands)
    {
        foreach ($commands as $commandName => $command) {
            /* @var $reflection ReflectionFunction */
            $reflection = new ReflectionFunction($command);
            $this->getDocParser()
                ->setCommandName($commandName)
                ->setRawDocComment($reflection->getDocComment())
                ->parseRawDocComment()
                ->flush();
        }
        
        $help = $this->getDocParser()->buildHelp();
        $this->sendRawMessage($this
            ->applyStylePlaceholdersToMessage($help));
    }

    /**
     * Convert given array into table, array keys will become table column names
     * if $function is provided it will be applied to each array element in table.
     * array will be limited up to 2nd level.
     * 
     * @param array $array array to convert
     * @param Closure $function function to apply to each array element
     * @return string arrays data represented in the table
     */
    public function createTableFromArray(array $array, Closure $function = null)
    {
        $return = '';

        if (0 == count($array)) {
            return $return;
        }
        
        $array = $this->_transformToSmartArray($array);
        
        // prepend array keys as new element
        array_unshift($array, array_keys(reset($array)));

        /** use numeric keys instead by extracting values */
        $array = array_map(function($row) {
            return array_values($row);
        }, $array);

        // max column sizes holder with 0 as def. values
        $cellSizes = array_fill_keys(range(0,
            count($array[0])-1), 0);

        /** find longest el. in each column and apply closure except header */
        array_walk_recursive($array, function(&$cell, $cellNum) use (&$cellSizes, $function) {
            $maxSize = &$cellSizes[$cellNum];

            if ($maxSize && $function instanceof Closure) {
                $cell = $function($cell);
            }
            
            if ($maxSize < ($size = strlen($cell))) {
                $maxSize = $size;
            }
        });
        
        /** function draws separation line */
        $line = function ($row = null) {
            static $return;

            if (!is_null($row)) {
                $return = sprintf('+ %s +', implode(' + ', array_map(function($cell) {
                    return str_repeat('-', strlen($cell));
                }, $row)))."\n";
            }

            return $return;
        };

        // pad cells till longest cell in column
        foreach ($array as $rowNum => $row) {
            foreach ($row as $cellNum => &$cell) {
                $size = $cellSizes[$cellNum];
                $cell = str_pad($cell, $size);
                unset($cell);
            }

            $return .= sprintf('| %s |', implode(' | ', $row))."\n";

            if (0 == $rowNum) {
                $return .= $line($row);
            }
        }

        $return = $line() . $return . $line();

        return trim($return);
    }
    
    /**
     * Transforms array, collection of arrays/objects to
     * collection of arrays, set the depth of this collection
     * to 2nd level, by converting 2nd level elements into strings
     * if they are arrays. If single dimension array is passed it
     * will be converted to multi dimension array as well
     * 
     * @param array $array array to output in message
     * @return array 
     */
    private function _transformToSmartArray(array $array)
    {
        $array = json_decode(@json_encode($array), true);

        if (count($array) == count($array, true)) {
            $array = array($array);
        }
        
        $maxRowNum  = 0;
        $maxRowSize = 0;
        foreach ($array as $rowNum => &$row) {
            $row  = (array)$row;
            $size = count($row);
            if ($size > $maxRowSize) {
                $maxRowNum  = $rowNum;
                $maxRowSize = $size;
            }
            foreach ($row as &$cell) {
                if (is_array($cell)) {
                    /** remove non arrays */
                    $cell = array_filter($cell, function($value){
                        return !is_array($value);
                    });
                    $cell = implode(', ', $cell);
                }
                unset($cell);
            }
            unset($row);
        }
        // pad array el. up to longest el.
        foreach ($array as &$row) {
            $size = count($row);
            if ($size < $maxRowSize) {
                $diff = array_diff_key($array[$maxRowNum], $row);
                array_walk($diff, function(&$v) { $v = ''; 
                });
                
                $row = $row + $diff;
            }
            unset($row);
        }
        
        return $array;
    }

    /**
     * Validate if type of given variable is string
     * 
     * @throws InvalidArgumentException if type is not string
     * @param mixed $message given message
     * @return boolean true if message type is string
     */
    private function _messageIsString($message)
    {
        if (($messageType = gettype($message)) != 'string') {
            throw new InvalidArgumentException(
                sprintf('Invalid message type `%s`, string expected', $messageType));
        }
        
        return true;
    }
}