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

use JBSNewMedia\GitInstall\Installer;

class Core {

	/**
	 * @var string
	 */
	private string $page='';

	/**
	 * @var string
	 */
	private string $title='';

	/**
	 * @var array
	 */
	private array $pages=[];

	/**
	 * Core constructor.
	 */
	public function __construct() {
		$this->loadPages();
	}

	/**
	 * @param string $page
	 * @return object
	 */
	public function setPage(string $page):object {
		if (isset($this->pages[$page])) {
			$this->page=$page;
		} else {
			$this->page='dashboard';
		}

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPage():string {
		return $this->page;
	}

	/**
	 * @param string $page
	 * @return object
	 */
	public function setTitle(string $title):object {
		$this->title=$title;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTitle():string {
		return $this->title;
	}

	/**
	 * @param string $page
	 * @return bool
	 */
	public function isActivePage(string $page):bool {
		if (strpos($this->getPage(), '_')>0) {
			$sub=substr($this->getPage(), 0, strpos($this->getPage(), '_'));
			if ($sub==$page) {
				return true;
			}
			if ($page==$this->getPage()) {
				return true;
			}
		} else {
			if ($page==$this->getPage()) {
				return true;
			}
		}

		return false;
	}

	/**
	 * @return object
	 */
	private function loadPages():object {
		foreach (glob(\osWMensch\Server\Configure::getValueAsString('mensch_path').'php'.DIRECTORY_SEPARATOR.'pages'.DIRECTORY_SEPARATOR.'*.inc.php') as $_page) {
			$_page=str_replace([\osWMensch\Server\Configure::getValueAsString('mensch_path').'php'.DIRECTORY_SEPARATOR.'pages'.DIRECTORY_SEPARATOR, '.inc.php'], ['', ''], $_page);
			$this->pages[$_page]=$_page;
		}

		return $this;
	}

	/**
	 * @return string
	 */
	public static function getVersion():string {
		$file=OSWMENSCH_CORE_ABSPATH.'vendor'.DIRECTORY_SEPARATOR.'mensch2.json';
		if (file_exists($file)===true) {
			$json=json_decode(file_get_contents($file), true);
			if (isset($json['version'])) {
				return $json['version'];
			}
		}

		return '';
	}

	/**
	 * @return string
	 */
	public static function getCurrentTag():string {
		$file=OSWMENSCH_CORE_ABSPATH.'vendor'.DIRECTORY_SEPARATOR.'mensch2.tag';
		if ((file_exists($file)!==true)||(filemtime($file)<=time()-(60*60*24))) {
			$Installer=new Installer();
			$Installer->setRealPath(OSWMENSCH_CORE_ABSPATH);
			$Installer->setName('jbsnewmedia/mensch2');
			$Installer->setGit('github');
			$Installer->setUrl('https://api.github.com/repos/jbs-newmedia/mensch2/releases');
			$Installer->setRelease('stable');
			$Installer->setAction('info');
			$result=$Installer->runEngine();
			if ((isset($result['info']))&&(isset($result['info']['version_remote']))) {
				file_put_contents($file, $result['info']['version_remote']);
			} else {
				return '';
			}
		}

		return file_get_contents($file);
	}

	/**
	 * @param string $v1
	 * @param string $v2
	 * @return bool
	 */
	public static function checkUpdate(string $v1, string $v2):bool {
		return (version_compare($v1, $v2, '<'));
	}

}

?>