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

$Core->setTitle('Git reset | Code');

require_once OSWMENSCH_CORE_ABSPATH.'php'.DIRECTORY_SEPARATOR.'header.inc.php';

$cmd='rm -R '.\osWMensch\Server\Configure::getValueAsString('git_path').'source && cd '.\osWMensch\Server\Configure::getValueAsString('git_path').' && git reset --hard HEAD && git pull';
$return=shell_exec($cmd);
if ($return===null) {
	$return='result is null, command: "'.$cmd.'"';
}

?>

<div class="card shadow mb-4">
	<div class="card-header py-3">
		<h6 class="m-0 font-weight-bold text-primary">Git reset</h6>
	</div>
	<div class="card-body">
		<?php echo nl2br($return);?>
	</div>
</div>

<?php

require_once OSWMENSCH_CORE_ABSPATH.'php'.DIRECTORY_SEPARATOR.'footer.inc.php';

?>