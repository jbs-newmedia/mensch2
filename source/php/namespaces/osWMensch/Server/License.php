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

class License {

	use BaseConnectionTrait;

	/**
	 * @var array
	 */
	private array $license_list=[];

	/**
	 * @var array
	 */
	private array $package_list=[];

	/**
	 * @var array
	 */
	private array $license_package_list=[];

	/**
	 * @var array
	 */
	private array $package_license_list=[];

	/**
	 * License constructor.
	 */
	public function __construct() {

	}

	/**
	 * @return array
	 */
	public function getLicenseList():array {
		$this->license_list=[];
		$QgetData=self::getConnection();
		$QgetData->prepare('SELECT * FROM :table: WHERE 1 ORDER BY license_name ASC');
		$QgetData->bindTable(':table:', 'mensch_license');
		foreach ($QgetData->query() as $license) {
			$this->license_list[$license['license_id']]=$license;
		}

		return $this->license_list;
	}

	/**
	 * @param int $license_id
	 * @return array
	 */
	public function getLicenseDetails(int $license_id):array {
		if ($this->license_list==[]) {
			$this->getLicenseList();
		}

		if (isset($this->license_list[$license_id])) {
			return $this->license_list[$license_id];
		}

		return [];
	}

	/**
	 * @return array
	 */
	public function getPackageList():array {
		$file=Configure::getValueAsString('mensch_path').'data'.DIRECTORY_SEPARATOR.'server.packages';
		if ($this->package_list==[]) {
			$package_list=explode(',', file_get_contents($file));

			foreach ($package_list as $package) {
				$this->package_list[md5($package)]=$package;
			}
		}

		return $this->package_list;
	}

	/**
	 * @return array
	 */
	public function getLicensePackageList(int $license_id):array {
		if ($this->license_package_list==[]) {
			$QgetData=self::getConnection();
			$QgetData->prepare('SELECT * FROM :table: WHERE license_id=:license_id: ORDER BY license2package_package ASC');
			$QgetData->bindTable(':table:', 'mensch_license2package');
			$QgetData->bindInt(':license_id:', $license_id);
			foreach ($QgetData->query() as $package) {
				$this->license_package_list[$package['license2package_id']]=$package['license2package_package'];
			}
		}

		return $this->license_package_list;
	}

	/**
	 * @return array
	 */
	public function getPackageLicenseList():array {
		if ($this->license_list==[]) {
			$this->getLicenseList();
		}

		if ($this->getLicenseList()!=[]) {
			if ($this->package_license_list==[]) {
				foreach ($this->getLicenseList() as $license) {
					$QgetData=self::getConnection();
					$QgetData->prepare('SELECT * FROM :table: WHERE license_id=:license_id: ORDER BY license2package_package ASC');
					$QgetData->bindTable(':table:', 'mensch_license2package');
					$QgetData->bindInt(':license_id:', $license['license_id']);
					foreach ($QgetData->query() as $package) {
						if (!isset($this->package_license_list[$package['license2package_package']])) {
							$this->package_license_list[$package['license2package_package']]=[];
						}
						$this->package_license_list[$package['license2package_package']][]=$license['license_key'];
					}
				}
			}
		}

		return $this->package_license_list;
	}

	/**
	 * @param int $license_id
	 * @param string $package
	 * @return bool
	 */
	public function addPackage2License(int $license_id, string $package):bool {
		$QgetData=self::getConnection();
		$QgetData->prepare('SELECT license2package_id FROM :table: WHERE license_id=:license_id: and license2package_package=:license2package_package:');
		$QgetData->bindTable(':table:', 'mensch_license2package');
		$QgetData->bindInt(':license_id:', $license_id);
		$QgetData->bindString(':license2package_package:', $package);
		$QgetData->execute();
		if ($QgetData->rowCount()==0) {
			$QinsertData=self::getConnection();
			$QinsertData->prepare('INSERT INTO :table: (license_id, license2package_package, license2package_create_time, license2package_create_user_id, license2package_update_time, license2package_update_user_id) VALUES(:license_id:, :license2package_package:, :license2package_create_time:, :license2package_create_user_id:, :license2package_update_time:, :license2package_update_user_id:)');
			$QinsertData->bindTable(':table:', 'mensch_license2package');
			$QinsertData->bindInt(':license_id:', $license_id);
			$QinsertData->bindString(':license2package_package:', $package);
			$QinsertData->bindInt(':license2package_create_time:', time());
			$QinsertData->bindInt(':license2package_create_user_id:', 0);
			$QinsertData->bindInt(':license2package_update_time:', time());
			$QinsertData->bindInt(':license2package_update_user_id:', 0);
			$QinsertData->execute();

			return true;
		}

		return false;
	}

