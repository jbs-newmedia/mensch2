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

class Packer {

	use BaseConnectionTrait;

	/**
	 * @var array
	 */
	private array $log=[];

	/**
	 * @var array
	 */
	private array $server_list=[];

	/**
	 * @var array
	 */
	private array $package_list=[];

	/**
	 * @var array
	 */
	private array $package_log=[];

	/**
	 * @var array
	 */
	private array $releases=[];

	/**
	 * @var array
	 */
	private array $prefix=[];

	/**
	 * @var array
	 */
	private array $cmb_checksum=[];

	/**
	 * @var array
	 */
	private array $cmb_packages=[];

	/**
	 * Packer constructor.
	 *
	 * @param array $server_list
	 * @param array $releases
	 * @param array $prefix
	 */
	public function __construct(array $server_list, array $releases, array $prefix) {
		$this->server_list=$server_list;
		$this->releases=$releases;
		$this->prefix=$prefix;
		$this->createServerList();
		$this->createPackageList();
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
	public function createServerList():bool {
		$data=[];
		$data['info']=[];
		$data['info']['name']=Configure::getValueAsString('source_serverlist_name');
		$data['info']['package']=Configure::getValueAsString('source_serverlist_package');
		$data['data']=[];
		foreach ($this->server_list as $server_id=>$server_data) {
			if ($server_data['server_status']==1) {
				$data['data'][$server_id]=['server_id'=>$server_data['server_id'], 'server_name'=>$server_data['server_name'], 'server_url'=>$server_data['server_url'].$server_data['server_file']];
			}
		}
		$json=json_encode($data);

		$dir=Configure::getValueAsString('mensch_path').'data'.DIRECTORY_SEPARATOR;
		Configure::makeDir($dir);
		$file=$dir.'server.list';
		if ((!file_exists($file))||((sha1_file($file))!==(sha1($json)))) {
			file_put_contents($file, $json);
			chmod($file, Configure::getValueAsInt('chmod_file'));
			$this->addLog('Serverlist updated successfully.');
		} else {
			$this->addLog('Serverlist is up to date.');
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function createPackageList():bool {
		$source_dir=Configure::getValueAsString('source_path');
		if (is_dir($source_dir)) {
			$this->package_list=[];
			$handle=opendir($source_dir);
			while ($package=readdir($handle)) {
				if (($package!='.')&&($package!='..')) {
					$data=explode('.', $package);
					$file=$source_dir.$package;
					if ((count($data)>=2)&&(is_dir($file))&&(in_array($data[0], $this->prefix))) {
						$this->package_list[]=$package;
					}
				}
			}
			asort($this->package_list);
			$this->addLog('Packagelist loaded successfully ('.count($this->package_list).').');
		}

		return true;
	}

	/**
	 * @return bool
	 */
	public function runPacker():bool {
		$source_dir=Configure::getValueAsString('source_path');
		foreach ($this->package_list as $package) {
			foreach ($this->releases as $release) {
				$package_path=$source_dir.$package.DIRECTORY_SEPARATOR.$release.DIRECTORY_SEPARATOR;
				if (is_dir($package_path)) {
					if ($package==Configure::getValueAsString('source_serverlist_package')) {
						$this->updateServerList($package_path);
					}
					$this->package_log[$package]=[];
					$this->package_log[$package]['package']=$package;
					$this->package_log[$package]['release']=$release;
					$this->createChangelogJSON($package, $release);
					$this->createPackageJSON($package, $release);
					$this->createFileListJSON($package, $release);
					$this->pack($package, $release);
				}
			}
		}

		$this->writeCheckSumFile();
		$this->writePackagesFile();

		return true;
	}

	/**
	 * @param string $package_path
	 * @return bool
	 */
	public function updateServerList(string $package_path):bool {
		$dir=$package_path.'oswtools'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'json'.DIRECTORY_SEPARATOR.'serverlist'.DIRECTORY_SEPARATOR;

		Configure::makeDir($dir);

		$file_package=$dir.Configure::getValueAsString('source_serverlist').'.json';
		$file_list=Configure::getValueAsString('mensch_path').'data'.DIRECTORY_SEPARATOR.'server.list';

		if ((!file_exists($file_package))||(!file_exists($file_list))||(sha1_file($file_package)!=sha1_file($file_list))) {
			file_put_contents($file_package, file_get_contents($file_list));
			chmod($file_package, Configure::getValueAsInt('chmod_file'));
			$this->addLog('Serverlist in package updated successfully.');
		} else {
			$this->addLog('Serverlist in package is up to date.');
		}

		return true;
	}

	/**
	 * @param string $package
	 * @param string $release
	 * @return bool
	 */
	public function createFileListJSON(string $package, string $release):bool {
		$source_dir=Configure::getValueAsString('source_path');
		$package_path=$source_dir.$package.DIRECTORY_SEPARATOR.$release;
		$files=$this->listdir($package_path);
		$isfiles=[];
		foreach ($files as $file=>$sha1) {
			$isfiles[str_replace($package_path, '', $file)]=$sha1;
		}

		if (isset($isfiles[''])) {
			unset($isfiles['']);
		}

		$json=json_encode($isfiles);

		$dir_file_list=$package_path.DIRECTORY_SEPARATOR.'oswtools'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'json'.DIRECTORY_SEPARATOR.'filelist'.DIRECTORY_SEPARATOR;

		Configure::makeDir($dir_file_list);

		$file=$dir_file_list.$package.'-'.$release.'.json';
		if ((!file_exists($file))||((sha1_file($file))!==(sha1($json)))) {
			file_put_contents($file, $json);
			chmod($file, Configure::getValueAsInt('chmod_file'));
			$this->package_log[$package]['filelist_update']=true;
		} else {
			$this->package_log[$package]['filelist_update']=false;
		}

		return true;
	}

	/**
	 * @param string $package
	 * @param string $release
	 * @return bool
	 */
	public function createPackageJSON(string $package, string $release):bool {
		$source_dir=Configure::getValueAsString('source_path');
		$package_path=$source_dir.$package.DIRECTORY_SEPARATOR.$release;

		$dir_file_list=$package_path.DIRECTORY_SEPARATOR.'oswtools'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'json'.DIRECTORY_SEPARATOR.'package'.DIRECTORY_SEPARATOR;
		$file=$dir_file_list.$package.'-'.$release.'.json';

		if (file_exists($file)) {
			$details=json_decode(file_get_contents($file), true);
			if ($details!==null) {
				if ((isset($details['info']))&&(isset($details['info']['name']))) {
					$this->package_log[$package]['name']=$details['info']['name'];
				}

				if ((isset($details['info']))&&(isset($details['info']['version']))) {
					$this->package_log[$package]['package_check']=true;
					if ($this->package_log[$package]['version']!==$details['info']['version']) {
						$details['info']['version']=$this->package_log[$package]['version'];
						file_put_contents($file, json_encode($details));
						$this->package_log[$package]['package_update']=true;
					} else {
						$this->package_log[$package]['package_update']=false;
					}
				} else {
					$this->package_log[$package]['package_check']=false;
				}
			} else {
				$this->package_log[$package]['package_check']=false;
			}
		} else {
			$this->package_log[$package]['package_check']=false;
		}

		return true;
	}

	/**
	 * @param string $package
	 * @param string $release
	 * @return bool
	 */
	public function createChangelogJSON(string $package, string $release):bool {
		$source_dir=Configure::getValueAsString('source_path');
		$package_path=$source_dir.$package.DIRECTORY_SEPARATOR.$release;

		$cmd='cd '.Configure::getValueAsString('git_path').' && git log -- source/'.$package.'/'.$release.'/';
		$return=shell_exec($cmd);
		$return=explode("\n", $return);
		$data=[];
		$commit='';
		foreach ($return as $line) {
			$line=trim($line);
			if ($line!=='') {
				if (substr($line, 0, 6)=='commit') {
					$commit=substr(trim($line), 6);
					$data[$commit]=[];
					$data[$commit]['change']=[];
				} elseif (substr($line, 0, 6)=='Author') {
					$data[$commit]['Author']=trim(substr($line, 7));
				} elseif (substr($line, 0, 4)=='Date') {
					$data[$commit]['Date']=trim(substr($line, 5));
					$data[$commit]['timestamp']=strtotime($data[$commit]['Date']);
					$data[$commit]['version']=date('Ymd.His', $data[$commit]['timestamp']);
				} else {
					$data[$commit]['change'][]=$line;
				}
				if (!isset($this->package_log[$package]['version'])) {
					$this->package_log[$package]['version']=$data[$commit]['version'];
				}
			}
		}

		$changelog=[];
		foreach ($data as $cl) {
			$changelog[$cl['version']]='- '.implode("\n- ", $cl['change']);
		}

		$json=json_encode($changelog);

		$dir_file_list=$package_path.DIRECTORY_SEPARATOR.'oswtools'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'json'.DIRECTORY_SEPARATOR.'changelog'.DIRECTORY_SEPARATOR;

		Configure::makeDir($dir_file_list);

		$file=$dir_file_list.$package.'-'.$release.'.json';
		if ((!file_exists($file))||((sha1_file($file))!==(sha1($json)))) {
			file_put_contents($file, $json);
			chmod($file, Configure::getValueAsInt('chmod_file'));
			$this->package_log[$package]['changelog_update']=true;
		} else {
			$this->package_log[$package]['changelog_update']=false;
		}

		return true;
	}

	/**
	 * @param string $package
	 * @param string $release
	 * @return bool
	 */
	public function pack(string $package, string $release):bool {
		$source_dir=Configure::getValueAsString('source_path');
		$package_path=$source_dir.$package.DIRECTORY_SEPARATOR.$release.DIRECTORY_SEPARATOR;

		$package_version=$this->package_log[$package]['version'];

		$package_file=Configure::getValueAsString('mensch_path').'data'.DIRECTORY_SEPARATOR.$package.DIRECTORY_SEPARATOR.$release.DIRECTORY_SEPARATOR.$package_version.'.zip';

		if (!file_exists($package_file)) {
			Configure::makeDir($package_file);
			$Zip=new Zip();
			$Zip->packDir($package_path, $package_file);
			sleep(1);
			$this->package_log[$package]['zip_update']=true;
		} else {
			$this->package_log[$package]['zip_update']=false;
		}
		$package_checksum=sha1_file($package_file);

		$this->addCheckSum($package_checksum);
		$this->addPackage($package.'-'.$release);

		$file_package_checksum=Configure::getValueAsString('mensch_path').'data'.DIRECTORY_SEPARATOR.$package.DIRECTORY_SEPARATOR.$release.DIRECTORY_SEPARATOR.'package.checksum';
		$file_package_version=Configure::getValueAsString('mensch_path').'data'.DIRECTORY_SEPARATOR.$package.DIRECTORY_SEPARATOR.$release.DIRECTORY_SEPARATOR.'package.version';
		$file_package_content=Configure::getValueAsString('mensch_path').'data'.DIRECTORY_SEPARATOR.$package.DIRECTORY_SEPARATOR.$release.DIRECTORY_SEPARATOR.'package.content';
		$file_package_info=Configure::getValueAsString('mensch_path').'data'.DIRECTORY_SEPARATOR.$package.DIRECTORY_SEPARATOR.$release.DIRECTORY_SEPARATOR.'package.info';
		$file_package_parts=Configure::getValueAsString('mensch_path').'data'.DIRECTORY_SEPARATOR.$package.DIRECTORY_SEPARATOR.$release.DIRECTORY_SEPARATOR.'package.parts';

		if ((!file_exists($file_package_checksum))||($package_checksum!=file_get_contents($file_package_checksum))) {
			file_put_contents($file_package_checksum, $package_checksum);
			file_put_contents($file_package_version, $package_version);
			file_put_contents($file_package_content, file_get_contents($package_file));
			copy($package_path.'oswtools'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'json'.DIRECTORY_SEPARATOR.'package'.DIRECTORY_SEPARATOR.$package.'-'.$release.'.json', $file_package_info);

			$files=scandir(Configure::getValueAsString('mensch_path').'data'.DIRECTORY_SEPARATOR.$package.DIRECTORY_SEPARATOR.$release.DIRECTORY_SEPARATOR);
			foreach ($files as $file) {
				if (strstr(Configure::getValueAsString('mensch_path').'data'.DIRECTORY_SEPARATOR.$package.DIRECTORY_SEPARATOR.$release.DIRECTORY_SEPARATOR.$file, 'part')) {
					unlink(Configure::getValueAsString('mensch_path').'data'.DIRECTORY_SEPARATOR.$package.DIRECTORY_SEPARATOR.$release.DIRECTORY_SEPARATOR.$file);
				}
			}

			$i=0;
			$handle=fopen($file_package_content, 'r');
			while (!feof($handle)) {
				$i++;
				$content=fread($handle, 1024*1024);
				$content='bof_'.base64_encode('bof_'.$content.'_eof').'_eof';
				file_put_contents($file_package_content.'.part.'.$i, $content);
				chmod($file_package_content.'.part.'.$i, Configure::getValueAsInt('chmod_file'));
				file_put_contents($file_package_checksum.'.part.'.$i, @sha1_file(Configure::getValueAsString('mensch_path').'data'.DIRECTORY_SEPARATOR.$package.DIRECTORY_SEPARATOR.$release.DIRECTORY_SEPARATOR.'package.content.part.'.$i));
				chmod($file_package_checksum.'.part.'.$i, Configure::getValueAsInt('chmod_file'));

			}
			fclose($handle);

			file_put_contents($file_package_parts, $i);
			chmod($file_package_parts, Configure::getValueAsInt('chmod_file'));
		}

		return true;
	}

	/**
	 * @source http://www.php.net/manual/de/function.readdir.php#100710
	 * @param string $dir
	 * @return array
	 */
	public function listdir(string $dir='.'):array {
		if (!is_dir($dir)) {
			return [];
		}

		return $this->listdiraux($dir);
	}

	/**
	 * @source http://www.php.net/manual/de/function.readdir.php#100710
	 * @param string $dir
	 * @param array $files
	 * @return array
	 */
	public function listdiraux(string $dir, array $files=[]):array {
		$handle=opendir($dir);
		$files[$dir]='';
		while (($file=readdir($handle))!==false) {
			if ($file=='.'||$file=='..') {
				continue;
			}
			$filepath=$dir=='.'?$file:$dir.'/'.$file;
			if (is_link($filepath)) {
				continue;
			}
			if (is_file($filepath)) {
				if (stristr($filepath, 'oswtools'.DIRECTORY_SEPARATOR.'resources'.DIRECTORY_SEPARATOR.'json'.DIRECTORY_SEPARATOR.'filelist')) {
					$files[$filepath]='dummy';
				} else {
					$files[$filepath]=sha1_file($filepath);
				}
			} elseif (is_dir($filepath)) {
				$files=$this->listdiraux($filepath, $files);
			}
		}
		closedir($handle);

		return $files;
	}

	/**
	 * @param string $checksum
	 * @return bool
	 */
	public function addCheckSum(string $checksum):bool {
		$this->cmb_checksum[]=$checksum;

		return true;
	}

	/**
	 * @param string $package
	 * @return bool
	 */
	public function addPackage(string $package):bool {
		$this->cmb_packages[]=$package;

		return true;
	}

	/**
	 * @return bool
	 */
	public function writeCheckSumFile():bool {
		$file=Configure::getValueAsString('mensch_path').'data'.DIRECTORY_SEPARATOR.'server.checksum';
		file_put_contents($file, sha1(implode('', $this->cmb_checksum)));
		chmod($file, Configure::getValueAsInt('chmod_file'));

		return true;
	}

	/**
	 * @return bool
	 */
	public function writePackagesFile():bool {
		$file=Configure::getValueAsString('mensch_path').'data'.DIRECTORY_SEPARATOR.'server.packages';
		file_put_contents($file, implode(',', $this->cmb_packages));
		chmod($file, Configure::getValueAsInt('chmod_file'));

		return true;
	}

}

?>