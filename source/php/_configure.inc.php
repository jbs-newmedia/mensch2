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

/**
 * Konfiguration
 */
$configure['mysql_server']='localhost';
$configure['mysql_user']='';
$configure['mysql_password']='';
$configure['mysql_database']='';
$configure['mysql_prefix']='';
$configure['mysql_engine']='InnoDB';
$configure['mysql_character']='utf8mb4';
$configure['mysql_collation']='utf8mb4_general_ci';
$configure['source_serverlist']='';
$configure['source_serverlist_name']='';
$configure['source_serverlist_package']='';
$configure['mensch_url']='';
$configure['mensch_url_path']='';
$configure['mensch_path']=OSWMENSCH_CORE_ABSPATH;
$configure['git_path']='';
$configure['git_mensch_path']='';
$configure['source_path']=$configure['git_path'].'source/';
$configure['release']=['stable', 'beta', 'alpha'];
$configure['prefix']=[''];
$configure['htuser']='';
$configure['htpass']='';
$configure['mensch_token']='';

?>