	/**
	 * @param int $license_id
	 * @param string $package
	 * @return bool
	 */
	public function removePackage2License(int $license_id, string $package):bool {
		$QgetData=self::getConnection();
		$QgetData->prepare('SELECT license2package_id FROM :table: WHERE license_id=:license_id: and license2package_package=:license2package_package:');
		$QgetData->bindTable(':table:', 'mensch_license2package');
		$QgetData->bindInt(':license_id:', $license_id);
		$QgetData->bindString(':license2package_package:', $package);
		$QgetData->execute();
		if ($QgetData->rowCount()==1) {
			$result=$QgetData->fetch();
			$QdeleteData=self::getConnection();
			$QdeleteData->prepare('DELETE FROM :table: WHERE license2package_id=:license2package_id:');
			$QdeleteData->bindTable(':table:', 'mensch_license2package');
			$QdeleteData->bindInt(':license2package_id:', $result['license2package_id']);
			$QdeleteData->execute();

			return true;
		}

		return false;
	}

	/**
	 * @param string $license_name
	 * @param string $license_description
	 * @param string $license_server_name
	 * @param string $license_server_address
	 * @param string $license_server_mac
	 * @param string $license_key
	 * @param int $license_status
	 * @return bool
	 */
	public static function createLicense(string $license_name, string $license_description, string $license_server_name, string $license_server_address, string $license_server_mac, string $license_key, int $license_status):bool {
		$QinsertData=self::getConnection();
		$QinsertData->prepare('INSERT INTO :table: (license_name, license_description, license_server_name, license_server_address, license_server_mac, license_key, license_status, license_create_time, license_create_user_id, license_update_time, license_update_user_id) VALUES(:license_name:, :license_description:, :license_server_name:, :license_server_address:, :license_server_mac:, :license_key:, :license_status:, :license_create_time:, :license_create_user_id:, :license_update_time:, :license_update_user_id:)');
		$QinsertData->bindTable(':table:', 'mensch_license');
		$QinsertData->bindString(':license_name:', $license_name);
		$QinsertData->bindString(':license_description:', $license_description);
		$QinsertData->bindString(':license_server_name:', $license_server_name);
		$QinsertData->bindString(':license_server_address:', $license_server_address);
		$QinsertData->bindString(':license_server_mac:', $license_server_mac);
		$QinsertData->bindString(':license_key:', $license_key);
		$QinsertData->bindInt(':license_status:', $license_status);
		$QinsertData->bindInt(':license_create_time:', time());
		$QinsertData->bindInt(':license_create_user_id:', 0);
		$QinsertData->bindInt(':license_update_time:', time());
		$QinsertData->bindInt(':license_update_user_id:', 0);
		$QinsertData->execute();

		return true;
	}

	/**
	 * @param int $license_id
	 * @param string $license_name
	 * @param string $license_description
	 * @param string $license_server_name
	 * @param string $license_server_address
	 * @param string $license_server_mac
	 * @param string $license_key
	 * @param int $license_status
	 * @return bool
	 */
	public static function updateLicense(int $license_id, string $license_name, string $license_description, string $license_server_name, string $license_server_address, string $license_server_mac, string $license_key, int $license_status):bool {
		$QupdateData=self::getConnection();
		$QupdateData->prepare('UPDATE :table: SET license_name=:license_name:, license_description=:license_description:, license_server_name=:license_server_name:, license_server_address=:license_server_address:, license_server_mac=:license_server_mac:, license_key=:license_key:, license_status=:license_status:, license_update_time=:license_update_time:, license_update_user_id=:license_update_user_id: WHERE license_id=:license_id:');
		$QupdateData->bindTable(':table:', 'mensch_license');
		$QupdateData->bindString(':license_name:', $license_name);
		$QupdateData->bindString(':license_description:', $license_description);
		$QupdateData->bindString(':license_server_name:', $license_server_name);
		$QupdateData->bindString(':license_server_address:', $license_server_address);
		$QupdateData->bindString(':license_server_mac:', $license_server_mac);
		$QupdateData->bindString(':license_key:', $license_key);
		$QupdateData->bindInt(':license_status:', $license_status);
		$QupdateData->bindInt(':license_update_time:', time());
		$QupdateData->bindInt(':license_update_user_id:', 0);
		$QupdateData->bindInt(':license_id:', $license_id);
		$QupdateData->execute();

		return true;
	}

	/**
	 * @param int $license_id
	 * @return bool
	 */
	public static function deleteLicense(int $license_id):bool {
		$QupdateData=self::getConnection();
		$QupdateData->prepare('DELETE FROM :table: WHERE license_id=:license_id:');
		$QupdateData->bindTable(':table:', 'mensch_license');
		$QupdateData->bindInt(':license_id:', $license_id);
		$QupdateData->execute();

		return true;
	}

	/**
	 * @return bool
	 */
	public function clearLicensePackages():bool {
		$file=Configure::getValueAsString('mensch_path').'data'.DIRECTORY_SEPARATOR.'server.packages';
		$package_list=explode(',', file_get_contents($file));

		$QdeleteData=self::getConnection();
		$QdeleteData->prepare('DELETE FROM :table: WHERE license2package_package NOT IN (:license2package_package:)');
		$QdeleteData->bindTable(':table:', 'mensch_license2package');
		$QdeleteData->bindRaw(':license2package_package:', '\''.implode('\',\'', $package_list).'\'');
		$QdeleteData->execute();

		return true;
	}

}

?>