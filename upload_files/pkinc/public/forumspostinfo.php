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


$modehash=array('report');
$mode=(isset($_REQUEST['mode']) && in_array($_REQUEST['mode'],$modehash)) ? $_REQUEST['mode'] : NULL;


include(pkDIRPUBLICINC.'forumsheader'.pkEXT);


switch($mode)
	{
	case 'report' :
		if(!pkGetUservalue('id')>0)
			{
			pkEvent('access_refused');
			include(pkDIRPUBLICINC.'forumsfooter'.pkEXT);
			return;
			}

		unset($modhash);
		
		$postid=(isset($_REQUEST['postid']) && intval($_REQUEST['postid'])>0) ? intval($_REQUEST['postid']) : 0;
	
		$forumpostinfo=$SQL->fetch_array($SQL->query("SELECT forumpost_threadid,forumpost_id FROM ".pkSQLTAB_FORUM_POST." WHERE forumpost_id='".$postid."' LIMIT 1"));
		$forumthreadinfo=$SQL->fetch_array($SQL->query("SELECT forumthread_catid FROM ".pkSQLTAB_FORUM_THREAD." WHERE forumthread_id='".$forumpostinfo['forumpost_threadid']."' LIMIT 1"));
		$forumcatinfo=$forumcat_cache[$forumthreadinfo['forumthread_catid']];
		
		$sqlcommand="SELECT * FROM ".pkSQLTAB_USER." WHERE (user_status='mod' OR user_status='admin'";
		$modhash=explode("-",$forumcatinfo['forumcat_mods']);
		
		if(is_array($modhash))
			{
			foreach($modhash as $userid)
				{
				$sqlcommand.=" OR user_id='".$userid."'";
				}
			}
		
		unset($report_row);
		
		$getuserinfo=$SQL->query($sqlcommand.") AND user_id!='".pkGetUservalue('id')."' ORDER by TRIM(user_nick) ASC");
		while($userinfo=$SQL->fetch_array($getuserinfo))
			{
			$row=rowcolor($row);
			$userinfo['user_nick']=pkEntities($userinfo['user_nick']);
			
			eval("\$report_row.= \"".pkTpl("forum/report_row")."\";");
			}
		
		
		eval("\$site_body.= \"".pkTpl("forum/report")."\";");
		break;
		#END case report
	default :
		$postid=(isset($_REQUEST['postid']) && intval($_REQUEST['postid'])>0) ? intval($_REQUEST['postid']) : 0;
		
		
		$postinfo=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_FORUM_POST." WHERE forumpost_id='".$postid."' LIMIT 1"));
		$forumthreadinfo=$SQL->fetch_array($SQL->query("SELECT forumthread_catid, forumthread_title FROM ".pkSQLTAB_FORUM_THREAD." WHERE forumthread_id='".$postinfo['forumpost_threadid']."' LIMIT 1"));
		
		$forumcat=$forumcat_cache[$forumthreadinfo['forumthread_catid']];

		if(!userrights($forumcat['forumcat_mods']))
			{
			pkEvent('access_refused');
			include(pkDIRPUBLICINC.'forumsfooter'.pkEXT);
			return;	
			}
		
		
		if($postinfo['forumpost_autorid']>0)
			$userinfo=$SQL->fetch_array($SQL->query("SELECT * FROM ".pkSQLTAB_USER." WHERE user_id='".$postinfo['forumpost_autorid']."' LIMIT 1"));
	
		if($userinfo['user_id']>0)
			{
			$userinfo_hash=array();
			$userinfo_hash[$userinfo['user_id']]=$userinfo;
			$userinfo['user_nick']=pkEntities($userinfo['user_nick']);
			
			eval("\$post_autor= \"".pkTpl("forum/member_showprofil_textlink")."\";");
			}
		else
			$post_autor=pkEntities($postinfo['forumpost_autor']);
		
		
		$post_time=formattime($postinfo['forumpost_time']);
		$post_ip=$postinfo['forumpost_ipaddr'];
		
		
		$getpostinfos=$SQL->query("SELECT 
			*
			FROM ".pkSQLTAB_FORUM_POST."
			WHERE ((forumpost_autorid!=0 AND forumpost_autorid='".$postinfo['forumpost_autorid']."') 
				OR (forumpost_ipaddr='".$postinfo['forumpost_ipaddr']."' AND forumpost_ipaddr!=''))
				AND forumpost_id!='".$postinfo['forumpost_id']."'
			ORDER by forumpost_time DESC
			LIMIT 15");
		
		unset($sqlcommand);
		while($postinfos=$SQL->fetch_array($getpostinfos))
			{
			$postinfos_cache[]=$postinfos;
			
			if($postinfos['forumpost_autorid']>0 && $postinfos['forumpost_autorid']!=$userinfo['user_id'])
				{
				if($sqlcommand)
					$sqlcommand.=" OR user_id='".$postinfos['forumpost_autorid']."'";
				else
					$sqlcommand="SELECT * FROM ".pkSQLTAB_USER." WHERE user_id='".$postinfos['forumpost_autorid']."'";
				}
			}
		
		
		if($sqlcommand)
			{
			$getuserinfo=$SQL->query($sqlcommand);
			while($userinfo=$SQL->fetch_array($getuserinfo))
				{
				$userinfo_hash[$userinfo['user_id']]=$userinfo;
				}
			}
		
		unset($postinfo_row);
		
		if(is_array($postinfos_cache))
			{
			foreach($postinfos_cache as $postinfos)
				{
				$row=rowcolor($row);
				$userinfo=$userinfo_hash[$postinfos['forumpost_autorid']];
		
				if($userinfo['user_id']>0)
					{
					$userinfo['user_nick']=pkEntities($userinfo['user_nick']);
					
					eval("\$posting_autor= \"".pkTpl("forum/member_showprofil_textlink")."\";");
					}
				else
					$posting_autor=pkEntities($postinfos['forumpost_autor']);
				
				$posting_title=pkEntities($postinfos['forumpost_title']);
				$posting_time=formattime($postinfos['forumpost_time']);
				$userinfo=$userinfo_hash[$postinfos['forumpost_autorid']];
				
				eval("\$postinfo_row.= \"".pkTpl("forum/postinfo_row")."\";");
				}
			}
		
		if(!$postinfo_row)
			eval("\$postinfo_row.= \"".pkTpl("forum/postinfo_empty")."\";");
			
		$forumthreadinfo['forumthread_title']=pkEntities($forumthreadinfo['forumthread_title']);
	
		eval("\$site_body.= \"".pkTpl("forum/postinfo")."\";");
		break;
		#END default
	}


include(pkDIRPUBLICINC.'forumsfooter'.pkEXT);
?>