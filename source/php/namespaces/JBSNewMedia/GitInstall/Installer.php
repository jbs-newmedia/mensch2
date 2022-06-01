<?php

/**
 * This file is part of the GITInstall package
 *
 * @author Juergen Schwind
 * @copyright Copyright (c) JBS New Media GmbH - Juergen Schwind (https://jbs-newmedia.com)
 * @package GITInstall
 * @link https://oswframe.com
 * @license MIT License
 */

namespace JBSNewMedia\GitInstall;

class Installer {

	/**
	 * @var string
	 */
	protected string $action='';

	/**
	 * @var string
	 */
	protected string $useragent='';

	/**
	 * @var string
	 */
	protected string $name='';

	/**
	 * @var string
	 */
	protected string $git='';

	/**
	 * @var string
	 */
	protected string $url='';

	/**
	 * @var string
	 */
	protected string $release='';

	/**
	 * @var string
	 */
	protected string $user='';

	/**
	 * @var string
	 */
	protected string $password='';

	/**
	 * @var string
	 */
	protected string $token='';

	/**
	 * @var string
	 */
	protected string $remote_path='';

	/**
	 * @var array
	 */
	protected array $files=[];

	/**
	 * @var array
	 */
	protected array $directories=[];

	/**
	 * @var string
	 */
	protected string $real_path='';

	/**
	 * @var string
	 */
	protected string $local_real_path='';

	/**
	 * @var string
	 */
	protected string $local_version='';

	/**
	 * @var string
	 */
	protected string $remote_version='';

	/**
	 * @var string
	 */
	protected string $git_zip_url='';

	/**
	 * @var string
	 */
	protected string $local_version_file='';

	/**
	 * @var string
	 */
	protected string $local_running_file='';

	/**
	 * @var int
	 */
	protected int $chmod_file=0664;

	/**
	 * @var int
	 */
	protected int $chmod_dir=0775;

	/**
	 * @var bool
	 */
	protected bool $executable=false;

	/**
	 * @var bool
	 */
	protected bool $error=false;

	/**
	 * @var string
	 */
	protected string $error_string='';

	/**
	 * @var \ZipArchive
	 */
	protected \ZipArchive $zip_archive;

	/**
	 *
	 */
	public function __construct() {
		$this->setUseragent('Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0');
	}

	/**
	 * @param string $action
	 */
	public function setAction(string $action):void {
		$this->action=$action;
	}

	/**
	 * @return string
	 */
	public function getAction():string {
		return $this->action;
	}

	/**
	 * @param string $useragent
	 */
	public function setUseragent(string $useragent):void {
		$this->useragent=$useragent;
	}

	/**
	 * @return string
	 */
	public function getUseragent():string {
		return $this->useragent;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name):void {
		$this->name=$name;
	}

	/**
	 * @return string
	 */
	public function getName():string {
		return $this->name;
	}

	/**
	 * @param string $git
	 */
	public function setGit(string $git):void {
		$this->git=$git;
	}

	/**
	 * @return string
	 */
	public function getGit():string {
		return $this->git;
	}

	/**
	 * @param string $url
	 */
	public function setUrl(string $url):void {
		$this->url=$url;
	}

	/**
	 * @return string
	 */
	public function getUrl():string {
		return $this->url;
	}

	/**
	 * @param string $release
	 */
	public function setRelease(string $release):void {
		$this->release=$release;
	}

	/**
	 * @return string
	 */
	public function getRelease():string {
		return $this->release;
	}

	/**
	 * @param string $user
	 */
	public function setUser(string $user):void {
		$this->user=$user;
	}

	/**
	 * @return string
	 */
	public function getUser():string {
		return $this->user;
	}

	/**
	 * @param string $password
	 */
	public function setPassword(string $password):void {
		$this->password=$password;
	}

	/**
	 * @return string
	 */
	public function getPassword():string {
		return $this->password;
	}

