<?php

$Core->setTitle('Database | Settings');

require_once OSWMENSCH_CORE_ABSPATH.'php'.DIRECTORY_SEPARATOR.'header.inc.php';


if ((isset($_POST['doit']))&&($_POST['doit']=='yes')) {
	$DBStruct=new \osWMensch\Server\DBStruct();
	if ($DBStruct->checkTables()===true) {
		?>

		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<h6 class="m-0 font-weight-bold text-primary">Database | Settings</h6>
			</div>
			<div class="card-body">
				<div class="alert alert-success">
					<strong>Success!</strong><br/>database checked successfully.
				</div>
			</div>
		</div>

			<?php
	} else {
		?>

		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<h6 class="m-0 font-weight-bold text-primary">Database | Settings</h6>
			</div>
			<div class="card-body">
				<div class="alert alert-danger">
					<strong>Error!</strong><br/>database check failed.
				</div>
			</div>
		</div>

		<?php
	}

} else {

?>

	<div class="card shadow mb-4">
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-primary">Database | Settings</h6>
		</div>
		<div class="card-body">
			<form action="settings/database" method="post" enctype="multipart/form-data">
				<div class="form-group">
					<label for="sel1">Create/Update Database:</label>
					<select class="form-control" name="doit">
						<option>yes</option>
						<option>no</option>
					</select>
				</div>
				<input type="submit" class="btn btn-primary" value="Submit">
			</form>
		</div>
	</div>

<?php

}

require_once OSWMENSCH_CORE_ABSPATH.'php'.DIRECTORY_SEPARATOR.'footer.inc.php';

?>