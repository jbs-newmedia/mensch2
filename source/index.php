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

if (PHP_VERSION_ID<70400) {
	die('This version of osWMensch requires PHP 7.4 or higher.<br/>You are currently running PHP '.phpversion().'.');
}

/**
 * Definieren des absoluten Pfads.
 */
define('OSWMENSCH_CORE_ABSPATH', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);

/**
 * DebugLib laden. print_a usw.
 */
require_once OSWMENSCH_CORE_ABSPATH.'php'.DIRECTORY_SEPARATOR.'printa.inc.php';

/**
 * Konfiguration initialisieren
 */
require_once OSWMENSCH_CORE_ABSPATH.'php'.DIRECTORY_SEPARATOR.'init.inc.php';

/**
 * Konfiguration laden
 */
require_once OSWMENSCH_CORE_ABSPATH.'php'.DIRECTORY_SEPARATOR.'configure.inc.php';

/**
 * Grundsystem laden
 */
require_once OSWMENSCH_CORE_ABSPATH.'php'.DIRECTORY_SEPARATOR.'core.inc.php';

/**
 * Page laden
 */
include OSWMENSCH_CORE_ABSPATH.'php'.DIRECTORY_SEPARATOR.'pages'.DIRECTORY_SEPARATOR.$Core->getPage().'.inc.php';

?>