<?php
/**
 * Example showing how to build portable file based on library which uses ioc.
 * 
 * As an example file example3.php will be used.
 *
 * @author      Bernard Baltrusaitis <bernard@runawaylover.info>
 * @package     Terminalor
 * @example     example3.php this build source
 * @link        http://terminalor.runawaylover.info
 */
include dirname(__FILE__).'/../library/Terminalor/Builder.php';

// specify source file
$filename = dirname(__FILE__).'/example3.php';
$build = new Terminalor_Builder($filename);

// set Swift namespace
$build->addIncludeClassPattern('/^Swift/');

// include induvidual file
$build->includeFileForBuild('Swift/swift_required.php');
$build->includeFileForBuild('Swift/mime_types.php');

// include all files from given directory
$path = 'Swift/dependency_maps';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
$build->includeDirectoryForBuild($iterator);
$build->includeFileForBuild('Swift/preferences.php');

// build with custom atrributes
$build->build(array(
    'sendmail'  => array(
        'title' => 'this is test title',
        'body'  => 'this is test body'
    )
));