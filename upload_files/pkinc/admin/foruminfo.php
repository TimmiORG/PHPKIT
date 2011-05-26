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


if(!adminaccess('finfo'))
	return pkEvent('access_forbidden');


$finfoid=(isset($_REQUEST['finfoid']) && intval($_REQUEST['finfoid'])>0) ? intval($_REQUEST['finfoid']) : ((isset($_REQUEST['finfoid']) && $_REQUEST['finfoid']=='new') ? 'new' : 0);
$ACTION=(isset($_POST['action'])) ? $_POST['action'] : 'view';


if(isset($_POST['save']) && $ACTION==$_POST['save'])
	{
	$finfo_autorid=pkGetUservalue('id');
	$finfo_text=addslashes($_POST['content']);
	$finfo_title=addslashes($_POST['finfo_title']);
	$finfo_timet=formattime(pkMkTime($ENV->_post_id('finfo_time_h'),$ENV->_post_id('finfo_time_mm'),0,$ENV->_post_id('finfo_time_m'),$ENV->_post_id('finfo_time_d'),$ENV->_post_id('finfo_time_y')),'','istamp');
	$finfo_expire=pkMkTime($ENV->_post_id('finfo_expire_h'),$ENV->_post_id('finfo_expire_mm'),0,$ENV->_post_id('finfo_expire_m'),$ENV->_post_id('finfo_expire_d'),$ENV->_post_id('finfo_expire_y'));
	
	$finfo_timet=($finfo_timet<0 ? pkTIME : $finfo_timet);
	
	$finfo_expire=($finfo_expire<=0 ? 0 : formattime($finfo_expire,'','stamp'));
	$finfo_expire=($finfo_expire<=$finfo_timet ? 0 : $finfo_expire);


	if($_POST['finfo_autor']!=pkGetUservalue('nick'))
		list($finfo_autorid)=$SQL->fetch_row($SQL->query("SELECT user_id FROM ".pkSQLTAB_USER." WHERE user_nick='".$SQL->f($ENV->_post['finfo_autor'])."'"));
	

	if(is_array($_POST['finfo_cat']))
		{
		if($_POST['finfo_cat'][0]==0)
			$finfo_cat=0;
		else
			$finfo_cat="-".implode("-",$_POST['finfo_cat'])."-";
		}
	
	
	if($finfoid=='new')
		{
		$SQL->query("INSERT INTO ".pkSQLTAB_FORUM_INFO." (foruminfo_time) VALUES ('".pkTIME."')");
		$finfoid=$SQL->insert_id();
		}
	
	$SQL->query("UPDATE ".pkSQLTAB_FORUM_INFO." 
		SET foruminfo_autorid='".$finfo_autorid."',
			foruminfo_title='".$finfo_title."',
			foruminfo_text='".$finfo_text."',
			foruminfo_time='".$finfo_timet."',
			foruminfo_expire='".$finfo_expire."',
			foruminfo_catids='".$finfo_cat."'
		WHERE foruminfo_id='".$finfoid."'");
	}
elseif($ACTION==$_POST['delete'] && $finfoid!='new')
	{
	$SQL->query("DELETE FROM ".pkSQLTAB_FORUM_INFO." WHERE foruminfo_id='".$finfoid."'");
	}


if($ACTION!='view')
	pkHeaderLocation('foruminfo');



if($finfoid)
	{
	if($finfoid=='new')
		{
		$foruminfo['foruminfo_autorid']=pkGetUservalue('id');
		$foruminfo['foruminfo_time']=pkTIME;
		$foruminfo['foruminfo_title']='';
		$foruminfo_text='';
		}
	else
		{
		$foruminfo=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_FORUM_INFO." WHERE foruminfo_id='".$finfoid."'"));
		$foruminfo_text=pkEntities($foruminfo['foruminfo_text']);
		$foruminfo['foruminfo_title']=pkEntities($foruminfo['foruminfo_title']);		
		}
	
	
	if($foruminfo['foruminfo_autorid']!=pkGetUservalue('id') && $finfoid!='new')
		list($finfo_autor)=$SQL->fetch_row($SQL->query("SELECT user_nick FROM ".pkSQLTAB_USER." WHERE user_id='".$foruminfo['foruminfo_autorid']."'"));
	elseif($foruminfo['foruminfo_autorid']==pkGetUservalue('id'))
		$finfo_autor=pkGetUservalue('nick');
		
	
	if($foruminfo['foruminfo_time']>=0)
		{
		$time=formattime($foruminfo['foruminfo_time'],'','stamp');
		$finfo_time_d=date("d",$time);
		$finfo_time_m=date("m",$time);
		$finfo_time_y=date("Y",$time);
		$finfo_time_h=date("H",$time);
		$finfo_time_mm=date("i",$time);
		}
	
	if($foruminfo['foruminfo_expire']>0)
		{
		$time=formattime($foruminfo['foruminfo_expire'],'','stamp');
		$finfo_expire_d=date("d",$time);
		$finfo_expire_m=date("m",$time);
		$finfo_expire_y=date("Y",$time);
		$finfo_expire_h=date("H",$time);
		$finfo_expire_mm=date("i",$time);
		}
	
	
	$getcatinfo=$SQL->query("SELECT forumcat_id, forumcat_name FROM ".pkSQLTAB_FORUM_CATEGORY." ORDER by forumcat_name ASC");
	while($catinfo=$SQL->fetch_array($getcatinfo))
		{
		$catinfo['forumcat_name']=pkEntities($catinfo['forumcat_name']);
		
		if($foruminfo['foruminfo_catids']!="" && $foruminfo['foruminfo_catids']!="0")
			{
			if(strstr($foruminfo['foruminfo_catids'],"-".$catinfo['forumcat_id']."-"))
				{
				$forumlist.=$catinfo['forumcat_name'].", ";
				$selected=" selected";
				}
			}
		
		eval("\$finfo_catoption.= \"".pkTpl("forum/finfo_catoption")."\";");
		unset($selected);
		}
	
	if($foruminfo['foruminfo_catids']=="0")
		$selected0=" selected";

	$smilies=new smilies();
	$format_smilies=$smilies->getSmilies(1,1);
	
	$error_validity_period=($foruminfo['foruminfo_expire']<$foruminfo['foruminfo_time'] && $foruminfo['foruminfo_expire']>0) ? pkGetLangError('enddate_earlier_then_start') : '';	
	
	eval("\$finfo_bbcode=\"".pkTpl("format_text")."\";");
	eval("\$finfo_body=\"".pkTpl("forum/finfo_form")."\";");
	}
