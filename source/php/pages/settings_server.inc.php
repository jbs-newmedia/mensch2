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

$Core->setTitle('Server | Settings');

$Server=new \osWMensch\Server\Server();

if ((isset($_GET['action']))&&(in_array($_GET['action'], ['download']))) {

	if (isset($_POST['server_id'])) {
		$server_id=intval($_POST['server_id']);
	} elseif (isset($_GET['server_id'])) {
		$server_id=intval($_GET['server_id']);
	} else {
		$server_id=0;
	}

	$Server->downloadServer($server_id);
}

require_once OSWMENSCH_CORE_ABSPATH.'php'.DIRECTORY_SEPARATOR.'header.inc.php';

if ((isset($_GET['action']))&&(in_array($_GET['action'], ['add', 'doadd']))) {

	$server_details=[];
	$server_details['server_name']='';
	$server_details['server_rank']='';
	$server_details['server_url']='';
	$server_details['server_file']='';
	$server_details['server_secure']='';
	$server_details['server_token']='';
	$server_details['server_status']='';

	$error=[];

	if ($_GET['action']=='doadd') {
		if (isset($_POST['server_name'])) {
			$server_details['server_name']=$_POST['server_name'];
		}

		if (isset($_POST['server_rank'])) {
			$server_details['server_rank']=$_POST['server_rank'];
		}

		if (isset($_POST['server_url'])) {
			$server_details['server_url']=$_POST['server_url'];
		}

		if (isset($_POST['server_file'])) {
			$server_details['server_file']=$_POST['server_file'];
		}

		if (isset($_POST['server_secure'])) {
			$server_details['server_secure']=$_POST['server_secure'];
		}

		if (isset($_POST['server_token'])) {
			$server_details['server_token']=$_POST['server_token'];
		}

		if (isset($_POST['server_status'])) {
			$server_details['server_status']=$_POST['server_status'];
		}

		if (stristr($server_details['server_name'], 'osWFrame Release Server')===false) {
			$error[]='Name must contains "osWFrame Release Server".';
		}

		if ($server_details['server_rank']!==strval(intval($server_details['server_rank']))) {
			$error[]='Rank must be numeric.';
		} elseif (($server_details['server_rank']<0)||($server_details['server_rank']>100)) {
			$error[]='Rank must be between  0 and 100.';
		}

		if (\osWMensch\Server\Configure::verifyUrlIDNAPattern($server_details['server_url'])!==true) {
			$error[]='Url is invalid.';
		}

		if (\osWMensch\Server\Configure::verifyHash($server_details['server_secure'])!==true) {
			$error[]='Secure is invalid.';
		}

		if (\osWMensch\Server\Configure::verifyHash($server_details['server_token'])!==true) {
			$error[]='Token is invalid.';
		}

		if (!in_array($server_details['server_status'], [0, 1])) {
			$error[]='Status is invalid.';
		}

		if ($error!==[]) {
			$_GET['action']='add';
		} else {
			\osWMensch\Server\Server::createServer($server_details['server_name'], $server_details['server_rank'], $server_details['server_url'], $server_details['server_file'], $server_details['server_secure'], $server_details['server_token'], $server_details['server_status']);
		}
	}

	if ($_GET['action']=='doadd') {
		?>
		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<h6 class="m-0 font-weight-bold text-primary">Database | Settings</h6>
			</div>
			<div class="card-body">
				<div class="alert alert-success">
					<strong>Success!</strong><br/>Server created successfully.
				</div>
			</div>
		</div>
		<?php
	}

	if ($_GET['action']=='add') {
		?>
		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<h6 class="m-0 font-weight-bold text-primary">Add | Server | Settings</h6>
			</div>
			<div class="card-body">
				<?php if ($error!==[]): ?>
					<div class="alert alert-danger">
						<strong>Error!</strong><br/><?php echo implode('<br/>', $error) ?>
					</div>

				<?php endif ?>

				<form action="settings/server?action=doadd" method="post" enctype="multipart/form-data">
					<div class="form-group">
						<label for="server_name">Name:</label>
						<input type="text" class="form-control" name="server_name" value="<?php echo $server_details['server_name'] ?>">
					</div>
					<div class="form-group">
						<label for="server_rank">Rank:</label>
						<input type="text" class="form-control" name="server_rank" value="<?php echo $server_details['server_rank'] ?>">
					</div>
					<div class="form-group">
						<label for="server_url">URL:</label>
						<input type="text" class="form-control" name="server_url" value="<?php echo $server_details['server_url'] ?>">
					</div>
					<div class="form-group">
						<label for="server_file">File:</label>
						<input type="text" class="form-control" name="server_file" value="<?php echo $server_details['server_file'] ?>">
					</div>
					<div class="form-group">
						<label for="server_secure">Secure:</label>
						<input type="text" class="form-control" id="server_secure" name="server_secure" value="<?php echo $server_details['server_secure'] ?>">
					</div>
					<div class="form-group">
						<label for="server_token">Token:</label>
						<input type="text" class="form-control" id="server_token" name="server_token" value="<?php echo $server_details['server_token'] ?>">
					</div>
					<div class="form-group">
						<label for="sel1">Status:</label> <select class="form-control" name="server_status">
							<option value="1"<?php if ($server_details['server_status']==1): ?> selected<?php endif; ?>>active</option>
							<option value="0"<?php if ($server_details['server_status']==0): ?> selected<?php endif; ?>>inactive</option>
						</select>
					</div>
					<input type="submit" class="btn btn-primary" value="Submit">
					<a href="javascript:generateHash()" class="float-right btn btn-primary">generate toke/secure</a>
				</form>
			</div>
		</div>
		<?php

	}

} elseif ((isset($_GET['action']))&&(in_array($_GET['action'], ['edit', 'doedit']))) {

	if (isset($_POST['server_id'])) {
		$server_id=intval($_POST['server_id']);
	} elseif (isset($_GET['server_id'])) {
		$server_id=intval($_GET['server_id']);
	} else {
		$server_id=0;
	}

	$server_details=$Server->getServerDetails($server_id);

	$server_details_new=[];
	$server_details_new['server_name']='';
	$server_details_new['server_version']='';
	$server_details_new['server_rank']='';
	$server_details_new['server_url']='';
	$server_details_new['server_file']='';
	$server_details_new['server_secure']='';
	$server_details_new['server_token']='';
	$server_details_new['server_status']='';
	$server_details_new['server_lastconnect']='';

	$error=[];

	if ($_GET['action']=='doedit') {
		if (isset($_POST['server_name'])) {
			$server_details_new['server_name']=$_POST['server_name'];
		}

		if (isset($_POST['server_version'])) {
			$server_details_new['server_version']=$_POST['server_version'];
		}

		if (isset($_POST['server_rank'])) {
			$server_details_new['server_rank']=$_POST['server_rank'];
		}

		if (isset($_POST['server_url'])) {
			$server_details_new['server_url']=$_POST['server_url'];
		}

		if (isset($_POST['server_file'])) {
			$server_details_new['server_file']=$_POST['server_file'];
		}

		if (isset($_POST['server_secure'])) {
			$server_details_new['server_secure']=$_POST['server_secure'];
		}

		if (isset($_POST['server_token'])) {
			$server_details_new['server_token']=$_POST['server_token'];
		}

		if (isset($_POST['server_status'])) {
			$server_details_new['server_status']=$_POST['server_status'];
		}

		if (isset($_POST['server_lastconnect'])) {
			$server_details_new['server_lastconnect']=$_POST['server_lastconnect'];
		}

		if ($server_details==[]) {
			$error[]='Server doesn\'t exists.';
		}

		if (stristr($server_details_new['server_name'], 'osWFrame Release Server')===false) {
			$error[]='Name must contains "osWFrame Release Server".';
		}

		if ($server_details_new['server_rank']!==strval(intval($server_details_new['server_rank']))) {
			$error[]='Rank must be numeric.';
		} elseif (($server_details_new['server_rank']<0)||($server_details_new['server_rank']>100)) {
			$error[]='Rank must be between  0 and 100.';
		}

		if (\osWMensch\Server\Configure::verifyUrlIDNAPattern($server_details_new['server_url'])!==true) {
			$error[]='Url is invalid.';
		}

		if (\osWMensch\Server\Configure::verifyHash($server_details_new['server_secure'])!==true) {
			$error[]='Secure is invalid.';
		}

		if (\osWMensch\Server\Configure::verifyHash($server_details_new['server_token'])!==true) {
			$error[]='Token is invalid.';
		}

		if (!in_array($server_details_new['server_status'], [0, 1])) {
			$error[]='Status is invalid.';
		}

		if ($error!==[]) {
			$_GET['action']='edit';
		} else {
			\osWMensch\Server\Server::updateServer($server_id, $server_details_new['server_name'], $server_details_new['server_version'], $server_details_new['server_rank'], $server_details_new['server_url'], $server_details_new['server_file'], $server_details_new['server_secure'], $server_details_new['server_token'], $server_details_new['server_status'], $server_details_new['server_lastconnect']);
		}
	}

	if ($_GET['action']=='doedit') {
		?>
		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<h6 class="m-0 font-weight-bold text-primary">Edit | Server | Settings</h6>
			</div>
			<div class="card-body">
				<div class="alert alert-success">
					<strong>Success!</strong><br/>Server edited successfully.
				</div>
			</div>
		</div>
		<?php
	}

	if (($_GET['action']=='edit')&&($server_details!==[])) {
		?>
		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<h6 class="m-0 font-weight-bold text-primary">Edit | Server | Settings</h6>
			</div>
			<div class="card-body">
				<?php if ($error!==[]): ?>
					<div class="alert alert-danger">
						<strong>Error!</strong><br/><?php echo implode('<br/>', $error) ?>
					</div>

				<?php endif ?>

				<form action="settings/server?action=doedit" method="post" enctype="multipart/form-data">
					<div class="form-group">
						<label for="server_name">Name:</label>
						<input type="text" class="form-control" name="server_name" value="<?php echo $server_details['server_name'] ?>">
					</div>
					<div class="form-group">
						<label for="server_name">Version:</label>
						<input type="text" class="form-control" name="server_version" value="<?php echo $server_details['server_version'] ?>">
					</div>
					<div class="form-group">
						<label for="server_rank">Rank:</label>
						<input type="text" class="form-control" name="server_rank" value="<?php echo $server_details['server_rank'] ?>">
					</div>
					<div class="form-group">
						<label for="server_url">URL:</label>
						<input type="text" class="form-control" name="server_url" value="<?php echo $server_details['server_url'] ?>">
					</div>
					<div class="form-group">
						<label for="server_file">File:</label>
						<input type="text" class="form-control" name="server_file" value="<?php echo $server_details['server_file'] ?>">
					</div>
					<div class="form-group">
						<label for="server_secure">Secure:</label>
						<input type="text" class="form-control" id="server_secure" name="server_secure" value="<?php echo $server_details['server_secure'] ?>">
					</div>
					<div class="form-group">
						<label for="server_token">Token:</label>
						<input type="text" class="form-control" id="server_token" name="server_token" value="<?php echo $server_details['server_token'] ?>">
					</div>
					<div class="form-group">
						<label for="sel1">Status:</label> <select class="form-control" name="server_status">
							<option value="1"<?php if ($server_details['server_status']==1): ?> selected<?php endif; ?>>active</option>
							<option value="0"<?php if ($server_details['server_status']==0): ?> selected<?php endif; ?>>inactive</option>
						</select>
					</div>
					<div class="form-group">
						<label for="server_name">Last connected:</label>
						<input type="text" class="form-control" name="server_lastconnect" value="<?php echo $server_details['server_lastconnect'] ?>">
					</div>
					<input type="submit" class="btn btn-primary" value="Submit">
					<a href="javascript:generateHash()" class="float-right btn btn-primary">generate toke/secure</a>
					<input type="hidden" name="server_id" value="<?php echo $server_id ?>">
				</form>
			</div>
		</div>
		<?php

	} elseif ($_GET['action']!='doedit') {
		?>
		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<h6 class="m-0 font-weight-bold text-primary">Edit | Server | Settings</h6>
			</div>
			<div class="card-body">
				<div class="alert alert-danger">
					<strong>Error!</strong><br/>Server doesn't exists.
				</div>
			</div>
		</div>
		<?php
	}

} elseif ((isset($_GET['action']))&&(in_array($_GET['action'], ['delete', 'dodelete']))) {

	if (isset($_POST['server_id'])) {
		$server_id=intval($_POST['server_id']);
	} elseif (isset($_GET['server_id'])) {
		$server_id=intval($_GET['server_id']);
	} else {
		$server_id=0;
	}

	$server_details=$Server->getServerDetails($server_id);

	$error=[];

	if ($_GET['action']=='dodelete') {
		if ($server_details==[]) {
			$error[]='Server doesn\'t exists.';
		}

		if ($error!==[]) {
			$_GET['action']='delete';
		} else {
			\osWMensch\Server\Server::deleteServer($server_id);
		}
	}

	if ($_GET['action']=='dodelete') {
		?>
		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<h6 class="m-0 font-weight-bold text-primary">Delete | Server | Settings</h6>
			</div>
			<div class="card-body">
				<div class="alert alert-success">
					<strong>Success!</strong><br/>Server deleted successfully.
				</div>
			</div>
		</div>
		<?php
	}

	if (($_GET['action']=='delete')&&($server_details!==[])) {
		?>
		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<h6 class="m-0 font-weight-bold text-primary">Delete | Server | Settings</h6>
			</div>
			<div class="card-body">
				<?php if ($error!==[]): ?>
					<div class="alert alert-danger">
						<strong>Error!</strong><br/><?php echo implode('<br/>', $error) ?>
					</div>

				<?php endif ?>

				<form action="settings/server?action=dodelete" method="post" enctype="multipart/form-data">
					<div class="form-group">
						<label for="server_name">Name:</label>
						<div class="form-control"><?php echo $server_details['server_name'] ?></div>
					</div>
					<div class="form-group">
						<label for="server_name">Version:</label>
						<div class="form-control"><?php echo $server_details['server_version'] ?></div>
					</div>
					<div class="form-group">
						<label for="server_rank">Rank:</label>
						<div class="form-control"><?php echo $server_details['server_rank'] ?></div>
					</div>
					<div class="form-group">
						<label for="server_url">URL:</label>
						<div class="form-control"><?php echo $server_details['server_url'] ?></div>
					</div>
					<div class="form-group">
						<label for="server_file">File:</label>
						<div class="form-control"><?php echo $server_details['server_file'] ?></div>
					</div>
					<div class="form-group">
						<label for="server_secure">Secure:</label>
						<div class="form-control"><?php echo $server_details['server_secure'] ?></div>
					</div>
					<div class="form-group">
						<label for="server_token">Token:</label>
						<div class="form-control"><?php echo $server_details['server_token'] ?></div>
					</div>
					<div class="form-group">
						<label for="server_token">Status:</label>
						<div class="form-control"><?php if ($server_details['server_status']==1): ?>active<?php endif; ?><?php if ($server_details['server_status']==0): ?>inactive<?php endif; ?></div>
					</div>
					<div class="form-group">
						<label for="server_name">Last connected:</label>
						<div class="form-control"><?php echo $server_details['server_lastconnect'] ?></div>
					</div>
					<input type="submit" class="btn btn-danger" value="Submit">
					<input type="hidden" name="server_id" value="<?php echo $server_id ?>">
				</form>
			</div>
		</div>
		<?php

	} elseif ($_GET['action']!='dodelete') {
		?>
		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<h6 class="m-0 font-weight-bold text-primary">Delete | Server | Settings</h6>
			</div>
			<div class="card-body">
				<div class="alert alert-danger">
					<strong>Error!</strong><br/>Server doesn't exists.
				</div>
			</div>
		</div>
		<?php
	}

} else {
	?>

	<div class="card shadow mb-4">
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-primary">Server | Settings</h6>
		</div>
		<div class="card-body">
			<a href="settings/server?action=add" class="mb-4 btn btn-primary">Create Server</a>
			<?php if ($Server->getServerList()!=[]): ?>
			<div class="table-responsive">
				<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
					<thead>
					<tr>
						<th>Name</th>
						<th>Version</th>
						<th>Rank</th>
						<th>URL</th>
						<th>File</th>
						<th>Status</th>
						<th>Last connected</th>
						<th>Options</th>
					</tr>
					</thead>
						<tbody>
						<?php foreach ($Server->getServerList() as $server): ?>
							<tr>
								<td><?php echo $server['server_name'] ?></td>
								<?php if ($server['server_version']=='0'): ?>
									<td>unchecked</td>
								<?php else: ?>
									<td><?php echo $server['server_version'] ?></td>
								<?php endif ?>
								<td><?php echo $server['server_rank'] ?></td>
								<td><?php echo $server['server_url'] ?></td>
								<td><?php echo $server['server_file'] ?></td>
								<?php if ($server['server_status']==1): ?>
									<td><span class="badge badge-success">active</span></td>
								<?php else: ?>
									<td><span class="badge badge-danger">inactive</span></td>
								<?php endif ?>
								<?php if ($server['server_lastconnect']==0): ?>
									<td>never</td>
								<?php else: ?>
									<td><?php echo date('Y.m.d H:i:s', $server['server_lastconnect']) ?></td>
								<?php endif ?>
								<td>
									<a href="settings/server?action=edit&server_id=<?php echo $server['server_id'] ?>" class="badge badge-primary">Edit</a>
									<a target="_blank" href="settings/server?action=download&server_id=<?php echo $server['server_id'] ?>" class="badge badge-primary">Download</a>
									<a href="settings/server?action=delete&server_id=<?php echo $server['server_id'] ?>" class="badge badge-danger">Delete</a>
								</td>
							</tr>
						<?php endforeach ?>
						</tbody>
				</table>
			</div>
			<?php endif ?>
		</div>
	</div>

	<?php
}

require_once OSWMENSCH_CORE_ABSPATH.'php'.DIRECTORY_SEPARATOR.'footer.inc.php';

?>