<?php

/**
 * This file is part of the Mensch2 package
 *
 * @author Juergen Schwind
 * @copyright Copyright (c) JBS New Media GmbH - Juergen Schwind (https://jbs-newmedia.com)
 * @package Mensch2
 * @link https://oswframe.com
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU General Public License 3
 */

/**
 * Zeitzone setzen
 */
date_default_timezone_set($configure['mensch_timezone']);

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
if (\osWMensch\Server\Configure::getValueAsString('mensch_url')!=($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/')) {
	header('Location: '.str_replace('//', '/', \osWMensch\Server\Configure::getValueAsString('mensch_url').$_SERVER['REQUEST_URI']));
}

/**
 * Datenbank verbinden
 */
\osWMensch\Server\DB::addConnectionMYSQL(\osWMensch\Server\Configure::getValueAsString('mysql_server'), \osWMensch\Server\Configure::getValueAsString('mysql_user'), \osWMensch\Server\Configure::getValueAsString('mysql_password'), \osWMensch\Server\Configure::getValueAsString('mysql_database'), \osWMensch\Server\Configure::getValueAsString('mysql_character'));
\osWMensch\Server\DB::connect();

?>