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

class Configure {

	/**
	 * @var array
	 */
	static array $configure=[];

	/**
	 * Seed für den Zufallsgenerator
	 */
	private static $seeded=false;

	/**
	 * Core constructor.
	 */
	private function __construct() {

	}

	/**
	 * @param array $configure
	 * @return bool
	 */
	public static function setByArray(array $configure):bool {
		self::$configure=$configure;

		return true;

	}

	/**
	 * @param string $key
	 * @return int
	 */
	public static function getValueAsInt(string $key):int {
		if (isset(self::$configure[$key])) {
			return intval(self::$configure[$key]);
		}

		return 0;
	}

	/**
	 * @param string $key
	 * @return string
	 */
	public static function getValueAsString(string $key):string {
		if (isset(self::$configure[$key])) {
			return strval(self::$configure[$key]);
		}

		return '';
	}

	/**
	 * @param string $key
	 * @return array
	 */
	public static function getValueAsArray(string $key):array {
		if (isset(self::$configure[$key])) {
			return self::$configure[$key];
		}

		return [];
	}

	/**
	 *
	 * @param string $string
	 * @param string $algo
	 * @param string $salt_length
	 * @return string
	 */
	public static function encryptString(string $string, string $algo='sha512', int $salt_length=6) {
		$password='';
		for ($i=0; $i<($salt_length*3); $i++) {
			$password.=self::randomInt(0, 9);
		}
		if (!in_array($algo, ['md5', 'sha1', 'sha256', 'sha384', 'sha512', 'ripemd128', 'ripemd160', 'ripemd256', 'ripemd320', 'whirlpool'])) {
			$algo='sha512';
		}
		$salt=substr(hash($algo, $password), 0, $salt_length);
		$password=hash($algo, $salt.$string).':'.$salt;

		return $password;
	}

	/**
	 * Gibt einen ganzzahligen Zufallswert zurück.
	 *
	 * @param int $min Linker Rand
	 * @param int $max Rechter Rand
	 * @return int Zufallswert
	 */
	public static function randomInt(int $min, int $max):int {
		if (self::$seeded==false) {
			mt_srand(self::makeSeed());
			self::$seeded=true;
		}

		return mt_rand($min, $max);
	}

	/**
	 *
	 * @return float Zufallswert für Initialisierung des Zufallsgenerators.
	 */
	private static function makeSeed():float {
		return microtime(true)*1000000;
	}

	/**
	 * @param string $url
	 * @return bool
	 */
	public static function verifyUrlIDNAPattern(string $url):bool {
		if (filter_var(idn_to_ascii($url), FILTER_VALIDATE_URL)) {
			return true;
		}

		return false;
	}

	/**
	 * @param $hash
	 * @return bool
	 */
	public static function verifyHash($hash):bool {

		return boolval(preg_match('/^[a-zA-Z0-9]{32}$/', $hash));
	}

	/**
	 * @param $dir
	 * @return bool
	 */
	public static function makeDir($dir) {
		clearstatcache();

		$_dir=explode(DIRECTORY_SEPARATOR, $dir);
		unset($_dir[count($_dir)]);
		unset($_dir[0]);
		$dir=DIRECTORY_SEPARATOR.implode(DIRECTORY_SEPARATOR, $_dir);

		if (!is_dir($dir)) {
			$path=DIRECTORY_SEPARATOR;
			$i=0;
			foreach ($_dir as $dir) {
				$i++;
				if ($i>3) {
					if (!is_dir($path)) {
						mkdir($path);
						chmod($path, self::getValueAsInt('chmod_dir'));
					}
				}
				clearstatcache();
				$path.=$dir.DIRECTORY_SEPARATOR;
			}
			clearstatcache();
		}

		return true;
	}

	/**
	 * @source https://www.php.net/manual/de/function.copy.php#91010
	 * @param string $src
	 * @param string $dst
	 * @return bool
	 */
	public static function copyRecursive(string $src, string $dst):bool {
		$dir=opendir($src);
		@mkdir($dst);
		while (false!==($file=readdir($dir))) {
			if (($file!='.')&&($file!='..')) {
				if (is_dir($src.'/'.$file)) {
					self::copyRecursive($src.'/'.$file, $dst.'/'.$file);
				} else {
					copy($src.'/'.$file, $dst.'/'.$file);
				}
			}
		}
		closedir($dir);
		return true;
	}

}

?>