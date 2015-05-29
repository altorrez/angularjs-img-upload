<?php 

// Simpliest autoloader ever
// transforms namespace to filestructure for easy loading
function __autoload($class_name) {

	$core_file = ROOT.DS.str_replace('\\', DS, $class_name).'.php';
	require $core_file;
}