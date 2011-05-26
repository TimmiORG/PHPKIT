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


if(!adminaccess('usergroup'))
	return pkEvent('access_forbidden');


$accesshash=array('config',
   'user',
   'usergroup',
   'userinfo',
   'navlink',
   'navcat',
   'stats',
   'refferer',
   'avatar',
   'adview',
   'images',
   'comment',
   'smilies',
   'style',
   'templates',
   'database',
   'gbedit',
   'gbdelete',
   'content',
   'article',
   'news',
   'links',
   'download',
   'contdelete',
   'contfree',
   'submit',
   'contentcat',
   'fedit',
   'fdelete',
   'frank',
   'finfo',
   'vote',
   'faq',
   'faqcat');


$ACTION=(isset($_POST['action'])) ? $_POST['action'] : 'view';
$grpid=(isset($_REQUEST['grpid']) && intval($_REQUEST['grpid'])>0) ? intval($_REQUEST['grpid']) : ((isset($_REQUEST['grpid']) && $_REQUEST['grpid']=='new') ? 'new' : 0);


if(isset($_POST['cancel']) && $ACTION==$_POST['cancel'])
	{
	pkHeaderLocation('usergroup');
	}

 
if(isset($_POST['delete']) && $ACTION==$_POST['delete'] && $grpid && $grpid!='new')
	{
	$SQL->query("UPDATE ".pkSQLTAB_USER." SET user_groupid=0 WHERE user_groupid='".$grpid."'");
	$SQL->query("DELETE FROM ".pkSQLTAB_USER_GROUP." WHERE usergroup_id='".$grpid."' LIMIT 1");
	
	pkHeaderLocation('usergroup');
	}


if(isset($_POST['remove']) && $ACTION==$_POST['remove'])
	{
	if(isset($_POST['usergroup_member']) && intval($_POST['usergroup_member'])>0)
		{
		$SQL->query("UPDATE ".pkSQLTAB_USER." SET user_groupid=0 WHERE user_id='".intval($_POST['usergroup_member'])."'  LIMIT 1");
		}
	
	pkHeaderLocation('usergroup');
	}


if(isset($_POST['save']) && $ACTION==$_POST['save']) 
	{
	if($grpid && $grpid=='new')
		{
		$SQL->query("INSERT INTO ".pkSQLTAB_USER_GROUP." (usergroup_name) VALUES ('')");
		$grpid=$SQL->insert_id();
		}
	
	$sqlcommand='';
	
	foreach($accesshash as $key)
		{
		$var='access_'.$key;
		$sqlcommand.=",access_".$key."='".((isset($_POST[$var]) ? $_POST[$var] : 0))."'";
		}
	
	
	$SQL->query("UPDATE ".pkSQLTAB_USER_GROUP." 
		SET usergroup_name='".$SQL->f($_POST['usergroup_name'])."',
			usergroup_description='".$SQL->f($_POST['usergroup_description'])."'".
			$sqlcommand."
		WHERE usergroup_id='".$grpid."'");

	pkHeaderLocation('usergroup','','grpid='.$grpid);
	}

if($grpid)
	{
	if($grpid && intval($grpid)>0)
		{
		$group=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_USER_GROUP." WHERE usergroup_id='".$grpid."' LIMIT 1"));
		
		foreach($accesshash as $key)
			{
			$var='access_'.$key;
			$$var=($group['access_'.$key]) ? 'checked' : '';
			}
		
		$usergroup_name=pkEntities($group['usergroup_name']);
		$usergroup_description=pkEntities($group['usergroup_description']);
		}
	
	eval("\$site_body.= \"".pkTpl("usergroup_form")."\";"); 
	return;
	}


unset($group_row);
$groups=$SQL->fetch_array($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_USER_GROUP.""));

if(!$groups[0]>0)
	$groups[0]='0';

$getgroups=$SQL->query("SELECT * FROM ".pkSQLTAB_USER_GROUP." ORDER by usergroup_name ASC"); 
while($group=$SQL->fetch_array($getgroups))
	{
	$row=rowcolor($row);
	$getuserinfo=$SQL->query("SELECT user_id, user_nick FROM ".pkSQLTAB_USER." WHERE user_groupid='".$group['usergroup_id']."' ORDER by user_nick");
	while($userinfo=$SQL->fetch_array($getuserinfo))
		{
		$userinfo['user_nick']=pkEntities($userinfo['user_nick']);
		
		eval("\$group_member.= \"".pkTpl("usergroup_info_row_member")."\";");
		}
	
	$usergroup_name=pkEntities($group['usergroup_name']);
	$usergroup_description=pkEntities($group['usergroup_description']);
	
	eval("\$group_row.= \"".pkTpl("usergroup_info_row")."\";");
	unset($group_member);
	}

if($group_row)
	eval("\$info_group= \"".pkTpl("usergroup_info")."\";");


eval("\$site_body.= \"".pkTpl("usergroup")."\";"); 
?>