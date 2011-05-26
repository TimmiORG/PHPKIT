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


if(!adminaccess('frank'))
	return pkEvent('access_forbidden');

$ACTION=(isset($_POST['action'])) ? $_POST['action'] : 'view';
$rankid=(isset($_POST['rankid']) && intval($_POST['rankid'])>0) ? intval($_POST['rankid']) : ((isset($_POST['rankid']) && $_POST['rankid']=='new')? 'new' : 0);

if(isset($_POST['save']) && isset($_POST['newrank_title']) && $ACTION==$_POST['save'] && !empty($_POST['newrank_title']) && $rankid)
	{
	if($rankid=='new')
		{
		if($SQL->num_rows($SQL->query("SELECT * FROM ".pkSQLTAB_FORUM_RANK." WHERE forumrank_post='".intval($_POST['newrank_post'])."'"))<1)
			{
			$SQL->query("INSERT INTO ".pkSQLTAB_FORUM_RANK."
				(forumrank_post, forumrank_title)
				VALUES
				('".intval($_POST['newrank_post'])."','".$SQL->f($_POST['newrank_title'])."')");
			}
		}
	else
		{
		$SQL->query("UPDATE ".pkSQLTAB_FORUM_RANK."
			SET forumrank_post='".intval($_POST['newrank_post'])."',
				forumrank_title='".$SQL->f($_POST['newrank_title'])."' 
			WHERE forumrank_id='".$rankid."'");
		}
	}

if($ACTION==$_POST['delete'] && $rankid && $rankid!='new')
	{
	$SQL->query("DELETE FROM ".pkSQLTAB_FORUM_RANK." WHERE forumrank_id='".$rankid."'");
	}

if($ACTION!='view')
	{
	pkHeaderLocation('forumrank');
	}

		
$getrank=$SQL->query("SELECT * FROM ".pkSQLTAB_FORUM_RANK." ORDER by forumrank_post ASC");
while($rank=$SQL->fetch_array($getrank))
	{
	$row=rowcolor($row);
	
	$posts=intval($rank['forumrank_post']);
	$title=pkEntities($rank['forumrank_title']);
	
	eval ("\$editrank_row.= \"".pkTpl("forum/editrank_row")."\";");
	}

eval("\$site_body.= \"".pkTpl("forum/editrank")."\";");
?>