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


if(!defined('pkFRONTEND') || pkFRONTEND!='public')
	die('Direct access to this location is not permitted.');


include(pkDIRPUBLICINC.'forumsheader'.pkEXT);


if(!userrights($forumcat['forumcat_mods'])) 
	{
	pkEvent('access_refused');
	
	include(pkDIRPUBLICINC.'forumsfooter'.pkEXT);
	return;
	}
	
$ACTION=(isset($_POST['action'])) ? $_POST['action'] :'view';


$forumthread['forumthread_title']=pkEntities($forumthread['forumthread_title']);


if($ACTION==$_POST['cancel'])
	{
	pkHeaderLocation('forumsthread','','threadid='.$threadid);
	}

if($ACTION==$_POST['delete'] && $_POST['delete_confirm']=='confirmed')
	{
	$SQL->query("DELETE FROM ".pkSQLTAB_FORUM_THREAD." WHERE forumthread_id='".$threadid."' LIMIT 1");
	$SQL->query("DELETE FROM ".pkSQLTAB_FORUM_POST." WHERE forumpost_threadid='".$threadid."'");
	
	pkHeaderLocation('forumscategory','','catid='.$forumthread['forumthread_catid']);
	}

if($_REQUEST['alter_delete']==1)
	{
	eval("\$site_body.= \"".pkTpl("forum/moderate_delete")."\";");
	}
elseif($ACTION==$_POST['save'] && !empty($_POST['alter_title']))
	{
	if($_POST['alter_cat']>0)
		$alter_cat=", forumthread_catid='".intval($_POST['alter_cat'])."'";
	else
		unset($alter_cat);
		
	$SQL->query("UPDATE ".pkSQLTAB_FORUM_THREAD." 
		SET forumthread_icon='".$SQL->f($_POST['post_icon'])."',
			forumthread_status='".$SQL->i($_POST['alter_status'])."',
			forumthread_title='".$SQL->f($_POST['alter_title'])."' ".
			$alter_cat." 
		WHERE forumthread_id='".$threadid."'");
	
	list($firstpostid)=$SQL->fetch_row($SQL->query("SELECT MIN(forumpost_id) FROM ".pkSQLTAB_FORUM_POST." WHERE forumpost_threadid='".$threadid."' LIMIT 1"));

	if($firstpostid>0)
		$SQL->query("UPDATE ".pkSQLTAB_FORUM_POST."
			SET forumpost_title='".$SQL->f($_POST['alter_title'])."',
				forumpost_icon='".$SQL->f($_POST['post_icon'])."'
			WHERE forumpost_id='".$firstpostid."'");
	
	pkHeaderLocation('forumsthread','','threadid='.$threadid);
	}
else
	{
	eval("\$theme_icon.= \"".pkTpl("forum/newpost_noicon")."\";");
	
	$dir="images/icons";
	$width=2;
	
	$a=opendir($dir);
	while($datei=readdir($a))
		{
		if(strstr($datei,".gif"))
			{
			if($width==8)
				{
				$theme_icon.="</tr><tr>";
				$width=1;
				}
			
			if($forumthread['forumthread_icon']==$datei)
				$iconoption='checked';
			
			eval("\$theme_icon.= \"".pkTpl("forum/newpost_icons")."\";");
			$width++; 
			
			unset($iconoption);
			}
		}
	closedir($a);
	
	$cs=8-$width;
	
	if($cs>0)
		$theme_icon.='<td colspan="'.$cs.'"></td>';
	
	foreach($forumcat_cache_byname as $cats)
		{
		if($cats['forumcat_id']==$forumthread['forumthread_catid'])
			continue;
		
		$option_cat.='<option value="'.$cats['forumcat_id'].'">'.pkEntities($cats['forumcat_name']).'</option>';
		}
	
	
	if($forumthread['forumthread_status']==1)
		$status1='selected';
	elseif($forumthread['forumthread_status']==2)
		$status2='selected';
	elseif($forumthread['forumthread_status']==3)
		$status3='selected';
	else
		$status0='selected';
		
	eval("\$site_body.= \"".pkTpl("forum/moderate")."\";");
	}


include(pkDIRPUBLICINC.'forumsfooter'.pkEXT);
?>