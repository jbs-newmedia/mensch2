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

$Core->setTitle('mensch² | Code');

require_once OSWMENSCH_CORE_ABSPATH.'php'.DIRECTORY_SEPARATOR.'header.inc.php';

use \JBSNewMedia\GitInstall\Installer;

$Installer=new Installer();
$Installer->setRealPath(OSWMENSCH_CORE_ABSPATH);
$Installer->setChmodFile(\osWMensch\Server\Configure::getValueAsInt('chmod_file'));
$Installer->setChmodDir(\osWMensch\Server\Configure::getValueAsInt('chmod_dir'));
$Installer->setLocalVersionFile('vendor'.DIRECTORY_SEPARATOR.'mensch2.json');
$Installer->setLocalRunningFile('vendor'.DIRECTORY_SEPARATOR.'mensch2.run');
$Installer->setName('jbsnewmedia/mensch2');
$Installer->setGit('github');
$Installer->setUrl('https://api.github.com/repos/jbs-newmedia/mensch2/releases');
$Installer->setRelease('stable');
$Installer->setRemotePath('source');
$Installer->setLocalPath('');
$Installer->setAction('execute');
$result=$Installer->runEngine();

?>

<div class="card shadow mb-4">
	<div class="card-header py-3">
		<h6 class="m-0 font-weight-bold text-primary">mensch² update</h6>
	</div>
	<div class="card-body">
		<?php echo $result['result_message'];?>
	</div>
</div>

<?php

require_once OSWMENSCH_CORE_ABSPATH.'php'.DIRECTORY_SEPARATOR.'footer.inc.php';

?>