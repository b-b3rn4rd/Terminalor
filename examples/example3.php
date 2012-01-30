<?php
/**
 * Example showing how to specify predefined argument value using router object
 * and change default command name
 *
 * @author      Bernard Baltrusaitis <bernard@runawaylover.info>
 * @package     Terminalor
 * @example     example5.php How to build this file
 * @link        http://terminalor.runawaylover.info
 */

require_once dirname(__FILE__). '/../library/terminalor_required.php';
require_once '/var/www/lendline/library/Swift/swift_required.php';

/* @var $terminalor Terminalor_Application_Interface */
$terminalor = Terminalor_Container::getInstance()
    ->get('Terminalor.Application');

$terminalor->getRequest()
    ->setArgument('title', 'This is default title') // set def. value
    ->getRouter()
    ->setDefaultCommandName('sendmail'); // set def. command name

/**
 * Send email using swift mailer with given title and body, title argument 
 * has predefined value thus can be omitted
 * 
 * @param string $title email title
 * @param string $body email body
 */
$terminalor['sendmail'] = function(Terminalor_Application_Interface $terminalor, $title, $body){
    $transport = Swift_SmtpTransport::newInstance('mail.ntaa.com.au');
    $mailer    = Swift_Mailer::newInstance($transport);
    $message   = Swift_Message::newInstance($title)
        ->setFrom(array('bernard.baltrusaitis@interprac.com.au' => 'John Doh'))
        ->setTo(array('bernard@runawaylover.info'))
        ->setBody($body);

    $result = $mailer->send($message);
};

$terminalor->__toString();