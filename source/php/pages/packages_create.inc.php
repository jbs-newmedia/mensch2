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

$Core->setTitle('Create | Packages');

require_once OSWMENSCH_CORE_ABSPATH.'php'.DIRECTORY_SEPARATOR.'header.inc.php';

$Server=new \osWMensch\Server\Server();

$Packer=new \osWMensch\Server\Packer($Server->getServerList(), \osWMensch\Server\Configure::getValueAsArray('release'), \osWMensch\Server\Configure::getValueAsArray('prefix'));
$Packer->runPacker();

?>

	<div class="card shadow mb-4">
		<div class="card-header py-3">
			<h6 class="m-0 font-weight-bold text-primary">Create | Packages</h6>
		</div>
		<div class="card-body">
			<div class="alert alert-info mb-4">
				<strong>Info!</strong><br/><?php echo implode('<br/>', $Packer->getLog()) ?>
			</div>
			<div class="table-responsive">
				<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
					<thead>
					<tr>
						<th>Status</th>
						<th>Package</th>
						<th>Release</th>
						<th>FilelistJSON</th>
						<th>ChangelogJSON</th>
						<th>PackageJSON</th>
						<th>PackageZIP</th>
					</tr>
					</thead>
					<?php if ($Packer->getPackageLog()!=[]): ?>
						<tbody>
						<?php foreach ($Packer->getPackageLog() as $package): ?>
							<tr>
								<?php if ($package['package_check']===true): ?>
									<td><span class="badge badge-success">ok</span></td>
								<?php else: ?>
									<td><span class="badge badge-danger">error</span></td>
								<?php endif ?>
								<td><?php echo $package['package'] ?></td>
								<td><?php echo $package['release'] ?></td>
								<?php if ($package['filelist_update']!==true): ?>
									<td><span class="badge badge-success">up2date</span></td>
								<?php else: ?>
									<td><span class="badge badge-warning">updated</span></td>
								<?php endif ?>
								<?php if ($package['changelog_update']!==true): ?>
									<td><span class="badge badge-success">up2date</span></td>
								<?php else: ?>
									<td><span class="badge badge-warning">updated</span></td>
								<?php endif ?>
								<?php if ($package['package_update']!==true): ?>
									<td><span class="badge badge-success">up2date</span></td>
								<?php else: ?>
									<td><span class="badge badge-warning">updated</span></td>
								<?php endif ?>
								<?php if ($package['zip_update']!==true): ?>
									<td><span class="badge badge-success">up2date</span></td>
								<?php else: ?>
									<td><span class="badge badge-warning">updated</span></td>
								<?php endif ?>
							</tr>
						<?php endforeach ?>
						</tbody>
					<?php endif ?>
				</table>
			</div>
		</div>
	</div>
<?php

require_once OSWMENSCH_CORE_ABSPATH.'php'.DIRECTORY_SEPARATOR.'footer.inc.php';

?>