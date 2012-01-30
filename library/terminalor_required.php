<?php
/*
 * This file is part of Terminalor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * File responsible for installing default dependencies, include THIS file BEFORE
 * your commands
 * <code>
 * include 'terminalor_required.php';
 * /* @var $terminalor Terminalor_Application_Interface *\/
 * $terminalor = Terminalor_Container::getInstance()
 *      ->get('Terminalor.Application');
 * </code>
 *
 * @author      Bernard Baltrusaitis <bernard@runawaylover.info>
 * @package     Terminalor
 * @link        http://terminalor.runawaylover.info
 */
require_once dirname(__FILE__).'/Terminalor/Application/Interface.php';
require_once dirname(__FILE__).'/Terminalor/Application/Abstract.php';
require_once dirname(__FILE__).'/Terminalor/Application.php';

Terminalor_Application::autoloadRegister();
Terminalor_Container::getInstance()
    ->setDefaultDependencies();
