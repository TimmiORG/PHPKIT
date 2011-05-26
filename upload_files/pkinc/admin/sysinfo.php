<?php
# PHPKIT WCMS | Web Content Management System
#
#
# YOU ARE NOT AUTHORISED TO CREATE ILLEGAL COPIES OF THIS
# FILE AND/OR TO REMOVE THIS INFORMATION
#
# SIE SIND NICHT BERECHTIGT, UNRECHTMÄSSIGE KOPIEN DIESER
# DATEI ZU ERSTELLEN UND/ODER DIESE INFORMATIONEN ZU ENTFERNEN
#
# This file / the PHPKIT software is no freeware! For further 
# information please visit our website or contact us via email:
#
# Diese Datei / die PHPKIT Software ist keine Freeware! Für weitere
# Informationen besuchen Sie bitte unsere Website oder kontaktieren uns per E-Mail:
#
# email     : info@phpkit.com
# website   : http://www.phpkit.com
# licence   : http://www.phpkit.com/licence
# copyright : Copyright (c) 2002-2009 mxbyte gbr | http://www.mxbyte.com


if(!defined('pkFRONTEND') || pkFRONTEND!='admin')
	die('Direct access to this location is not permitted.');


if(!adminaccess('adminarea'))
	return pkEvent('access_forbidden');


pkLoadFunc('system');
$php_version=phpversion();

$sql_version=$SQL->sqlversion();
$db_size=$SQL->database_size();
$dbsize=FileSizeExt('','B',$db_size[0]);
$dbsize_total=FileSizeExt('','B',$db_size[1]);


if($_ENV['MACHTYPE']!='')
	$server_os=$_ENV['MACHTYPE'];
elseif(isset($_ENV["WINDIR"]))
	$server_os='Windows';
else
	$server_os='---';

$server_software=$_SERVER["SERVER_SOFTWARE"];

if($uptime=@exec("uptime"))
	{
	unset($match);
	preg_match("/averages?: ([0-9\.]+),[\s]+([0-9\.]+),[\s]+([0-9\.]+)/",$uptime,$match);
	$match[1]*=100;
	$match[2]*=100;
	$match[3]*=100;
	eval("\$about_rows.= \"".pkTpl("about_serverinfo")."\";");
	}

$server_time = strftime('%d.%m.%Y - %H:%M:%S',pkTIME).' / '.formattime();
$safemode_status=(ini_get('safe_mode')) ? $lang['enabled'] : $lang['disabled'];


$about_chmods='';
foreach(pkSystemWriteableDirectories() as $dir)
	{
	if(!@is_writable($dir))
		{
		$error=true; 
		$status='false';
		
		if(!is_dir($dir) && !is_file($dir))
			$dir_status=$lang['not_available'];
		else 
			$dir_status=$lang['not_set'];
		}
	else
		{
		$status='true';
		$dir_status=$lang['isset'];
		}
	
	$dir=str_replace(pkDIRROOT,'',$dir);
	
	eval("\$about_rows.= \"".pkTpl("about_chmods")."\";");
	}


$avatardir_size=0;
$dir='../'.$config['avatar_path'];

if(@is_dir($dir))
	{
	$a=opendir($dir);
	while($datei=readdir($a))
		{
		if($datei!='index.php' && $datei!='.' && $datei!='..')
			$avatardir_size += @filesize($dir.'/'.$datei);
		}
	
	$avatardir_size=FileSizeExt('','B',$avatardir_size);
	closedir($a);
	}

eval("\$site_body.= \"".pkTpl("about")."\";");
?>