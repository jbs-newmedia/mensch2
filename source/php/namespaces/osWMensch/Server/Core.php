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

}

?>