else
	{
	$foruminfo_hash=array();
	$userinfo_hash=array();
	$sqlcommand='';
	
	$getforuminfo=$SQL->query("SELECT * FROM ".pkSQLTAB_FORUM_INFO." ORDER by foruminfo_time DESC");
	while($foruminfo=$SQL->fetch_array($getforuminfo))
		{
		$foruminfo_hash[]=$foruminfo;
		
		if($sqlcommand)
			$sqlcommand.=" OR user_id='".$foruminfo['foruminfo_autorid']."'";
		else 
			$sqlcommand="SELECT user_id, user_nick FROM ".pkSQLTAB_USER." WHERE user_id='".$foruminfo['foruminfo_autorid']."'";
		}
  
	if(is_array($foruminfo_hash))
		{
		if($sqlcommand)
			{
			$getuserinfo=$SQL->query($sqlcommand); 
			while($userinfo=$SQL->fetch_array($getuserinfo))
				{
				$userinfo_hash[$userinfo['user_id']]=$userinfo;
				}
			}
		
		foreach($foruminfo_hash as $foruminfo)
			{
			$row=rowcolor($row);
			
			
			if(empty($foruminfo['foruminfo_title']))
				$foruminfo_title='<font class="highlight">'.$lang['no_title'].'</font>';
			else
				$foruminfo_title=pkEntities($foruminfo['foruminfo_title']);
			
			$userinfo=$userinfo_hash[$foruminfo['foruminfo_autorid']];
			$foruminfo_autorid=$userinfo['user_id'];
			$foruminfo_autor=pkEntities($userinfo['user_nick']);
			$foruminfo_date=formattime($foruminfo['foruminfo_time']);
			
			
			if($foruminfo['foruminfo_expire']>0)
				$foruminfo_expire=formattime($foruminfo['foruminfo_expire']);
			else
				$foruminfo_expire='-';
			
			eval("\$archiv_row.= \"".pkTpl("forum/finfo_archiv_row")."\";");
			}
		}
	
	eval("\$finfo_body= \"".pkTpl("forum/finfo_archiv")."\";");
	}

eval("\$site_body.= \"".pkTpl("forum/finfo")."\";");
?>