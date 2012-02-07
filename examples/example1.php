<?php
/**
 * Example showing how to send emails using Zend_Mail library from
 * php cli interface. 
 *
 * @author      Bernard Baltrusaitis <bernard@runawaylover.info>
 * @package     Terminalor
 * @example     example6.php How to build this file
 * @example     example5.php How to build more complex files
 * @link        http://terminalor.runawaylover.info
 */

require_once dirname(__FILE__). '/../library/terminalor_required.php';
require_once 'Zend/Mail/Transport/Smtp.php';
require_once 'Zend/Mail.php';

/* @var $terminalor Terminalor_Application_Interface */
$terminalor = Terminalor_Container::getInstance()
    ->get('Terminalor.Application');

$terminalor['index'] =
/**
 * Sends email with given attributes using gmail
 * 
 * @author Bernard Baltrusaitis <bernard@runawaylover.info>
 * @version 1.0
 * @param string $title email subject
 * @param string $to email to
 * @param string $body email body
 */
function(Terminalor_Application_Interface $terminalor, $title, $body, $to = 'john.doh@example.com') {
    $transport = new Zend_Mail_Transport_Smtp('smtp.gmail.com', array(
        'ssl'      => 'ssl',
        'port'     => '465',
        'auth'     => 'login',
        'username' => '******@gmail.com',
        'password' => '******' ));
        
    $mail = new Zend_Mail();
    $mail->setFrom('john.doh@example.com', 'John Doh');
    $mail->addTo($to);
    $mail->setSubject($title);
    $mail->setBodyText($body);
    $mail->send($transport);

    $terminalor->getResponse()->message(sprintf('Email to `%s` has been sent', $to),
        'success');
};

$terminalor['test'] = function (Terminalor_Application_Interface $terminalor) {
    $a = $terminalor->getResponse()->confirm('ok');
    var_dump($a);
};

$terminalor->__toString();