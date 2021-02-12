<?php

$Core->setTitle('Git pull | Code');

require_once OSWMENSCH_CORE_ABSPATH.'php'.DIRECTORY_SEPARATOR.'header.inc.php';

$cmd='cd '.\osWMensch\Server\Configure::getValueAsString('git_path').' && git pull';
$return=shell_exec($cmd);
if ($return===null) {
	$return='result is null, command: "'.$cmd.'"';
}

?>

<div class="card shadow mb-4">
	<div class="card-header py-3">
		<h6 class="m-0 font-weight-bold text-primary">Git pull</h6>
	</div>
	<div class="card-body">
		<?php echo nl2br($return);?>
	</div>
</div>

<?php

require_once OSWMENSCH_CORE_ABSPATH.'php'.DIRECTORY_SEPARATOR.'footer.inc.php';

?>