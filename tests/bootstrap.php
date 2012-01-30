<?php
spl_autoload_register(function($class){
    $filename  = sprintf('%s/../library/%s.php',
        dirname(__FILE__), 
        str_replace('_', '/', $class));
        
    if (is_file($filename)) {
        include $filename;
    }
});
