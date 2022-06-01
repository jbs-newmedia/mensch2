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

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title><?php echo $Core->getTitle();?> | Mensch²</title>
	<base href="<?php echo \osWMensch\Server\Configure::getValueAsString('mensch_url').\osWMensch\Server\Configure::getValueAsString('mensch_url_path')?>">
	<meta name="description" content="Mensch²">
	<meta name="author" content="Juergen Schwind | oswframe.com">
	<link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
	<link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
	<link href="css/sb-admin-2-mensch.min.css" rel="stylesheet">

</head>
<body id="page-top">
<div id="wrapper">
	<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">
		<a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard">
			<div class="sidebar-brand-icon rotate-n-15">
				<i class="fas fa-fingerprint"></i>
			</div>
			<div class="sidebar-brand-text mx-3">Mensch²</div>
		</a>
		<hr class="sidebar-divider my-0">
		<li class="nav-item<?php if($Core->isActivePage('dashboard')===true):?> active<?php endif?>">
			<a class="nav-link" href="dashboard">
				<i class="fas fa-fw fa-tachometer-alt"></i>
				<span>Dashboard</span></a>
		</li>
		<hr class="sidebar-divider my-0">
		<li class="nav-item<?php if($Core->isActivePage('code')===true):?> active<?php endif?>">
			<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseCode"
			   aria-expanded="true" aria-controls="collapseCode">
				<i class="fas fa-code"></i>
				<span>Code</span>
			</a>
			<div id="collapseCode" class="collapse<?php if($Core->isActivePage('code')===true):?> show<?php endif?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
				<div class="bg-white py-2 collapse-inner rounded">
					<a class="collapse-item<?php if($Core->isActivePage('code_git_pull')===true):?> active<?php endif?>" href="code/git_pull">Git pull</a>
					<a class="collapse-item<?php if($Core->isActivePage('code_git_reset')===true):?> active<?php endif?>" href="code/git_reset">Git reset</a>
					<a class="collapse-item<?php if($Core->isActivePage('code_mensch2')===true):?> active<?php endif?>" href="code/mensch2">Mensch update</a>
				</div>
			</div>
		</li>
		<hr class="sidebar-divider my-0">
		<li class="nav-item<?php if($Core->isActivePage('license')===true):?> active<?php endif?>">
			<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseLicense"
			   aria-expanded="true" aria-controls="collapseLicense">
				<i class="fas fa-certificate"></i>
				<span>License</span>
			</a>
			<div id="collapseLicense" class="collapse<?php if($Core->isActivePage('license')===true):?> show<?php endif?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
				<div class="bg-white py-2 collapse-inner rounded">
					<a class="collapse-item<?php if($Core->isActivePage('license_manage')===true):?> active<?php endif?>" href="license/manage">Manage</a>
					<a class="collapse-item<?php if($Core->isActivePage('license_rollout')===true):?> active<?php endif?>" href="license/rollout">Rollout</a>
				</div>
			</div>
		</li>
		<hr class="sidebar-divider my-0">
		<li class="nav-item<?php if($Core->isActivePage('packages')===true):?> active<?php endif?>">
			<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePackages"
			   aria-expanded="true" aria-controls="collapsePackages">
				<i class="fas fa-archive"></i>
				<span>Packages</span>
			</a>
			<div id="collapsePackages" class="collapse<?php if($Core->isActivePage('packages')===true):?> show<?php endif?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
				<div class="bg-white py-2 collapse-inner rounded">
					<a class="collapse-item<?php if($Core->isActivePage('packages_create')===true):?> active<?php endif?>" href="packages/create">Create</a>
					<a class="collapse-item<?php if($Core->isActivePage('packages_rollout')===true):?> active<?php endif?>" href="packages/rollout">Rollout</a>
				</div>
			</div>
		</li>
		<hr class="sidebar-divider my-0">
		<li class="nav-item<?php if($Core->isActivePage('settings')===true):?> active<?php endif?>">
			<a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseSettings"
			   aria-expanded="true" aria-controls="collapseSettings">
				<i class="fas fa-cog"></i>
				<span>Settings</span>
			</a>
			<div id="collapseSettings" class="collapse<?php if($Core->isActivePage('settings')===true):?> show<?php endif?>" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
				<div class="bg-white py-2 collapse-inner rounded">
					<a class="collapse-item<?php if($Core->isActivePage('settings_database')===true):?> active<?php endif?>" href="settings/database">Database</a>
					<a class="collapse-item<?php if($Core->isActivePage('settings_server')===true):?> active<?php endif?>" href="settings/server">Server</a>
				</div>
			</div>
		</li>
	</ul>
	<div id="content-wrapper" class="d-flex flex-column">
		<div id="content">
			<nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
				<button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
					<i class="fa fa-bars"></i>
				</button>
				<span><strong>Systemtime: </strong><span id="time"><?php echo date('Y.m.d H:i:s')?></span></span>&nbsp;|&nbsp;<span><strong>Version: </strong><span id="version"><?php echo \osWMensch\Server\Core::getVersion()?><?php if (\osWMensch\Server\Core::checkUpdate(\osWMensch\Server\Core::getVersion(), \osWMensch\Server\Configure::getValueAsString('mensch_update'))===true):?> [<a href="code/mensch2">update available</a>]<?php endif?></span></span>
				<ul class="navbar-nav ml-auto">
					<li class="nav-item dropdown no-arrow">
								<span class="nav-link dropdown-toggle" href="#">
									<span class="mr-2 d-none d-lg-inline text-gray-600">Welcome to Mensch²</span>
									<a href="https://oswframe.com" target="_blank" title="osWFrame.com"><img class="img-profile rounded-circle" src="img/osw_logo_symbol.svg"></a>
								</span>
					</li>
				</ul>
			</nav>
			<div class="container-fluid">
