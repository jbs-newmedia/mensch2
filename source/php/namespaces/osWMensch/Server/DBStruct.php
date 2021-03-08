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

class DBStruct {

	use BaseConnectionTrait;

	/**
	 * DBStruct constructor.
	 */
	public function __construct() {

	}

	/**
	 * @return bool
	 */
	public function checkTables():bool {
		$error=true;
		if ($this->checkTableServer()!==true) {
			$error=false;
		}
		if ($this->checkTableLicense()!==true) {
			$error=false;
		}
		if ($this->checkTableLicense2Package()!==true) {
			$error=false;
		}

		return $error;
	}

	/**
	 * @return bool
	 */
	private function checkTableServer():bool {
		$__datatable_table='mensch_server';
		$__datatable_create=false;
		$__datatable_do=false;

		$QreadData=self::getConnection();
		$QreadData->prepare('SHOW TABLE STATUS LIKE :table:');
		$QreadData->bindString(':table:', \osWMensch\Server\Configure::getValueAsString('mysql_prefix').$__datatable_table);
		$QreadData->execute();
		if ($QreadData->rowCount()==1) {
			$result=$QreadData->fetch();
			$avb_tbl=$result['Comment'];
		} else {
			$avb_tbl='0.0';
		}
		$avb_tbl=explode('.', $avb_tbl);
		if (count($avb_tbl)==1) {
			$av_tbl=intval($avb_tbl[0]);
			$ab_tbl=0;
		} elseif (count($avb_tbl)==2) {
			$av_tbl=intval($avb_tbl[0]);
			$ab_tbl=intval($avb_tbl[1]);
		} else {
			$av_tbl=0;
			$ab_tbl=0;
		}
		if (($av_tbl==0)&&($ab_tbl==0)) {
			$av_tbl=1;
			$ab_tbl=0;
			$__datatable_create=true;

			$QwriteData=self::getConnection();
			$QwriteData->prepare('
CREATE TABLE :table: (
	server_id int(11) unsigned NOT NULL AUTO_INCREMENT,
	server_name varchar(64) NOT NULL DEFAULT \'\',
	server_version varchar(16) NOT NULL DEFAULT \'\',
	server_rank int(11) UNSIGNED NOT NULL DEFAULT 0,
	server_url varchar(64) NOT NULL DEFAULT \'\',
	server_file varchar(32) NOT NULL DEFAULT \'\',
	server_secure varchar(32) NOT NULL DEFAULT \'\',
	server_token varchar(32) NOT NULL DEFAULT \'\',
	server_status tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
	server_lastconnect int(11) UNSIGNED NOT NULL DEFAULT 0,
	server_create_time int(11) UNSIGNED NOT NULL DEFAULT 0,
	server_create_user_id int(11) UNSIGNED NOT NULL DEFAULT 0,
	server_update_time int(11) UNSIGNED NOT NULL DEFAULT 0,
	server_update_user_id int(11) UNSIGNED NOT NULL DEFAULT 0,
	PRIMARY KEY (server_id),
	KEY server_rank (server_rank),
	KEY server_status (server_status),
	KEY server_lastconnect (server_lastconnect),
	KEY server_create_time (server_create_time),
	KEY server_create_user_id (server_create_user_id),
	KEY server_update_time (server_update_time),
	KEY server_update_user_id (server_update_user_id)
) ENGINE='.\osWMensch\Server\Configure::getValueAsString('mysql_engine').' DEFAULT CHARSET='.\osWMensch\Server\Configure::getValueAsString('mysql_character').' COMMENT=:version:
');
			$QwriteData->bindTable(':table:', $__datatable_table);
			$QwriteData->bindString(':version:', $av_tbl.'.'.$ab_tbl);
			if ($QwriteData->execute()===null) {
				return false;
			}
		}

		if ($__datatable_do===true) {
			$QwriteData=self::getConnection();
			$QwriteData->prepare('ALTER TABLE :table: COMMENT = :version:;');
			$QwriteData->bindTable(':table:', $__datatable_table);
			$QwriteData->bindString(':version:', $av_tbl.'.'.$ab_tbl);
			$QwriteData->execute();
			if ($QwriteData->execute()===null) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @return bool
	 */
	private function checkTableLicense():bool {
		$__datatable_table='mensch_license';
		$__datatable_create=false;
		$__datatable_do=false;

		$QreadData=self::getConnection();
		$QreadData->prepare('SHOW TABLE STATUS LIKE :table:');
		$QreadData->bindString(':table:', \osWMensch\Server\Configure::getValueAsString('mysql_prefix').$__datatable_table);
		$QreadData->execute();
		if ($QreadData->rowCount()==1) {
			$result=$QreadData->fetch();
			$avb_tbl=$result['Comment'];
		} else {
			$avb_tbl='0.0';
		}
		$avb_tbl=explode('.', $avb_tbl);
		if (count($avb_tbl)==1) {
			$av_tbl=intval($avb_tbl[0]);
			$ab_tbl=0;
		} elseif (count($avb_tbl)==2) {
			$av_tbl=intval($avb_tbl[0]);
			$ab_tbl=intval($avb_tbl[1]);
		} else {
			$av_tbl=0;
			$ab_tbl=0;
		}
		if (($av_tbl==0)&&($ab_tbl==0)) {
			$av_tbl=1;
			$ab_tbl=0;
			$__datatable_create=true;

			$QwriteData=self::getConnection();
			$QwriteData->prepare('
CREATE TABLE :table: (
	license_id int(11) unsigned NOT NULL AUTO_INCREMENT,
	license_name varchar(64) NOT NULL DEFAULT \'\',
	license_description varchar(128) NOT NULL DEFAULT \'\',
	license_server_name varchar(128) NOT NULL DEFAULT \'\',
	license_server_address varchar(128) NOT NULL DEFAULT \'\',
	license_server_mac varchar(128) NOT NULL DEFAULT \'\',
	license_key varchar(128) NOT NULL DEFAULT \'\',
	license_status tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
	license_create_time int(11) UNSIGNED NOT NULL DEFAULT 0,
	license_create_user_id int(11) UNSIGNED NOT NULL DEFAULT 0,
	license_update_time int(11) UNSIGNED NOT NULL DEFAULT 0,
	license_update_user_id int(11) UNSIGNED NOT NULL DEFAULT 0,
	PRIMARY KEY (license_id),
	KEY license_key (license_key),
	KEY license_status (license_status),
	KEY license_create_time (license_create_time),
	KEY license_create_user_id (license_create_user_id),
	KEY license_update_time (license_update_time),
	KEY license_update_user_id (license_update_user_id)
) ENGINE='.\osWMensch\Server\Configure::getValueAsString('mysql_engine').' DEFAULT CHARSET='.\osWMensch\Server\Configure::getValueAsString('mysql_character').' COMMENT=:version:
');
			$QwriteData->bindTable(':table:', $__datatable_table);
			$QwriteData->bindString(':version:', $av_tbl.'.'.$ab_tbl);
			if ($QwriteData->execute()===null) {
				return false;
			}
		}

		if ($__datatable_do===true) {
			$QwriteData=self::getConnection();
			$QwriteData->prepare('ALTER TABLE :table: COMMENT = :version:;');
			$QwriteData->bindTable(':table:', $__datatable_table);
			$QwriteData->bindString(':version:', $av_tbl.'.'.$ab_tbl);
			$QwriteData->execute();
			if ($QwriteData->execute()===null) {
				return false;
			}
		}

		return true;
	}

	/**
	 * @return bool
	 */
	private function checkTableLicense2Package():bool {
		$__datatable_table='mensch_license2package';
		$__datatable_create=false;
		$__datatable_do=false;

		$QreadData=self::getConnection();
		$QreadData->prepare('SHOW TABLE STATUS LIKE :table:');
		$QreadData->bindString(':table:', \osWMensch\Server\Configure::getValueAsString('mysql_prefix').$__datatable_table);
		$QreadData->execute();
		if ($QreadData->rowCount()==1) {
			$result=$QreadData->fetch();
			$avb_tbl=$result['Comment'];
		} else {
			$avb_tbl='0.0';
		}
		$avb_tbl=explode('.', $avb_tbl);
		if (count($avb_tbl)==1) {
			$av_tbl=intval($avb_tbl[0]);
			$ab_tbl=0;
		} elseif (count($avb_tbl)==2) {
			$av_tbl=intval($avb_tbl[0]);
			$ab_tbl=intval($avb_tbl[1]);
		} else {
			$av_tbl=0;
			$ab_tbl=0;
		}
		if (($av_tbl==0)&&($ab_tbl==0)) {
			$av_tbl=1;
			$ab_tbl=0;
			$__datatable_create=true;

			$QwriteData=self::getConnection();
			$QwriteData->prepare('
CREATE TABLE :table: (
	license2package_id int(11) unsigned NOT NULL AUTO_INCREMENT,
	license_id int(11) UNSIGNED NOT NULL DEFAULT 0,
	license2package_package varchar(128) NOT NULL DEFAULT \'\',
	license2package_create_time int(11) UNSIGNED NOT NULL DEFAULT 0,
	license2package_create_user_id int(11) UNSIGNED NOT NULL DEFAULT 0,
	license2package_update_time int(11) UNSIGNED NOT NULL DEFAULT 0,
	license2package_update_user_id int(11) UNSIGNED NOT NULL DEFAULT 0,
	PRIMARY KEY (license2package_id),
	KEY license_id (license_id),
	KEY license2package_create_time (license2package_create_time),
	KEY license2package_create_user_id (license2package_create_user_id),
	KEY license2package_update_time (license2package_update_time),
	KEY license2package_update_user_id (license2package_update_user_id)
) ENGINE='.\osWMensch\Server\Configure::getValueAsString('mysql_engine').' DEFAULT CHARSET='.\osWMensch\Server\Configure::getValueAsString('mysql_character').' COMMENT=:version:
');
			$QwriteData->bindTable(':table:', $__datatable_table);
			$QwriteData->bindString(':version:', $av_tbl.'.'.$ab_tbl);
			if ($QwriteData->execute()===null) {
				return false;
			}
		}

		if ($__datatable_do===true) {
			$QwriteData=self::getConnection();
			$QwriteData->prepare('ALTER TABLE :table: COMMENT = :version:;');
			$QwriteData->bindTable(':table:', $__datatable_table);
			$QwriteData->bindString(':version:', $av_tbl.'.'.$ab_tbl);
			$QwriteData->execute();
			if ($QwriteData->execute()===null) {
				return false;
			}
		}

		return true;
	}

}

?>