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


if(!adminaccess('refferer')) 
	return pkEvent('access_forbidden');


$entries=isset($_REQUEST['entries']) && intval($_REQUEST['entries'])>0 ? intval($_REQUEST['entries']) : 0;


if(isset($_POST['action']))
	$ACTION=$_POST['action'];
else
	$ACTION='view';


if($ACTION==$_POST['delete'] && is_array($_POST['referer_delete'])) 
	{
	unset($sqlcommand); 
	foreach($_POST['referer_delete'] as $u) 
		{
		if($sqlcommand)
			$sqlcommand.=" OR record_referer='".$SQL->f($u)."'";
		else
			$sqlcommand="DELETE FROM ".pkSQLTAB_RECORD." WHERE record_referer='".$SQL->f($u)."'";
		}
	
	if($sqlcommand)
		$SQL->query($sqlcommand);
	
	pkHeaderLocation('referer','','entries='.$entries);
	}

if($ACTION==$_POST['remove_all']) 
	{
	$SQL->query("DELETE FROM ".pkSQLTAB_RECORD);
	
	pkHeaderLocation('referer');
	}


$epp=20;
$maxwidth=65;
$width1=50;
$width2=-15;


$sqlcommand="SELECT * FROM ".pkSQLTAB_RECORD." WHERE record_referer NOT LIKE '%".$config['site_url']."%' AND record_referer!=''";
if($config['referer_filter']!='') 
	{
	$f=explode("\n",$config['referer_filter']);
	foreach($f as $rf)
		{
		if(($rf=trim($rf))!='') 
			$sqlcommand.=" AND record_referer NOT LIKE '%".$SQL->f($rf)."%'";
		}
	}

$counter=intval($SQL->num_rows($SQL->query($sqlcommand)));
$sidelink_top=sidelinkfull($counter, $epp, $entries, "include.php?path=referer","headssmall");
$sidelink_bottom=sidelinkfull($counter, $epp, $entries, "include.php?path=referer","");
$sqlcommand.=" ORDER by record_time DESC LIMIT ".$entries.",".$epp;


$geturl=$SQL->query($sqlcommand);
while($url=$SQL->fetch_assoc($geturl))
	{
	$row=rowcolor($row);
	$referer_time_date=formattime($url['record_time'],'','date');
	$referer_time=formattime($url['record_time'],'','time');
	$referer_url=pkEntities(preg_replace('/[&|?]PHPKITSID=[^&]*/',"",$url['record_referer']));
	
	
	$referer_linktext=trim($url['record_referer']);
	
	if(strlen($referer_linktext)>$maxwidth)
		$referer_linktext=substr($referer_linktext,0,$width1)."...".substr($referer_linktext,$width2);
	
	$referer_linktext=pkEntities($referer_linktext);
	
	
	eval("\$referer_row.= \"".pkTpl("referer_row")."\";");
	}

if($referer_row=="")
	eval("\$referer_row= \"".pkTpl("referer_empty")."\";");
	

$form_action=pkLink('referer');

eval("\$site_body.= \"".pkTpl("referer")."\";");
?>