	/**
	 * @param string $token
	 */
	public function setToken(string $token):void {
		$this->token=$token;
	}

	/**
	 * @return string
	 */
	public function getToken():string {
		return $this->token;
	}

	/**
	 * @param string $local_path
	 */
	public function setLocalPath(string $local_path):void {
		$this->local_path=$local_path;
		$this->setLocalRealPath($this->normalizePath($this->getRealPath().$local_path).DIRECTORY_SEPARATOR);
	}

	/**
	 * @return string
	 */
	public function getLocalPath():string {
		return $this->local_path;
	}

	/**
	 * @param array $files
	 */
	public function setFiles(array $files):void {
		$this->files=$files;
	}

	/**
	 * @return array
	 */
	public function getFiles():array {
		return $this->files;
	}

	/**
	 * @param array $directories
	 */
	public function setDirectories(array $directories):void {
		$this->directories=$directories;
	}

	/**
	 * @return array
	 */
	public function getDirectories():array {
		return $this->directories;
	}

	/**
	 * @param string $remote_path
	 */
	public function setRemotePath(string $remote_path):void {
		$this->remote_path=$remote_path;
	}

	/**
	 * @return string
	 */
	public function getRemotePath():string {
		return $this->remote_path;
	}

	/**
	 * @param string $real_path
	 */
	public function setRealPath(string $real_path):void {
		$this->real_path=$real_path;
	}

	/**
	 * @return string
	 */
	public function getRealPath():string {
		return $this->real_path;
	}

	/**
	 * @param string $local_real_path
	 */
	protected function setLocalRealPath(string $local_real_path):void {
		$this->local_real_path=$local_real_path;
	}

	/**
	 * @return string
	 */
	protected function getLocalRealPath():string {
		return $this->local_real_path;
	}

	/**
	 * @param string $local_version
	 */
	public function setLocalVersion(string $local_version):void {
		$this->local_version=$local_version;
	}

	/**
	 * @return string
	 */
	public function getLocalVersion():string {
		return $this->local_version;
	}

	/**
	 * @param string $remote_version
	 */
	public function setRemoteVersion(string $remote_version):void {
		$this->remote_version=$remote_version;
	}

	/**
	 * @return string
	 */
	public function getRemoteVersion():string {
		return $this->remote_version;
	}

	/**
	 * @param string $git_zip_url
	 */
	public function setGitZipUrl(string $git_zip_url):void {
		$this->git_zip_url=$git_zip_url;
	}

	/**
	 * @return string
	 */
	public function getGitZipUrl():string {
		return $this->git_zip_url;
	}

	/**
	 * @param string $local_version_file
	 */
	public function setLocalVersionFile(string $local_version_file):void {
		$this->local_version_file=$this->normalizePath($this->getRealPath().$local_version_file);
	}

	/**
	 * @return string
	 */
	public function getLocalVersionFile():string {
		return $this->local_version_file;
	}

	/**
	 * @param string $local_running_file
	 */
	public function setLocalRunningFile(string $local_running_file):void {
		$this->local_running_file=$local_running_file;
	}

	/**
	 * @return string
	 */
	public function getLocalRunningFile():string {
		return $this->local_running_file;
	}

	/**
	 * @param int $chmod_file
	 */
	public function setChmodFile(int $chmod_file):void {
		$this->chmod_file=$chmod_file;
	}

	/**
	 * @return int
	 */
	public function getChmodFile():int {
		return $this->chmod_file;
	}

	/**
	 * @param int $chmod_dir
	 */
	public function setChmodDir(int $chmod_dir):void {
		$this->chmod_dir=$chmod_dir;
	}

	/**
	 * @return int
	 */
	public function getChmodDir():int {
		return $this->chmod_dir;
	}

	/**
	 * @param bool $executable
	 */
	public function setExecutable(bool $executable):void {
		$this->executable=$executable;
	}

