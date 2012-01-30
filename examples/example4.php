<?php
/**
 * Example showing some available manipulations with styles and Terminalor_Response
 * object
 *
 * @author      Bernard Baltrusaitis <bernard@runawaylover.info>
 * @package     Terminalor
 * @link        http://terminalor.runawaylover.info
 */

require_once dirname(__FILE__). '/../library/terminalor_required.php';

/* @var $terminalor Terminalor_Application_Interface */
$terminalor = Terminalor_Container::getInstance()
    ->get('Terminalor.Application');

// set default command to null
$terminalor->getRequest()
    ->getRouter()
    ->setDefaultCommandName(null);

// set default color
$terminalor->getResponse()
    ->getStyle()
    ->setDefaultColor('green');

// add new color
$terminalor->getResponse()
    ->getStyle()
    ->installColor('cyan', '0;36');

// display message with new style
$terminalor->getResponse()->message('This message in cyan color bold', array(
    'colorName' => 'cyan',
    'bold'      => true
));

// set default style
$terminalor->getResponse()
    ->getStyle()
    ->setDefaultStyle('success');

// display message using default style
$terminalor->getResponse()->message('Message displayed using default style');

// display array using message() method as table in green color
$terminalor->getResponse()->message(array(
    array('id' => 1, 'name' => 'bernard', 'email' => 'bernard@runawaylover.info'),
    array('id' => 2, 'name' => 'john',    'email' => 'john.doh@runawaylover.info')
), array('colorName' => 'green'));

// display confirm message
$choice = $terminalor->getResponse()
    ->confirm('Do you like beer ?');

// display promt message  with custom promt sign
$response = $terminalor->getResponse()->promt('How are you',
    array('colorName' => 'green'), 'Your answer: ');