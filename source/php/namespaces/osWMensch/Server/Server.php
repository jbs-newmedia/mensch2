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

class Server {

	use BaseConnectionTrait;

	/**
	 * @var array
	 */
	private array $server_list=[];

	/**
	 * Server constructor.
	 */
	public function __construct() {

	}

	/**
	 * @return array
	 */
	public function getServerList():array {
		$this->server_list=[];
		$QgetData=self::getConnection();
		$QgetData->prepare('SELECT * FROM :table: WHERE 1 ORDER BY server_name ASC, server_rank DESC');
		$QgetData->bindTable(':table:', 'mensch_server');
		foreach ($QgetData->query() as $server) {
			$this->server_list[$server['server_id']]=$server;
		}

		return $this->server_list;
	}

	/**
	 * @param int $server_id
	 * @return array
	 */
	public function getServerDetails(int $server_id):array {
		if ($this->server_list==[]) {
			$this->getServerList();
		}

		if (isset($this->server_list[$server_id])) {
			return $this->server_list[$server_id];
		}

		return [];
	}

	/**
	 * @param string $server_name
	 * @param int $server_rank
	 * @param string $server_url
	 * @param string $server_file
	 * @param string $server_secure
	 * @param string $server_token
	 * @param int $server_status
	 * @return bool
	 */
	public static function createServer(string $server_name, int $server_rank, string $server_url, string $server_file, string $server_secure, string $server_token, int $server_status):bool {
		$QinsertData=self::getConnection();
		$QinsertData->prepare('INSERT INTO :table: (server_name, server_version, server_rank, server_url, server_file, server_secure, server_token, server_status, server_lastconnect, server_create_time, server_create_user_id, server_update_time, server_update_user_id) VALUES (:server_name:, :server_version:, :server_rank:, :server_url:, :server_file:, :server_secure:, :server_token:, :server_status:, :server_lastconnect:, :server_create_time:, :server_create_user_id:, :server_update_time:, :server_update_user_id:)');
		$QinsertData->bindTable(':table:', 'mensch_server');
		$QinsertData->bindString(':server_name:', $server_name);
		$QinsertData->bindString(':server_version:', 0);
		$QinsertData->bindInt(':server_rank:', $server_rank);
		$QinsertData->bindString(':server_url:', $server_url);
		$QinsertData->bindString(':server_file:', $server_file);
		$QinsertData->bindString(':server_secure:', $server_secure);
		$QinsertData->bindString(':server_token:', $server_token);
		$QinsertData->bindInt(':server_status:', $server_status);
		$QinsertData->bindInt(':server_lastconnect:', 0);
		$QinsertData->bindInt(':server_create_time:', time());
		$QinsertData->bindInt(':server_create_user_id:', 0);
		$QinsertData->bindInt(':server_update_time:', time());
		$QinsertData->bindInt(':server_update_user_id:', 0);
		$QinsertData->execute();

		return true;
	}

	/**
	 * @param int $server_id
	 * @param string $server_name
	 * @param string $server_version
	 * @param int $server_rank
	 * @param string $server_url
	 * @param string $server_file
	 * @param string $server_secure
	 * @param string $server_token
	 * @param int $server_status
	 * @param int $server_lastconnect
	 * @return bool
	 */
	public static function updateServer(int $server_id, string $server_name, string $server_version, int $server_rank, string $server_url, string $server_file, string $server_secure, string $server_token, int $server_status, int $server_lastconnect):bool {
		$QupdateData=self::getConnection();
		$QupdateData->prepare('UPDATE :table: SET server_name=:server_name:, server_version=:server_version:, server_rank=:server_rank:, server_url=:server_url:, server_file=:server_file:, server_secure=:server_secure:, server_token=:server_token:, server_status=:server_status:, server_lastconnect=:server_lastconnect:, server_update_time=:server_update_time:, server_update_user_id=:server_update_user_id: WHERE server_id=:server_id:');
		$QupdateData->bindTable(':table:', 'mensch_server');
		$QupdateData->bindString(':server_name:', $server_name);
		$QupdateData->bindString(':server_version:', $server_version);
		$QupdateData->bindInt(':server_rank:', $server_rank);
		$QupdateData->bindString(':server_url:', $server_url);
		$QupdateData->bindString(':server_file:', $server_file);
		$QupdateData->bindString(':server_secure:', $server_secure);
		$QupdateData->bindString(':server_token:', $server_token);
		$QupdateData->bindInt(':server_status:', $server_status);
		$QupdateData->bindInt(':server_lastconnect:', $server_lastconnect);
		$QupdateData->bindInt(':server_update_time:', time());
		$QupdateData->bindInt(':server_update_user_id:', 0);
		$QupdateData->bindInt(':server_id:', $server_id);
		$QupdateData->execute();

		return true;
	}

