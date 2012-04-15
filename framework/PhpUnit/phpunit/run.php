<?php


define('PHPUnit_MAIN_METHOD','PHPUnit_TextUI_Command::main');

// add phpunit to the include path
$paths = scandir('../');
$includes = array();
foreach($paths as $path){
    if (!preg_match('/^\./', $path)){
        $includes[] = '../' . $path . '/';
    }
}
set_include_path(implode(PATH_SEPARATOR,$includes).PATH_SEPARATOR.get_include_path());

//print_r($includes); die();
// set the auto loader
require 'PHPUnit/Autoload.php';

// execute
PHPUnit_TextUI_Command::main();
