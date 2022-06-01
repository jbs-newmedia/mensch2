<?php

/**
 * This file is part of the Mensch2 package
 *
 * @author Juergen Schwind
 * @copyright Copyright (c) JBS New Media GmbH - Juergen Schwind (https://jbs-newmedia.com)
 * @package Mensch2
 * @link https://oswframe.com
 * @license MIT License
 */

namespace osWMensch\Server;

class Zip {

	/**
	 * @var string
	 */
	private string $dir='';

	/**
	 * @var object|null
	 */
	private ?object $zip=null;

	/**
	 * Zip constructor.
	 */
	public function __construct() {

	}

	/**
	 * @param string $dir
	 * @param string $file
	 * @return bool
	 */
	public function packDir(string $dir, string $file):bool {
		$this->dir=$dir;
		$this->zip=new \ZipArchive();
		if ($this->zip->open($file, \ZipArchive::CREATE)===true) {
			$this->packDirEngine($this->dir);
			$this->zip->close();
			return true;
		}

		return false;
	}

	/**
	 * @param string $dir
	 * @return bool
	 */
	public function packDirEngine(string $dir):bool {
		$handle=opendir($dir);
		while ($datei=readdir($handle)) {
			if (($datei!='.')&&($datei!='..')) {
				$file=$dir.$datei;
				if (is_dir($file)) {
					$this->zip->addEmptyDir(str_replace($this->dir, '', $file));
					$this->packDirEngine($file.'/');
				}
				if (is_file($file)) {
					$this->zip->addFile($file, str_replace($this->dir, '', $file));
				}
			}
		}
		closedir($handle);

		return true;
	}

	/**
	 * @param string $file
	 * @param string $dir
	 * @return bool
	 */
	public function unpackDir(string $file, string $dir):bool {
		$this->zip=new \ZipArchive();
		$this->zip->open($file);
		if ($this->zip->numFiles>0) {
			if (!is_dir($dir)) {
				mkdir($dir);
			}
			chmod($dir, Configure::getValueAsInt('chmod_file'));
			for ($i=0; $i<$this->zip->numFiles; $i++) {
				$stat=$this->zip->statIndex($i);
				if (($stat['crc']==0)&&($stat['size']==0)) {
					# dir
					if (!is_dir($dir.$stat['name'])) {
						mkdir($dir.$stat['name']);
					}
					chmod($dir.$stat['name'], Configure::getValueAsInt('chmod_dir'));
				} else {
					#file
					$data=$this->zip->getFromIndex($i);
					file_put_contents($dir.$stat['name'], $data);
					chmod($dir.$stat['name'], Configure::getValueAsInt('chmod_file'));
				}
			}
		}
		$this->zip->close();

		return true;
	}

}

?>