	/**
	 * @return bool
	 */
	public function isExecutable():bool {
		return $this->executable;
	}

	/**
	 * @param bool $error
	 */
	public function setError(bool $error):void {
		$this->error=$error;
	}

	/**
	 * @return bool
	 */
	public function isError():bool {
		return $this->error;
	}

	/**
	 * @param string $error_string
	 */
	public function setErrorString(string $error_string):void {
		$this->error_string=$error_string;
	}

	/**
	 * @return string
	 */
	public function getErrorString():string {
		return $this->error_string;
	}

	/**
	 * @param \ZipArchive $zip_archive
	 */
	public function setZipArchive(\ZipArchive $zip_archive):void {
		$this->zip_archive=$zip_archive;
	}

	/**
	 * @return \ZipArchive
	 */
	public function getZipArchive():\ZipArchive {
		return $this->zip_archive;
	}

	/**
	 * @return void
	 */
	public function runEngine():array {
		$output=[];
		$output['action']=$this->getAction();
		$output['running']=false;
		$output['running_mode']='';
		$output['result']=false;
		$output['result_message']='';
		$output['error']=false;
		$output['error_message']='';
		$output['info']=[];

		if (in_array($this->getAction(), ['info'])) {
			$output['running']=true;
			$output['running_mode']='info';
			if ($this->setInformation()===true) {
				$output['info']['name']=$this->getName();
				$output['info']['version_local']=$this->getLocalVersion();
				$output['info']['version_remote']=$this->getRemoteVersion();
				$output['info']['executable']=$this->isExecutable();
			} else {
				$output['error']=true;
				$output['error_message']=$this->getErrorString();
			}
		}

		if (in_array($this->getAction(), ['remove'])) {
			$output['running']=true;
			$output['running_mode']='remove';
			if ($this->setInformation()===true) {
				$output['info']['name']=$this->getName();
				$output['info']['version_local']=$this->getLocalVersion();
				$output['info']['version_remote']=$this->getRemoteVersion();
				$output['info']['executable']=$this->isExecutable();
				if ($this->removeAll()===true) {
					$output['result']=true;
					$output['running_mode']='remove';
					$output['result_message']='removed successfully';
				} else {
					$output['error']=true;
					$output['error_message']=$this->getErrorString();
				}
			}
		}

		if (in_array($this->getAction(), ['execute'])) {
			$output['running']=true;
			if ($this->setInformation()===true) {
				$output['info']['name']=$this->getName();
				$output['info']['version_local']=$this->getLocalVersion();
				$output['info']['version_remote']=$this->getRemoteVersion();
				$output['info']['executable']=$this->isExecutable();
				if ($this->isExecutable()===true) {
					if ($this->installZip()===true) {
						$output['result']=true;
						if ($this->getLocalVersion()=='') {
							$output['running_mode']='install';
							$output['result_message']='installed successfully';
						} else {
							$output['running_mode']='updated';
							$output['result_message']='updated successfully';
						}
					} else {
						$output['error']=true;
						$output['error_message']=$this->getErrorString();
					}
				} else {
					if ($this->isError()) {
						$output['error']=true;
						$output['error_message']=$this->getErrorString();
					} else {
						$output['result']=true;
						$output['result_message']='up2date';
					}
				}
			} else {
				$output['error']=true;
				$output['error_message']=$this->getErrorString();
			}
		}

		return $output;
	}

	/**
	 * @param string $url
	 * @return string
	 */
	protected function getUrlData(string $url):string {
		if (in_array($this->getGit(), ['github'])) {
			$useragent='Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';

			$ch=curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			if (strlen($this->getUser())>0) {
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($ch, CURLOPT_USERPWD, $this->getUser().":".$this->getPassword());
			}

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

			$return=curl_exec($ch);
			curl_close($ch);

			return $return;
		}
		if (in_array($this->getGit(), ['gitlab'])) {
			$useragent='Mozilla/5.0 (Windows NT 6.2; WOW64; rv:17.0) Gecko/20100101 Firefox/17.0';

			$ch=curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_USERAGENT, $useragent);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			if (strlen($this->getToken())>0) {
				$headers=[];
				$headers[]='PRIVATE-TOKEN: '.$this->getToken();
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			}

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

			$return=curl_exec($ch);
			curl_close($ch);

			return $return;
		}

