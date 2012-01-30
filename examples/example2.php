<?php
/**
 * Example showing how to implement iteraction between defined commands
 *
 * @author      Bernard Baltrusaitis <bernard@runawaylover.info>
 * @package     Terminalor
 * @link        http://terminalor.runawaylover.info
 */

require dirname(__FILE__). '/../library/terminalor_required.php';

/* @var $terminalor Terminalor_Application_Interface */
$terminalor = Terminalor_Container::getInstance()
    ->get('Terminalor.Application');

$terminalor->getResponse()
    ->getStyle()->setDefaultColor('green');

$terminalor['index'] =
/**
 * Users management utility
 * 
 * @version 1.0
 * @author Bernard Baltrusaitis <bernard@runawaylover.info>
 */
function(Terminalor_Application_Interface $terminalor) {
    while(true) {
        $choice = $terminalor->getResponse()
            ->promt('Available commands: (s)how users, (a)dd user, (q)uit');

        switch ($choice) {
            case 'a':
                $terminalor['adduser']($terminalor);
            break;
            case 's':
                $terminalor['showusers']($terminalor);
            break;
            case 'q':
                $terminalor->getResponse()->message('Bye');
                break 2;
            break;
            default :
                $terminalor->getResponse()->message(
                    sprintf('Invalid menu option `%s`', $choice), 'error');
            break;
        }
    }
};

/**
 * Show table of existing users
 *
 * @author Bernard Baltrusaitis <bernard@runawaylover.info>
 */
$terminalor['showusers'] = function(Terminalor_Application_Interface $terminalor) {
    $filename = dirname(__FILE__).'/users.json';
    $users    = json_decode(file_get_contents($filename), true);
    $terminalor->getResponse()->message($users, null, function($v){
        return ucfirst($v);
    });
};

/**
 * Add new user
 *
 * @author Bernard Baltrusaitis <bernard@runawaylover.info>
 */
$terminalor['adduser'] = function(Terminalor_Application_Interface $terminalor) {
    $filename = dirname(__FILE__).'/users.json';
    $users    = json_decode(file_get_contents($filename), true);
    $users[]  = array(
        'id'    => count($users)+1,
        'name'  => $terminalor->getResponse()->promt('User name:'),
        'email' => $terminalor->getResponse()->promt('User email:'));

    file_put_contents($filename, json_encode($users));
    $terminalor->getResponse()->message('User has been created', 'success');
};

$terminalor->__toString();