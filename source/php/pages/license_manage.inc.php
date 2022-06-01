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

$Core->setTitle('Manage | License');

$License=new \osWMensch\Server\License();

if ((isset($_GET['action']))&&(in_array($_GET['action'], ['download']))) {

	if (isset($_POST['license_id'])) {
		$license_id=intval($_POST['license_id']);
	} elseif (isset($_GET['license_id'])) {
		$license_id=intval($_GET['license_id']);
	} else {
		$license_id=0;
	}

	$License->downloadLicense($license_id);
}

if ((!isset($_GET['action']))||(!in_array($_GET['action'], ['doassign']))) {
	require_once OSWMENSCH_CORE_ABSPATH.'php'.DIRECTORY_SEPARATOR.'header.inc.php';
}

if ((isset($_GET['action']))&&(in_array($_GET['action'], ['add', 'doadd']))) {

	$license_details=[];
	$license_details['license_name']='';
	$license_details['license_description']='';
	$license_details['license_server_name']='';
	$license_details['license_server_address']='';
	$license_details['license_server_mac']='';
	$license_details['license_key']='';
	$license_details['license_status']='';

	$error=[];

	if ($_GET['action']=='doadd') {
		if (isset($_POST['license_name'])) {
			$license_details['license_name']=$_POST['license_name'];
		}

		if (isset($_POST['license_description'])) {
			$license_details['license_description']=$_POST['license_description'];
		}

		if (isset($_POST['license_server_name'])) {
			$license_details['license_server_name']=$_POST['license_server_name'];
		}

		if (isset($_POST['license_server_address'])) {
			$license_details['license_server_address']=$_POST['license_server_address'];
		}

		if (isset($_POST['license_server_mac'])) {
			$license_details['license_server_mac']=$_POST['license_server_mac'];
		}

		if (isset($_POST['license_key'])) {
			$license_details['license_key']=$_POST['license_key'];
		}

		if (isset($_POST['license_status'])) {
			$license_details['license_status']=$_POST['license_status'];
		}

		if ((strlen($license_details['license_name'])<=6)||(strlen($license_details['license_name'])>128)) {
			$error[]='Name is to short/long';
		}

		if ((strlen($license_details['license_description'])<=6)||(strlen($license_details['license_description'])>128)) {
			$error[]='Description is to short/long';
		}

		if (!in_array($license_details['license_status'], [0, 1])) {
			$error[]='Status is invalid.';
		}

		if ($error!==[]) {
			$_GET['action']='add';
		} else {
			\osWMensch\Server\License::createLicense($license_details['license_name'], $license_details['license_description'], $license_details['license_server_name'], $license_details['license_server_address'], $license_details['license_server_mac'], $license_details['license_key'], $license_details['license_status']);
		}
	}

	if ($_GET['action']=='doadd') {
		?>
		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<h6 class="m-0 font-weight-bold text-primary">Manage | License</h6>
			</div>
			<div class="card-body">
				<div class="alert alert-success">
					<strong>Success!</strong><br/>License created successfully.
				</div>
			</div>
		</div>
		<?php
	}

	if ($_GET['action']=='add') {
		?>
		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<h6 class="m-0 font-weight-bold text-primary">Add | Manage | License</h6>
			</div>
			<div class="card-body">
				<?php if ($error!==[]): ?>
					<div class="alert alert-danger">
						<strong>Error!</strong><br/><?php echo implode('<br/>', $error) ?>
					</div>

				<?php endif ?>

				<form action="license/manage?action=doadd" method="post" enctype="multipart/form-data">
					<div class="form-group">
						<label for="license_name">Name:</label>
						<input type="text" class="form-control" name="license_name" value="<?php echo $license_details['license_name'] ?>">
					</div>
					<div class="form-group">
						<label for="license_description">Description:</label>
						<input type="text" class="form-control" name="license_description" value="<?php echo $license_details['license_description'] ?>">
					</div>
					<div class="form-group">
						<label for="license_server_name">Server-Name:</label>
						<input type="text" class="form-control" name="license_server_name" value="<?php echo $license_details['license_server_name'] ?>">
					</div>
					<div class="form-group">
						<label for="license_server_address">Server-Address:</label>
						<input type="text" class="form-control" name="license_server_address" value="<?php echo $license_details['license_server_address'] ?>">
					</div>
					<div class="form-group">
						<label for="license_server_mac">Server-MAC:</label>
						<input type="text" class="form-control" name="license_server_mac" value="<?php echo $license_details['license_server_mac'] ?>">
					</div>
					<div class="form-group">
						<label for="license_key">Key:</label>
						<input type="text" class="form-control" name="license_key" value="<?php echo $license_details['license_key'] ?>">
					</div>
					<div class="form-group">
						<label for="sel1">Status:</label> <select class="form-control" name="license_status">
							<option value="1"<?php if ($license_details['license_status']==1): ?> selected<?php endif; ?>>active</option>
							<option value="0"<?php if ($license_details['license_status']==0): ?> selected<?php endif; ?>>inactive</option>
						</select>
					</div>
					<input type="submit" class="btn btn-primary" value="Submit">
				</form>
			</div>
		</div>
		<?php

	}

} elseif ((isset($_GET['action']))&&(in_array($_GET['action'], ['edit', 'doedit']))) {

	if (isset($_POST['license_id'])) {
		$license_id=intval($_POST['license_id']);
	} elseif (isset($_GET['license_id'])) {
		$license_id=intval($_GET['license_id']);
	} else {
		$license_id=0;
	}

	$license_details=$License->getLicenseDetails($license_id);

	$license_details_new=[];
	$license_details_new['license_name']='';
	$license_details_new['license_version']='';
	$license_details_new['license_rank']='';
	$license_details_new['license_url']='';
	$license_details_new['license_file']='';
	$license_details_new['license_secure']='';
	$license_details_new['license_token']='';
	$license_details_new['license_status']='';
	$license_details_new['license_lastconnect']='';

	$error=[];

	if ($_GET['action']=='doedit') {
		if (isset($_POST['license_name'])) {
			$license_details_new['license_name']=$_POST['license_name'];
		}

		if (isset($_POST['license_description'])) {
			$license_details_new['license_description']=$_POST['license_description'];
		}

		if (isset($_POST['license_server_name'])) {
			$license_details_new['license_server_name']=$_POST['license_server_name'];
		}

		if (isset($_POST['license_server_address'])) {
			$license_details_new['license_server_address']=$_POST['license_server_address'];
		}

		if (isset($_POST['license_server_mac'])) {
			$license_details_new['license_server_mac']=$_POST['license_server_mac'];
		}

		if (isset($_POST['license_key'])) {
			$license_details_new['license_key']=$_POST['license_key'];
		}

		if (isset($_POST['license_status'])) {
			$license_details_new['license_status']=$_POST['license_status'];
		}

		if ((strlen($license_details_new['license_name'])<=6)||(strlen($license_details_new['license_name'])>128)) {
			$error[]='Name is to short/long';
		}

		if ((strlen($license_details_new['license_description'])<=6)||(strlen($license_details_new['license_description'])>128)) {
			$error[]='Description is to short/long';
		}

		if (!in_array($license_details_new['license_status'], [0, 1])) {
			$error[]='Status is invalid.';
		}

		if ($error!==[]) {
			$_GET['action']='edit';
		} else {
			\osWMensch\Server\License::updateLicense($license_id, $license_details_new['license_name'], $license_details_new['license_description'], $license_details_new['license_server_name'], $license_details_new['license_server_address'], $license_details_new['license_server_mac'], $license_details_new['license_key'], $license_details_new['license_status']);
		}
	}

	if ($_GET['action']=='doedit') {
		?>
		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<h6 class="m-0 font-weight-bold text-primary">Edit | Manage | License</h6>
			</div>
			<div class="card-body">
				<div class="alert alert-success">
					<strong>Success!</strong><br/>License edited successfully.
				</div>
			</div>
		</div>
		<?php
	}

	if (($_GET['action']=='edit')&&($license_details!==[])) {
		?>
		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<h6 class="m-0 font-weight-bold text-primary">Edit | Manage | License</h6>
			</div>
			<div class="card-body">
				<?php if ($error!==[]): ?>
					<div class="alert alert-danger">
						<strong>Error!</strong><br/><?php echo implode('<br/>', $error) ?>
					</div>

				<?php endif ?>

				<form action="license/manage?action=doedit" method="post" enctype="multipart/form-data">
					<div class="form-group">
						<label for="license_name">Name:</label>
						<input type="text" class="form-control" name="license_name" value="<?php echo $license_details['license_name'] ?>">
					</div>
					<div class="form-group">
						<label for="license_description">Description:</label>
						<input type="text" class="form-control" name="license_description" value="<?php echo $license_details['license_description'] ?>">
					</div>
					<div class="form-group">
						<label for="license_server_name">Server-Name:</label>
						<input type="text" class="form-control" name="license_server_name" value="<?php echo $license_details['license_server_name'] ?>">
					</div>
					<div class="form-group">
						<label for="license_server_address">Server-Address:</label>
						<input type="text" class="form-control" name="license_server_address" value="<?php echo $license_details['license_server_address'] ?>">
					</div>
					<div class="form-group">
						<label for="license_server_mac">Server-MAC:</label>
						<input type="text" class="form-control" name="license_server_mac" value="<?php echo $license_details['license_server_mac'] ?>">
					</div>
					<div class="form-group">
						<label for="license_key">Key:</label>
						<input type="text" class="form-control" name="license_key" value="<?php echo $license_details['license_key'] ?>">
					</div>
					<div class="form-group">
						<label for="sel1">Status:</label> <select class="form-control" name="license_status">
							<option value="1"<?php if ($license_details['license_status']==1): ?> selected<?php endif; ?>>active</option>
							<option value="0"<?php if ($license_details['license_status']==0): ?> selected<?php endif; ?>>inactive</option>
						</select>
					</div>
					<input type="submit" class="btn btn-primary" value="Submit">
					<input type="hidden" name="license_id" value="<?php echo $license_id ?>">
				</form>
			</div>
		</div>
		<?php

	} elseif ($_GET['action']!='doedit') {
		?>
		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<h6 class="m-0 font-weight-bold text-primary">Edit | Manage | License</h6>
			</div>
			<div class="card-body">
				<div class="alert alert-danger">
					<strong>Error!</strong><br/>License doesn't exists.
				</div>
			</div>
		</div>
		<?php
	}

} elseif ((isset($_GET['action']))&&(in_array($_GET['action'], ['delete', 'dodelete']))) {

	if (isset($_POST['license_id'])) {
		$license_id=intval($_POST['license_id']);
	} elseif (isset($_GET['license_id'])) {
		$license_id=intval($_GET['license_id']);
	} else {
		$license_id=0;
	}

	$license_details=$License->getLicenseDetails($license_id);

	$error=[];

	if ($_GET['action']=='dodelete') {
		if ($license_details==[]) {
			$error[]='License doesn\'t exists.';
		}

		if ($error!==[]) {
			$_GET['action']='delete';
		} else {
			\osWMensch\Server\License::deleteLicense($license_id);
		}
	}

	if ($_GET['action']=='dodelete') {
		?>
		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<h6 class="m-0 font-weight-bold text-primary">Delete | Manage | License</h6>
			</div>
			<div class="card-body">
				<div class="alert alert-success">
					<strong>Success!</strong><br/>License deleted successfully.
				</div>
			</div>
		</div>
		<?php
	}

	if (($_GET['action']=='delete')&&($license_details!==[])) {
		?>
		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<h6 class="m-0 font-weight-bold text-primary">Delete | Manage | License</h6>
			</div>
			<div class="card-body">
				<?php if ($error!==[]): ?>
					<div class="alert alert-danger">
						<strong>Error!</strong><br/><?php echo implode('<br/>', $error) ?>
					</div>

				<?php endif ?>

				<form action="license/manage?action=dodelete" method="post" enctype="multipart/form-data">
					<div class="form-group">
						<label for="license_name">Name:</label>
						<div class="form-control"><?php echo $license_details['license_name'] ?></div>
					</div>
					<div class="form-group">
						<label for="license_description">Description:</label>
						<div class="form-control"><?php echo $license_details['license_description'] ?></div>
					</div>
					<div class="form-group">
						<label for="license_server_name">Server-Name:</label>
						<div class="form-control"><?php echo $license_details['license_server_name'] ?></div>
					</div>
					<div class="form-group">
						<label for="license_server_address">Server-Address:</label>
						<div class="form-control"><?php echo $license_details['license_server_address'] ?></div>
					</div>
					<div class="form-group">
						<label for="license_server_mac">Server-MAC:</label>
						<div class="form-control"><?php echo $license_details['license_server_mac'] ?></div>
					</div>
					<div class="form-group">
						<label for="license_key">Key:</label>
						<div class="form-control"><?php echo $license_details['license_key'] ?></div>
					</div>
					<div class="form-group">
						<label for="license_token">Status:</label>
						<div class="form-control"><?php if ($license_details['license_status']==1): ?>active<?php endif; ?><?php if ($license_details['license_status']==0): ?>inactive<?php endif; ?></div>
					</div>
					<input type="submit" class="btn btn-danger" value="Submit">
					<input type="hidden" name="license_id" value="<?php echo $license_id ?>">
				</form>
			</div>
		</div>
		<?php

	} elseif ($_GET['action']!='dodelete') {
		?>
		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<h6 class="m-0 font-weight-bold text-primary">Delete | Manage | License</h6>
			</div>
			<div class="card-body">
				<div class="alert alert-danger">
					<strong>Error!</strong><br/>License doesn't exists.
				</div>
			</div>
		</div>
		<?php
	}

} elseif ((isset($_GET['action']))&&(in_array($_GET['action'], ['assign', 'doassign']))) {

	if (isset($_POST['license_id'])) {
		$license_id=intval($_POST['license_id']);
	} elseif (isset($_GET['license_id'])) {
		$license_id=intval($_GET['license_id']);
	} else {
		$license_id=0;
	}

	$license_details=$License->getLicenseDetails($license_id);
	$licensepackage=$License->getLicensePackageList($license_id);

	if (($_GET['action']=='doassign')&&($License->getPackageList()!==[])) {
		if (isset($_POST['lpackage'])) {
			$package=$_POST['lpackage'];
		} else {
			die('error #1');
		}

		if (isset($_POST['status'])) {
			$status=$_POST['status'];
		} else {
			die('error #2');
		}

		$licensepackage=$License->getPackageList();
		if (!isset($licensepackage[$package])){
			die('error #3');
		}

		$package=$licensepackage[$package];

		if ($status=='1') {
			$License->addPackage2License($license_id, $package);
			die('success');
		} elseif ($status=='0') {
			$License->removePackage2License($license_id, $package);
			die('success');
		}
		die('error #4');
	}

	if (($_GET['action']=='assign')&&($License->getPackageList()!==[])) {
		?>
		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<h6 class="m-0 font-weight-bold text-primary">Assign | Manage | License</h6>
			</div>
			<div class="card-body">

				<?php if ($License->getPackageList()!=[]): ?>
				<h4>License: <?php echo $license_details['license_name']?></h4>
					<div class="table-responsive">
						<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
							<thead>
							<tr>
								<th>Name</th>
								<th>Status</th>
							</tr>
							</thead>
							<tbody>
							<?php foreach ($License->getPackageList() as $package): $package_md5=md5($package); ?>
								<tr>
									<td><?php echo $package ?></td>
									<td>
										<input type="hidden" name="<?php echo $package_md5 ?>" id="<?php echo $package_md5 ?>" value="<?php echo intval(in_array($package, $licensepackage)) ?>">
										<?php if (in_array($package, $licensepackage)): ?>
											<a href="javascript:changeLicenseStatus('<?php echo $license_id?>', '<?php echo $package_md5 ?>', 0)" id="<?php echo $package_md5 ?>_badge" class="badge badge-success" style="width:100px">Activ</a>
										<?php else: ?>
											<a href="javascript:changeLicenseStatus('<?php echo $license_id?>', '<?php echo $package_md5 ?>', 1)" id="<?php echo $package_md5 ?>_badge" class="badge badge-danger" style="width:100px">Inactive</a>
										<?php endif ?>
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

} else {
	?>

	<div class="card shadow mb-4">
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-primary">Manage | License</h6>
		</div>
		<div class="card-body">
			<a href="license/manage?action=add" class="mb-4 btn btn-primary">Create License</a>
			<?php if ($License->getLicenseList()!=[]): ?>
				<div class="table-responsive">
					<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
						<thead>
						<tr>
							<th>Name</th>
							<th>Description</th>
							<th>Server-Name</th>
							<th>Server-Address</th>
							<th>Server-Mac</th>
							<th>Key</th>
							<th>Status</th>
							<th>Options</th>
						</tr>
						</thead>
						<tbody>
						<?php foreach ($License->getLicenseList() as $license): ?>
							<tr>
								<td><?php echo $license['license_name'] ?></td>
								<td><?php echo $license['license_description'] ?></td>
								<td><?php echo $license['license_server_name'] ?></td>
								<td><?php echo $license['license_server_address'] ?></td>
								<td><?php echo $license['license_server_mac'] ?></td>
								<td><?php echo $license['license_key'] ?></td>
								<?php if ($license['license_status']==1): ?>
									<td><span class="badge badge-success">active</span></td>
								<?php else: ?>
									<td><span class="badge badge-danger">inactive</span></td>
								<?php endif ?>
								<td>
									<a href="license/manage?action=edit&license_id=<?php echo $license['license_id'] ?>" class="badge badge-primary">Edit</a>
									<a href="license/manage?action=assign&license_id=<?php echo $license['license_id'] ?>" class="badge badge-primary">Assign</a>
									<a href="license/manage?action=delete&license_id=<?php echo $license['license_id'] ?>" class="badge badge-danger">Delete</a>
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