		return '';
	}

	/**
	 * @return bool
	 */
	public function setInformation():bool {
		$remote_info=json_decode($this->getUrlData($this->getUrl()), true);
		if (!is_array($remote_info)) {
			$this->setError(true);
			$this->setErrorString('error loading json (remote)');

			return false;
		}

		if (file_exists($this->getLocalVersionFile())) {
			$local_info=json_decode(file_get_contents($this->getLocalVersionFile()), true);
			if (is_array($local_info)) {
				if ((isset($local_info['name']))&&(isset($local_info['version']))&&(isset($local_info['release']))&&($local_info['name']==$this->getName())&&($local_info['release']==$this->getRelease())) {
					$this->setLocalVersion($local_info['version']);
				}
				if (isset($local_info['files'])) {
					$this->setFiles($local_info['files']);
				}
				if (isset($local_info['directories'])) {
					$this->setDirectories($local_info['directories']);
				}
			}
		}

		$load=false;

		if (in_array($this->getGit(), ['github'])) {
			foreach ($remote_info as $_git) {
				if ($this->getRelease()=='stable') {
					if (($_git['draft']==false)&&($_git['prerelease']==false)) {
						$this->setRemoteVersion($_git['tag_name']);
						$this->setGitZipUrl($_git['zipball_url']);
						$load=true;
						break;
					}
				}
				if ($this->getRelease()=='prerelease') {
					if (($_git['draft']==false)&&($_git['prerelease']==true)) {
						$this->setRemoteVersion($_git['tag_name']);
						$this->setGitZipUrl($_git['zipball_url']);
						$load=true;
						break;
					}
				}
			}
		}

		if (in_array($this->getGit(), ['gitlab'])) {
			foreach ($remote_info as $_git) {
				if ($this->getRelease()=='stable') {
					if ($_git['upcoming_release']==false) {
						$this->setRemoteVersion($_git['tag_name']);
						foreach ($_git['assets']['sources'] as $source) {
							if ($source['format']=='zip') {
								$this->setGitZipUrl($source['url']);
								$load=true;
								break;
							}
						}
						break;
					}
				}
				if ($this->getRelease()=='prerelease') {
					if ($_git['upcoming_release']==true) {
						$this->setRemoteVersion($_git['tag_name']);
						foreach ($_git['assets']['sources'] as $source) {
							if ($source['format']=='zip') {
								$this->setGitZipUrl($source['url']);
								$load=true;
								break;
							}
						}
						break;
					}
				}
			}
		}

		if ($load!==true) {
			$this->setError(true);
			$this->setErrorString('error loading git');

			return false;
		}

		if (version_compare($this->getLocalVersion(), $this->getRemoteVersion(), '<')) {
			$this->setExecutable(true);
		}

		return true;
	}

	public function installZip():bool {
		$zip=$this->getUrlData($this->getGitZipUrl());
		if (($zip===null)||($zip===false)||(strlen($zip)==0)) {
			return false;
		}
		$this->setZipArchive(new \ZipArchive());
		$tmp_file=tempnam(sys_get_temp_dir(), md5(uniqid(microtime(true))));
		file_put_contents($tmp_file, $zip);
		$this->getZipArchive()->open($tmp_file);
		if ($this->getZipArchive()->count()>0) {
			file_put_contents($this->getLocalRunningFile(), time());
			@chmod($this->getLocalRunningFile(), $this->getChmodFile());

			if (is_dir($this->getLocalRealPath())!==true) {
				mkdir($this->getLocalRealPath(), $this->getChmodDir(), true);
			}
			$git_base_path='';
			$git_path='';
			$files=[];
			$directories=[];
			$files_local=$this->getFiles();
			$directories_local=$this->getDirectories();
			for ($i=0; $i<$this->getZipArchive()->count(); $i++) {
				$stat=$this->getZipArchive()->statIndex($i);
				if (($git_path=='')||(strpos($stat['name'], $git_path)===0)) {
					if (($stat['crc']==0)&&($stat['size']==0)) {
						if ($i==0) {
							$git_path=$stat['name'];
							$git_base_path=$stat['name'];
							if ($this->getRemotePath()!='') {
								$git_path.=$this->getRemotePath();
							}
						}
						$dir=str_replace([$git_path.DIRECTORY_SEPARATOR, $git_base_path], ['', ''], $stat['name']);
						if (is_dir($this->getLocalRealPath().$dir)!==true) {
							mkdir($this->getLocalRealPath().$dir, $this->getChmodDir(), true);
						}
						if (isset($directories_local[$this->getLocalRealPath().$dir])) {
							unset($directories_local[$this->getLocalRealPath().$dir]);
						}
						$directories[$this->getLocalRealPath().$dir]='';
					} else {
						$data=$this->getZipArchive()->getFromIndex($i);
						$dir=str_replace($git_path.DIRECTORY_SEPARATOR, '', $stat['name']);
						file_put_contents($this->getLocalRealPath().$dir, $data);
						@chmod($this->getLocalRealPath().$dir, $this->getChmodFile());
						if (isset($files_local[$this->getLocalRealPath().$dir])) {
							unset($files_local[$this->getLocalRealPath().$dir]);
						}
						$files[$this->getLocalRealPath().$dir]='';
					}
				}
			}
			if ($files_local!=[]) {
				foreach ($files_local as $file=>$foo) {
					if (is_file($file)) {
						unlink($file);
					}
				}
			}
			if ($directories_local!=[]) {
				krsort($directories_local);
				foreach ($directories_local as $directory=>$foo) {
					if ((is_dir($directory))&&(count(scandir($directory))==2)) {
						rmdir($directory);
					}
				}
			}
			unlink($tmp_file);

			$result=['name'=>$this->getName(), 'version'=>$this->getRemoteVersion(), 'release'=>$this->getRelease(), 'files'=>$files, 'directories'=>$directories];
			file_put_contents($this->getLocalVersionFile(), json_encode($result));
			@chmod($this->getLocalVersionFile(), $this->getChmodFile());

			if (file_exists($this->getLocalRunningFile())) {
				unlink($this->getLocalRunningFile());
			}

			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public function removeAll():bool {
		if (file_exists($this->getLocalVersionFile())) {
			$local_info=json_decode(file_get_contents($this->getLocalVersionFile()), true);
			if (isset($local_info['files'])) {
				foreach ($local_info['files'] as $file=>$foo) {
					if (is_file($file)) {
						unlink($file);
					}
				}
			}
			if (isset($local_info['directories'])) {
				krsort($local_info['directories']);
				foreach ($local_info['directories'] as $directory=>$foo) {
					if ((is_dir($directory))&&(count(scandir($directory))==2)) {
						rmdir($directory);
					}
				}
			}
			unlink($this->getLocalVersionFile());

			return true;
		}

		return false;
	}

	/**
	 * @source https://mixable.blog/php-realpath-for-non-existing-path/
	 *
	 * @param string $path
	 * @return false|string|array
	 */
	public function normalizePath(string $path):false|string|array {
		return array_reduce(explode('/', $path), function($a, $b) {
			if ($a===null) {
				$a="/";
			}
			if ($b===""||$b===".") {
				return $a;
			}
			if ($b==="..") {
				return dirname($a);
			}

			return preg_replace("/\/+/", "/", "$a/$b");
		});
	}

}

?>