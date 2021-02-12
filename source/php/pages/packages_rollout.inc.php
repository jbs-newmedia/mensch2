<?php

$Core->setTitle('Rollout | Packages');

require_once OSWMENSCH_CORE_ABSPATH.'php'.DIRECTORY_SEPARATOR.'header.inc.php';

$Server=new \osWMensch\Server\Server();
$server_list=$Server->getServerList();

#
#print_a($server_list);

if ($server_list!=[]) {
	foreach ($server_list as $server_id=>$server_details) {
		$Rollout=new \osWMensch\Server\Rollout($server_details, \osWMensch\Server\Configure::getValueAsArray('release'));
		if ($Rollout->connectServer()===true) {
			$server_list[$server_id]['status_by_start']=true;
			$Rollout->checkServer2Update();
			$Rollout->checkServerPackages2Update();

			if ($Rollout->disconnectServer()===true) {
				$server_list[$server_id]['status_by_finish']=true;
			} else {
				$server_list[$server_id]['status_by_finish']=false;
			}
		} else {
			$server_list[$server_id]['status_by_start']=false;
			$server_list[$server_id]['status_by_finish']=false;
		}

		?>
		<div class="card shadow mb-4">
			<div class="card-header py-3">
				<h6 class="m-0 font-weight-bold text-primary"><?php echo $server_list[$server_id]['server_name'] ?> | Rollout | Packages</h6>
			</div>
			<div class="card-body">
				<div class="alert alert-info mb-4">
					<strong>Info!</strong><br/><?php echo implode('<br/>', $Rollout->getLog()) ?>
				</div>
				<?php if ($Rollout->getPackageLog()!=[]): ?>
					<div class="table-responsive">
						<table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
							<thead>
							<tr>
								<th>Package</th>
								<th>Release</th>
								<th>Version</th>
								<th>Status</th>
							</tr>
							</thead>
							<tbody>
							<?php foreach ($Rollout->getPackageLog() as $package): ?>
								<tr>
									<td><?php echo $package['package'] ?></td>
									<td><?php echo $package['release'] ?></td>
									<?php if ($package['is_current']===true): ?>
										<td><span class="badge badge-success">up2date</span></td>
									<?php else: ?>
										<td><span class="badge badge-warning">out of date</span></td>
									<?php endif ?>
									<?php if ($package['status']==0): ?>
										<td><span class="badge badge-success">up2date</span></td>
									<?php elseif ($package['status']==1): ?>
										<td><span class="badge badge-warning">up2dated</span></td>
									<?php else: ?>
										<td><span class="badge badge-danger">error</span></td>
									<?php endif ?>
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
}

require_once OSWMENSCH_CORE_ABSPATH.'php'.DIRECTORY_SEPARATOR.'footer.inc.php';

?>