<?php

$Core->setTitle('Mensch update | Code');

require_once OSWMENSCH_CORE_ABSPATH.'php'.DIRECTORY_SEPARATOR.'header.inc.php';

$cmd='cd '.\osWMensch\Server\Configure::getValueAsString('git_mensch_path').' && git pull';
$return=shell_exec($cmd);
if ($return===null) {
	$return='result is null, command: "'.$cmd.'"';
} else {
	\osWMensch\Server\Configure::copyRecursive(str_replace(\osWMensch\Server\Configure::getValueAsString('mensch_path'), './', \osWMensch\Server\Configure::getValueAsString('git_mensch_path').'source/'), str_replace(\osWMensch\Server\Configure::getValueAsString('mensch_path'), '.', \osWMensch\Server\Configure::getValueAsString('mensch_path')));
}

?>

	<div class="card shadow mb-4">
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-primary">Mensch update</h6>
		</div>
		<div class="card-body">
			<?php echo nl2br($return);?>
		</div>
	</div>

<?php

require_once OSWMENSCH_CORE_ABSPATH.'php'.DIRECTORY_SEPARATOR.'footer.inc.php';

?>