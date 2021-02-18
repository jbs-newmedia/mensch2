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

class Rollout {

	/**
	 * @var array
	 */
	private array $log=[];

	/**
	 * @var array
	 */
	private array $server_details=[];

	/**
	 * @var array
	 */
	private array $releases=[];

	/**
	 * @var string
	 */
	private string $packages='';

	/**
	 * @var string
	 */
	private string $checksum='';

	/**
	 * @var bool
	 */
	private bool $connected=false;

	/**
	 * @var array
	 */
	private array $package_log=[];

	/**
	 * Packer constructor.
	 *
	 * @param array $server_list
	 * @param array $releases
	 * @param array $prefix
	 */
	public function __construct(array $server_details, array $releases) {
		$this->server_details=$server_details;
		$this->releases=$releases;
	}

	/**
	 * @param string $str
	 */
	private function addLog(string $str) {
		$this->log[]=$str;
	}

	/**
	 * @return array
	 */
	public function getLog():array {
		return $this->log;
	}

	/**
	 * @return array
	 */
	public function getPackageLog():array {
		return $this->package_log;
	}

	/**
	 * @return bool
	 */
	public function connectServer():bool {
		if ($this->server_details['server_status']==1) {
			if ($this->getUrlData($this->server_details['server_url'].$this->server_details['server_file'].'?action=hello')==$this->server_details['server_name']) {
				Server::updateServerLastConnect($this->server_details['server_id'], time());
				Server::updateServerVersion($this->server_details['server_id'], $this->getServerVersion());
				$this->connected=true;
			} else {
				$this->addLog($this->server_details['server_name'].' is offline');
				$this->connected=false;
			}
		} else {
			$this->addLog($this->server_details['server_name'].' is inactive');
			$this->connected=false;
		}

		return $this->connected;
	}

	/**
	 * @return bool
	 */
	public function disconnectServer():bool {
		if ($this->getUrlData($this->server_details['server_url'].$this->server_details['server_file'].'?action=goodbye')==$this->server_details['server_name']) {
			$this->connected=true;
		} else {
			$this->connected=false;
		}

		return $this->connected;
	}

	/**
	 * @return string
	 */
	public function getVersion():string {
		$source_dir=Configure::getValueAsString('source_path');
		$file=$source_dir.DIRECTORY_SEPARATOR.'server.main'.DIRECTORY_SEPARATOR.'stable'.DIRECTORY_SEPARATOR.'index.php';
		$content=file_get_contents($file);
		preg_match('/\'server_version\'=>\'([0-9\.]*)\',/si', $content, $matches);

		return number_format(floatval($matches[1]), 2, '.', '');
	}

	/**
	 * @return string
	 */
	public function getServerVersion():string {
		return number_format(floatval($this->getUrlData($this->server_details['server_url'].$this->server_details['server_file'].'?action=server_version')), 2, '.', '');
	}

	/**
	 * @return string
	 */
	public function getServerCheckSum():string {
		return $this->getUrlData($this->server_details['server_url'].$this->server_details['server_file'].'?action=server_checksum&packages='.$this->getPackages());
	}

	/**
	 * @return string
	 */
	public function getCheckSum():string {
		$file=Configure::getValueAsString('mensch_path').'data'.DIRECTORY_SEPARATOR.'server.checksum';
		if (file_exists($file)===true) {
			$this->checksum=file_get_contents($file);

		}

		return $this->checksum;
	}

	/**
	 * @return string
	 */
	public function getPackages():string {
		$file=Configure::getValueAsString('mensch_path').'data'.DIRECTORY_SEPARATOR.'server.packages';
		if (file_exists($file)===true) {
			$this->packages=file_get_contents($file);

		}

		return $this->packages;
	}

	/**
	 * @return bool
	 */
	public function checkServer2Update():bool {
		if ((floatval($this->getServerVersion()))<(floatval($this->getVersion()))) {
			$this->addLog('Server is outdated with version '.$this->getServerVersion().', current version is '.$this->getVersion());
			$this->getServerVersion();
			$this->updateServer();
			if ((floatval($this->getServerVersion()))==(floatval($this->getVersion()))) {
				$this->addLog('Server updated successfully with version '.$this->getServerVersion());
				Server::updateServerVersion($this->server_details['server_id'], $this->getServerVersion());

				return true;
			} else {
				$this->addLog('Server can\'t updated with version '.$this->getServerVersion());

				return false;
			}
		} else {
			$this->addLog('Server is up to date with version '.$this->getServerVersion());

			return true;
		}
	}