	/**
	 * @param int $server_id
	 * @param string $server_version
	 * @return bool
	 */
	public static function updateServerVersion(int $server_id, string $server_version):bool {
		$QupdateData=self::getConnection();
		$QupdateData->prepare('UPDATE :table: SET server_version=:server_version:, server_update_time=:server_update_time:, server_update_user_id=:server_update_user_id: WHERE server_id=:server_id:');
		$QupdateData->bindTable(':table:', 'mensch_server');
		$QupdateData->bindString(':server_version:', $server_version);
		$QupdateData->bindInt(':server_update_time:', time());
		$QupdateData->bindInt(':server_update_user_id:', 0);
		$QupdateData->bindInt(':server_id:', $server_id);
		$QupdateData->execute();

		return true;
	}

	/**
	 * @param int $server_id
	 * @param int $server_lastconnect
	 * @return bool
	 */
	public static function updateServerLastConnect(int $server_id, int $server_lastconnect):bool {
		$QupdateData=self::getConnection();
		$QupdateData->prepare('UPDATE :table: SET server_lastconnect=:server_lastconnect:, server_update_time=:server_update_time:, server_update_user_id=:server_update_user_id: WHERE server_id=:server_id:');
		$QupdateData->bindTable(':table:', 'mensch_server');
		$QupdateData->bindString(':server_lastconnect:', $server_lastconnect);
		$QupdateData->bindInt(':server_update_time:', time());
		$QupdateData->bindInt(':server_update_user_id:', 0);
		$QupdateData->bindInt(':server_id:', $server_id);
		$QupdateData->execute();

		return true;
	}

	/**
	 * @param int $server_id
	 * @return bool
	 */
	public static function deleteServer(int $server_id):bool {
		$QupdateData=self::getConnection();
		$QupdateData->prepare('DELETE FROM :table: WHERE server_id=:server_id:');
		$QupdateData->bindTable(':table:', 'mensch_server');
		$QupdateData->bindInt(':server_id:', $server_id);
		$QupdateData->execute();

		return true;
	}

	/**
	 * @param int $server_id
	 */
	public function downloadServer(int $server_id):void {
		$server_details=$this->getServerDetails($server_id);
		$server_main=\osWMensch\Server\Configure::getValueAsString('source_path').'server.main'.DIRECTORY_SEPARATOR.'stable'.DIRECTORY_SEPARATOR.'index.php';
		$php_source=file_get_contents($server_main);
		$php_source=str_replace('$SERVER_NAME$', $server_details['server_name'], $php_source);
		$php_source=str_replace('$SERVER_URL$', $server_details['server_url'], $php_source);
		$php_source=str_replace('$SERVER_FILE$', $server_details['server_file'], $php_source);
		$php_source=str_replace('$SERVER_LIST_NAME$', \osWMensch\Server\Configure::getValueAsString('source_serverlist_name'), $php_source);
		$php_source=str_replace('$SERVER_LIST$', \osWMensch\Server\Configure::getValueAsString('source_serverlist'), $php_source);
		$php_source=str_replace('$SERVER_SECURE$', $server_details['server_secure'], $php_source);
		$php_source=str_replace('$SERVER_TOKEN$', $server_details['server_token'], $php_source);
		header('Content-Type: text/plain');
		header('Content-Disposition: attachment; filename=index.php');
		header('Pragma: no-cache');
		die($php_source);
	}

}

?>