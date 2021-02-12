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

namespace osWMensch\Server;

class DB {

	/**
	 * Speichert alle Verbindungen.
	 *
	 * @var array
	 */
	public static array $connections=[];

	/**
	 * DB constructor.
	 */
	private function __construct() {

	}

	/**
	 * Verbindet die Datenbank.
	 *
	 * @param string $alias
	 * @return bool|null
	 */
	public static function connect(string $alias='default'):?bool {
		if (!isset(self::$connections[$alias])) {
			return null;
		}
		if (!isset(self::$connections[$alias]['con'])||!is_object(self::$connections[$alias]['con'])) {
			try {
				self::$connections[$alias]['con']=new \PDO(self::$connections[$alias]['dns'], self::$connections[$alias]['user'], self::$connections[$alias]['password']);
			} catch (\PDOException $e) {
				/* TODO: Message */
				echo 'Connection failed: '.$e->getMessage();

				return false;
			}
		}

		return true;
	}

	/**
	 * Fügt eine mySQL-Datenbankverbindung hinzu.
	 *
	 * @param string $host
	 * @param string $user
	 * @param string $password
	 * @param string $dbname
	 * @param string $charset
	 * @param string $alias
	 * @return bool
	 */
	public static function addConnectionMYSQL(string $host, string $user, string $password, string $dbname, string $charset='utf8', string $alias='default'):bool {
		return self::addConnection('mysql:host='.$host.';dbname='.$dbname.';charset='.$charset, $user, $password, $alias);
	}

	/**
	 * Fügt eine Datenbankverbindung hinzu.
	 *
	 * @param string $dns
	 * @param string $user
	 * @param string $password
	 * @param string $alias
	 * @return bool
	 */
	public static function addConnection(string $dns, string $user, string $password, string $alias='default'):bool {
		self::$connections[$alias]=['connected'=>false, 'dns'=>$dns, 'user'=>$user, 'password'=>$password, 'con'=>null];

		return true;
	}

	/**
	 * Gibt die Verbindung zur Datenbank zurück
	 *
	 * @param string $alias
	 * @return object|null
	 */
	public static function getConnection(string $alias='default'):?object {
		if (!isset(self::$connections[$alias])) {
			return null;
		}

		return self::$connections[$alias]['con'];
	}

}

?>