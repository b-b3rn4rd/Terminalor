<?php
/**
 * Example showing how to build portable file.
 * As an example file example1.php will be used.
 *
 * @author      Bernard Baltrusaitis <bernard@runawaylover.info>
 * @package     Terminalor
 * @example     example1.php This build source
 * @example     example5.php How to build more complex files
 * @link        http://terminalor.runawaylover.info
 */
include dirname(__FILE__).'/../library/Terminalor/Builder.php';

$filename = dirname(__FILE__).'/example1.php';
$build = new Terminalor_Builder($filename);
$build->build();