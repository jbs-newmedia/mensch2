<?php

$Core->setTitle('Mensch update | Code');

require_once OSWMENSCH_CORE_ABSPATH.'php'.DIRECTORY_SEPARATOR.'header.inc.php';

#print_a(strtotime('Thu Feb 11 12:37:39 2021 +0100'));


#'git reset --hard';
#$cmd='cd '.\osWMensch\Server\Configure::getValueAsString('git_path').' && git log -- source/oswframe.cookie';

$cmd='cd '.\osWMensch\Server\Configure::getValueAsString('git_mensch_path').' && git pull && cp -R source/* ../';
$return=shell_exec($cmd);
if ($return===null) {
	$return='result is null, command: "'.$cmd.'"';
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