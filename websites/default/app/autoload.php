<?php
function autoload($className) {
	$fileName = str_replace('\\', '/', $className) . '.php';
	$file = '../app/' . $fileName;

	require $file;
}
spl_autoload_register('autoload');