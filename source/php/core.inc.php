<?php

/**
 * This file is part of the Mensch2 package
 *
 * @author Juergen Schwind
 * @copyright Copyright (c) JBS New Media GmbH - Juergen Schwind (https://jbs-newmedia.com)
 * @package Mensch2
 * @link https://oswframe.com
 * @license MIT License
 */

/**
 * Zeitzone setzen
 */
date_default_timezone_set($configure['mensch_timezone']);

/**
 * Schutz
 */
if ((isset($configure['htuser']))&&($configure['htuser']!='')&&(isset($configure['htpass']))&&($configure['htpass']!='')) {
	if (((!isset($_SERVER['PHP_AUTH_USER']))||($_SERVER['PHP_AUTH_USER']!=$configure['htuser']))||((!isset($_SERVER['PHP_AUTH_PW']))||($_SERVER['PHP_AUTH_PW']!=$configure['htpass']))) {
		if ((isset($_SERVER['PHP_AUTH_USER']))&&(isset($_SERVER['PHP_AUTH_PW']))) {
			if (($_SERVER['PHP_AUTH_USER']!=$configure['htuser'])||($_SERVER['PHP_AUTH_PW']!=$configure['htpass'])) {
				header('WWW-Authenticate: Basic realm="mensch² protection"');
				header('HTTP/1.0 401 Unauthorized');
				die('blocked');
			}
		} else {
			header('WWW-Authenticate: Basic realm=mensch² protection"');
			header('HTTP/1.0 401 Unauthorized');
			die('blocked');
		}
	}
}

/**
 * Autoloader für Namespaces
 */
spl_autoload_register(function($className) {
	static $oswmensch_core_namespace_path=null;

	if ($oswmensch_core_namespace_path===null) {
		$oswmensch_core_namespace_path=realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR.'namespaces'.DIRECTORY_SEPARATOR;
	}

	$filename=str_replace('\\', DIRECTORY_SEPARATOR, $className).'.php';
	$fullpath=$oswmensch_core_namespace_path.$filename;

	if (file_exists($fullpath)) {
		require_once $fullpath;
	}
});

/**
 * UTF8 Header senden
 */
header('Content-Type: text/html; charset=utf-8');
$configure['mensch_update']=\osWMensch\Server\Core::getCurrentTag();

/**
 * Konfiguration setzen
 */
\osWMensch\Server\Configure::setByArray($configure);

/**
 * Mensch-Core Objekt erstellen
 */
$Core=new \osWMensch\Server\Core();

/**
 * Aktuelle Seite setzen und validieren
 */
if (isset($_POST['page'])) {
	$Core->setPage($_POST['page']);
} elseif (isset($_GET['page'])) {
	$Core->setPage($_GET['page']);
} else {
	$Core->setPage('');
}

/**
 * Url prüfen
 */
if (\osWMensch\Server\Configure::getValueAsString('mensch_url').'/'!=($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/')) {
	$url=\osWMensch\Server\Configure::getValueAsString('mensch_url').\osWMensch\Server\Configure::getValueAsString('mensch_url_path').str_replace(\osWMensch\Server\Configure::getValueAsString('mensch_url_path'), '', $_SERVER['REQUEST_URI']);
	header('Location: '.$url);
}

/**
 * Datenbank verbinden
 */
\osWMensch\Server\DB::addConnectionMYSQL(\osWMensch\Server\Configure::getValueAsString('mysql_server'), \osWMensch\Server\Configure::getValueAsString('mysql_user'), \osWMensch\Server\Configure::getValueAsString('mysql_password'), \osWMensch\Server\Configure::getValueAsString('mysql_database'), \osWMensch\Server\Configure::getValueAsString('mysql_character'));
if (\osWMensch\Server\DB::connect()===false) {
	die('database is currently not configured. '.\osWMensch\Server\DB::getErrorMessage());
}

?>