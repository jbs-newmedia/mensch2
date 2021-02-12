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

trait BaseConnectionTrait {

	/**
	 * @var object|null
	 */
	private static ?object $connection=null;

	/**
	 * @param string $alias
	 * @return object
	 */
	public static function getConnection($alias='default'):object {
		if (self::$connection===null) {
			self::$connection=new Database($alias);
		}

		return self::$connection;
	}

}

?>