<?php
require_once 'vendor/autoload.php';

if (!getenv('PDO_DB_DSN')) {
    putenv('PDO_DB_DSN=sqlite::memory:');
}

if (!function_exists('dd')) {
	function dd($var) {
		var_export($var);
		die();
	}
}
