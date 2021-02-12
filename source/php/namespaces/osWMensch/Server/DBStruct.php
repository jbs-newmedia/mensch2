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
	public function checkTables():bool{
		if ($this->checkTableServer()!==true) {
			return false;
		}

		return true;
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

}

?>