	/**
	 * @return bool
	 */
	public function checkServerPackages2Update():bool {
		if ($this->getServerCheckSum()!=$this->getCheckSum()) {
			$this->addLog('Packages are outdated with checksum '.$this->getServerCheckSum().', current checksum is '.$this->getCheckSum());
			$this->updateServerData();

			if ($this->getServerCheckSum()==$this->getCheckSum()) {
				$this->addLog('Packages updated successfully with checksum '.$this->getServerCheckSum());

				return true;
			} else {
				$this->addLog('Packages can\'t update with checksum '.$this->getServerCheckSum());

				return false;
			}
		} else {
			$this->addLog('Packages are up to date with checksum '.$this->getServerCheckSum());

			return true;
		}
	}

	/**
	 * @param array $license_data
	 * @return bool
	 */
	public function checkServerLicense2Update(array $license_data):bool {
		if ($this->updateServerLicense($license_data)===true) {
			$this->addLog('License updated successfully');
		} else {
			$this->addLog('License can\'t update');
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function updateServer():bool {
		$source_dir=Configure::getValueAsString('source_path');
		$file=$source_dir.DIRECTORY_SEPARATOR.'server.main'.DIRECTORY_SEPARATOR.'stable'.DIRECTORY_SEPARATOR.'index.php';
		$file_server_content_current=file_get_contents($file);
		$file_server_content_current=str_replace('$SERVER_NAME$', $this->server_details['server_name'], $file_server_content_current);
		$file_server_content_current=str_replace('$SERVER_URL$', $this->server_details['server_url'], $file_server_content_current);
		$file_server_content_current=str_replace('$SERVER_FILE$', $this->server_details['server_file'], $file_server_content_current);
		$file_server_content_current=str_replace('$SERVER_LIST_NAME$', \osWMensch\Server\Configure::getValueAsString('source_serverlist_name'), $file_server_content_current);
		$file_server_content_current=str_replace('$SERVER_LIST$', \osWMensch\Server\Configure::getValueAsString('source_serverlist'), $file_server_content_current);
		$file_server_content_current=str_replace('$SERVER_SECURE$', $this->server_details['server_secure'], $file_server_content_current);
		$file_server_content_current=str_replace('$SERVER_TOKEN$', $this->server_details['server_token'], $file_server_content_current);

		$token=sha1(sha1($file_server_content_current).'#'.$this->server_details['server_token']);
		$send_package='bof_'.base64_encode($this->encrypt('bof_'.base64_encode($file_server_content_current).'_eof', $this->server_details['server_secure'])).'_eof';

		$dir=Configure::getValueAsString('mensch_path').'.work'.DIRECTORY_SEPARATOR;
		Configure::makeDir($dir);
		$cache_server=$dir.'server.cache';
		file_put_contents($cache_server, $send_package);
		$cfile=new \CURLFile($cache_server, mime_content_type($cache_server), 'data');
		$post=['data'=>$cfile, 'token'=>$token];

		$ch=curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $this->server_details['server_url'].$this->server_details['server_file'].'?action=server_upgrade');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		$response=curl_exec($ch);

		unlink($cache_server);

		curl_close($ch);

		return true;
	}

	/**
	 * @return bool
	 */
	public function updateServerData():bool {
		$packages=explode(',', $this->getPackages());
		foreach ($packages as $_package) {
			$_package=explode('-', $_package);
			if (count($_package)==2) {
				$package=$_package[0];
				$release=$_package[1];

				$this->package_log[$package]=[];
				$this->package_log[$package]['package']=$package;
				$this->package_log[$package]['release']=$release;
				$this->package_log[$package]['is_current']=false;
				$this->package_log[$package]['status']=0;

				$file_package_checksum=Configure::getValueAsString('mensch_path').'data'.DIRECTORY_SEPARATOR.$package.DIRECTORY_SEPARATOR.$release.DIRECTORY_SEPARATOR.'package.checksum';
				$file_package_version=Configure::getValueAsString('mensch_path').'data'.DIRECTORY_SEPARATOR.$package.DIRECTORY_SEPARATOR.$release.DIRECTORY_SEPARATOR.'package.version';
				$file_package_content=Configure::getValueAsString('mensch_path').'data'.DIRECTORY_SEPARATOR.$package.DIRECTORY_SEPARATOR.$release.DIRECTORY_SEPARATOR.'package.content';
				$file_package_info=Configure::getValueAsString('mensch_path').'data'.DIRECTORY_SEPARATOR.$package.DIRECTORY_SEPARATOR.$release.DIRECTORY_SEPARATOR.'package.info';
				$file_package_parts=Configure::getValueAsString('mensch_path').'data'.DIRECTORY_SEPARATOR.$package.DIRECTORY_SEPARATOR.$release.DIRECTORY_SEPARATOR.'package.parts';

				if ((file_exists($file_package_checksum))&&(file_exists($file_package_version))&&(file_exists($file_package_content))) {
					$package_checksum=file_get_contents($file_package_checksum);
					if ($this->getUrlData($this->server_details['server_url'].$this->server_details['server_file'].'?action=server_check&package='.$package.'&release='.$release)!=$package_checksum) {
						$this->package_log[$package]['is_current']=false;

						$send_package=[];
						$send_package['packer_package']=$package;
						$send_package['packer_release']=$release;
						$send_package['packer_version']=file_get_contents($file_package_version);
						$send_package['packer_info']=file_get_contents($file_package_info);
						$send_package['packer_parts']=file_get_contents($file_package_parts);

						$last_part=false;
						for ($part=1; $part<=$send_package['packer_parts']; $part++) {
							$send_package['packer_checksum']=file_get_contents($file_package_checksum.'.part.'.$part);
							$send_package['packer_content']=file_get_contents($file_package_content.'.part.'.$part);
							$send_package['packer_end']='packer_end';

							$token=sha1(serialize($send_package).'#'.$this->server_details['server_token']);
							$send_package_raw='bof_'.base64_encode($this->encrypt('bof_'.base64_encode(serialize($send_package).'_eof'), $this->server_details['server_secure'])).'_eof';

							$dir=Configure::getValueAsString('mensch_path').'.work'.DIRECTORY_SEPARATOR;
							Configure::makeDir($dir);
							$cache_package=$dir.'data.package';
							file_put_contents($cache_package, $send_package_raw);
							if ($part==$send_package['packer_parts']) {
								$last_part=true;
							}

							$cfile=new \CURLFile($cache_package, mime_content_type($cache_package), 'data');
							$post=['data'=>$cfile, 'token'=>$token, 'part'=>$part, 'last_part'=>$last_part];

							$ch=curl_init();
							curl_setopt($ch, CURLOPT_HEADER, 0);
							curl_setopt($ch, CURLOPT_VERBOSE, 0);
							curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
							curl_setopt($ch, CURLOPT_URL, $this->server_details['server_url'].$this->server_details['server_file'].'?action=server_update');
							curl_setopt($ch, CURLOPT_POST, true);
							curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
							$response=curl_exec($ch);

							unlink($cache_package);
						}

						if ($this->getUrlData($this->server_details['server_url'].$this->server_details['server_file'].'?action=server_check&package='.$package.'&release='.$release)!=$package_checksum) {
							$this->package_log[$package]['status']=2;
						} else {
							$this->package_log[$package]['status']=1;
						}
					} else {
						$this->package_log[$package]['is_current']=true;
					}
				}
			}
		}

		return true;
	}

	/**
	 * @param array $license_data
	 * @return bool
	 */
	public function updateServerLicense(array $license_data):bool {
		$packages=explode(',', $this->getPackages());
		foreach ($packages as $_package) {
			if (!isset($license_data[$_package])) {
				$license_data[$_package]=[];
			}

		}

		$send_package=[];
		$send_package['license_data']=$license_data;

		$token=sha1(serialize($send_package).'#'.$this->server_details['server_token']);
		$send_package_raw='bof_'.base64_encode($this->encrypt('bof_'.base64_encode(serialize($send_package).'_eof'), $this->server_details['server_secure'])).'_eof';

		$dir=Configure::getValueAsString('mensch_path').'.work'.DIRECTORY_SEPARATOR;
		Configure::makeDir($dir);
		$cache_package=$dir.'data.license';
		file_put_contents($cache_package, $send_package_raw);

		$cfile=new \CURLFile($cache_package, mime_content_type($cache_package), 'data');
		$post=['data'=>$cfile, 'token'=>$token];

		$ch=curl_init();
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL, $this->server_details['server_url'].$this->server_details['server_file'].'?action=server_update_license');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		$response=curl_exec($ch);

		unlink($cache_package);

		if ($response=='ok') {
			return true;
		}

		return false;
	}

