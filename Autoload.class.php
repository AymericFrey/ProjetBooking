<?php
/******************************************************************
* \Autoload des classes modeles/
*******************************************************************/
defined("_nova_district_token_") or die('');

class Autoload {
	private static $base = "models/";
	
	public static function loader($class) {
		$file = Autoload::$base.str_replace('\\', '/', $class) . '.class.php';
		//echo '-> Trying to load ', $file, ' via ', __METHOD__, "()\n";
        require($file);
	}
}

//spl_autoload_register(function($class) {include $class . '.php'; });
spl_autoload_register(array('Autoload', 'loader'));

?>