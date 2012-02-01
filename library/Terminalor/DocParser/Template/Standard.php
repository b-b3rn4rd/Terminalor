<?php
/**
 * This file is part of Terminalor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Terminalor DocParser Template Standard class responsible
 * for storing individual tags renderers.
 *
 * @package    Terminalor
 * @subpackage DocParser
 * @author     Bernard Baltrusaitis <bernard@runawaylover.info>
 * @link       http://terminalor.runawaylover.info
 */
class Terminalor_DocParser_Template_Standard
    extends Terminalor_DocParser_Template_Abstract
{
    /**
     * Description tag renderer
     * 
     * @param string $commandName command name
     * @param array $tags array of available tags
     * @param string $tagName given tag name (description in this case)
     * @return string rendered tag with style placeholders
     */
    protected function _renderDescriptionTag($commandName, array $tags, $tagName)
    {
        $return   = '';
        $tag      = Terminalor_Response_Styles::STYLE_TAG;
        $isSingle = (1 >= count($this->getVars()));
        
        if (!array_key_exists($tagName, $tags)) {
            return $return;
        }

        $description = $tags[$tagName];
        
        if ($isSingle) {
            $return = "<{$tag} class=\"info\">{$description}</{$tag}>" . "\n";
        } else {
            $return = sprintf('<'.$tag.' class="command">%s</'.$tag.'> - <'.$tag.' class="info">%s</'.$tag.'>',
                    $commandName, $description) . "\n";
        }
        
        return $return;
    }

    /**
     * Arguments tag renderer, arguments is the alias for param tag if
     * tag has an alias it should be specified in renderer name instead of
     * original tag name. As the result: _renderParamTag => _renderArgumentsTag
     * 
     * @param string $commandName string
     * @param array $tags array of available tags
     * @param string $tagName given tag name (arguments in this case)
     * @return string rendered tag with style placeholders
     */
    protected function _renderArgumentsTag($commandName, array $tags, $tagName)
    {
        $return  = '';

        if (!array_key_exists($tagName, $tags)) {
            return $return;
        }
        
        $args    = $tags[$tagName];
        $padding = $this->_findLongestElementInArray(array_keys($tags));

        $tagName = str_pad($tagName, $padding);

        $pattern = "/^([^\s]+)\s([^\s]+)\s?(.*)$/e";
        $replace = "'--'.str_replace('\$','','\\2').'=<\\1>: \\3'";
        foreach ((array)$args as $i => $arg) {
            $arg = preg_replace($pattern, $replace, $arg);
            if (0 == $i) {
                $return  .= sprintf('  %s : %s', ucfirst($tagName),
                    $arg) . "\n";
            } else {
                $return  .= sprintf('  %s   %s', str_pad(' ', $padding),
                    $arg) . "\n";
            }
        }

        return $return;
    }
}