	/**
	 * @param string $file
	 * @return bool|string
	 */
	public function getUrlData(string $file) {
		$result='';
		$res=curl_init($file);
		curl_setopt($res, CURLOPT_RETURNTRANSFER, 1);
		$result=curl_exec($res);
		curl_close($res);

		return $result;
	}

	/**
	 * @param string $var_1
	 * @param string $var_2
	 * @return string
	 */
	public function encrypt(string $var_1, string $var_2):string {
		$l=strlen($var_2);
		if ($l<16) {
			$var_2=str_repeat($var_2, ceil(16/$l));
		}

		if ($m=strlen($var_1)%8) {
			$var_1.=str_repeat("\x00", 8-$m);
		}

		return openssl_encrypt($var_1, 'BF-ECB', $var_2, OPENSSL_RAW_DATA|OPENSSL_NO_PADDING);
	}

	/**
	 * @param string $var_1
	 * @param string $var_2
	 * @return string
	 */
	public function decrypt(string $var_1, string $var_2):string {
		$l=strlen($var_2);
		if ($l<16) {
			$var_2=str_repeat($var_2, ceil(16/$l));
		}

		return openssl_decrypt($var_1, 'BF-ECB', $var_2, OPENSSL_RAW_DATA|OPENSSL_NO_PADDING);
	}

}

?>