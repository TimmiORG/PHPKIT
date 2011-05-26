<?php
# PHPKIT Web Content Management System
# --------------------------------------------
# Copyright (c) 2002-2007 Gersöne & Schott GbR
#
# This file / the PHPKIT-software is no freeware!
# For further informations please vistit our website
# or contact us via email:
#
# Diese Datei / die PHPKIT-Software ist keine Freeware!
# Für weitere Information besuchen Sie bitte unsere 
# Webseite oder kontaktieren uns per E-Mail:
#
# Website : http://www.phpkit.de
# Mail    : info@phpkit.de
#
# YOU ARE NOT AUTHORISED TO CREATE ILLEGAL COPIES OF THIS
# FILE AND/OR TO REMOVE THIS INFORMATIONS
#
# SIE SIND NICHT BERECHTIGT, UNRECHTMÄSSIGE KOPIEN DIESER
# DATEI ZU ERSTELLEN UND/ODER DIESE INFORMATIONEN ZU ENTFERNEN


if(!defined('pkFRONTEND') || pkFRONTEND!='admin')
	die('Direct access to this location is not permitted.');


if(!adminaccess('adview'))
	return pkEvent('access_forbidden');																																																																								if(!@pkl( 1)) return pkEvent(strrev('deriuqer_ecnecil'));


$ACTION=(isset($_POST['action'])) ? $_POST['action'] : 'view';
$mode=(isset($_REQUEST['mode']) && $_REQUEST['mode']=='view') ? 'view' : NULL;


if($mode=='view')
	{
	$row=$_REQUEST['row'];
	
	$adview=$SQL->fetch_array($SQL->query("SELECT adview_code FROM ".pkSQLTAB_ADVIEW." WHERE adview_id='".$SQL->i($_REQUEST['id'])."'"));
	
	$adview_code=$adview['adview_code'];
	$basedir=pkGetConfigF('site_url').'/';
	
	eval("echo \$adview_iframe=\"".pkTpl("adview_iframe")."\";");
	exit;
	}


if($ACTION==$_POST['save'] && trim($_POST['adview_code'])!='')
	{
	if(intval($_POST['adview_relation'])<1) $_POST['adview_relation']=1;

	if(!$_POST['adview_id']>0)
		{
		$SQL->query("INSERT INTO ".pkSQLTAB_ADVIEW." (adview_code) VALUES ('new')");
		
		$_POST['adview_id']=$SQL->insert_id();
		$_POST['adview_reset']=1;
		}
	
	if($_POST['adview_reset']==1) 
		$sqlcommand=",adview_views=0,adview_time='".pkTIME."'";
	else
		unset($sqlcommand);
		
	$SQL->query("UPDATE ".pkSQLTAB_ADVIEW."
		SET adview_code='".$SQL->f($_POST['adview_code'])."',
			adview_relation='".$SQL->f($_POST['adview_relation'])."',
			adview_status='".$SQL->f($_POST['adview_status'])."' ".
			$sqlcommand." 
		WHERE adview_id='".$SQL->id($_POST['adview_id'])."'");
	}


if($ACTION==$_POST['delete'])
	{
	$SQL->query("DELETE FROM ".pkSQLTAB_ADVIEW." WHERE adview_id='".$SQL->id($_POST['adview_id'])."'");
	}

if($ACTION!='view')
	{
	pkHeaderLocation('advertising');
	}


if(isset($_REQUEST['editid']))
	{
	$adview=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_ADVIEW." WHERE adview_id='".$SQL->id($_REQUEST['editid'])."'"));
	
	
	$adview_editcode=pkEntities($adview['adview_code']);
	
	if($adview['adview_status']!="0")
		$adview_checked=' checked';
	
	if(!strstr(strtolower($adview['adview_code']),'iframe'))
		$adview_code=$adview['adview_code'];
	
	eval("\$adview_row.= \"".pkTpl("adview_view")."\";");
	}
else
	{
	$totalcounter=$SQL->fetch_array($SQL->query("SELECT SUM(adview_relation) FROM ".pkSQLTAB_ADVIEW." WHERE adview_status=1"));
	$tc=$totalcounter[0];
	$adview_checked=' checked';
	
	
	$getadview=$SQL->query("SELECT adview_id,
			adview_status,
			adview_time,
			adview_views,
			adview_relation,
			adview_clicks,
			adview_code 
		FROM ".pkSQLTAB_ADVIEW." 
		ORDER BY adview_relation DESC");
	while($adview=$SQL->fetch_array($getadview))
		{
		$row=rowcolor($row);
		
		if(!strstr(strtolower($adview['adview_code']),'iframe'))
			$adview_code=$adview['adview_code'];
		
		$adview_time=formattime($adview['adview_time']);
		
		if($adview['adview_status']==1)
			{
			$percent=$adview['adview_relation']/$tc*100; 
			$adview_relation=number_format($percent,1,",",".");
			$adview_relation2=$adview['adview_relation']."/$tc";
			$adview_status=$lang['enabled'];
			}
		else
			{
			$adview_relation="-";
			$adview_relation2="-";
			$adview_status=$lang['disabled'];
			} 
		
		eval("\$adview_row.= \"".pkTpl("adview_row")."\";");
		unset($adview_code);
		}
	}

pkEvent('thirdparty_adview_warning','warning');
eval("\$adview_banner=\"".pkTpl("adview_head")."\";");
eval("\$site_body.=\"".pkTpl("adview")."\";");
?>