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


if(intval(pkGetUservalue('id'))>0)
	{
	pkLoadFunc('user');
	$user_navigation=pkUserNavigation();
	
	if(isset($_REQUEST['add']) && intval($_REQUEST['add'])>0)
		{
		$add=intval($_REQUEST['add']);
		$favcounter=$SQL->fetch_array($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_FORUM_FAVORITE." WHERE forumfav_userid='".$SQL->i(pkGetUservalue('id'))."'"));
		if($favcounter[0]>=$config['forum_maxfav'])
			$error=1;
		else
			{
			$favcounter=$SQL->fetch_array($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_FORUM_FAVORITE." WHERE forumfav_userid='".$SQL->i(pkGetUservalue('id'))."' AND forumfav_threadid='".$add."' LIMIT 1"));
			
			if($favcounter[0]>0)
				$error=2;
			else
				$SQL->query("INSERT INTO ".pkSQLTAB_FORUM_FAVORITE." (forumfav_userid, forumfav_threadid) VALUES ('".$SQL->i(pkGetUservalue('id'))."','".$add."')");
			}
		
		pkHeaderLocation('forumsfavorites');
		}
	
	
	if(isset($_REQUEST['notifyid']) && isset($_REQUEST['set']))
		{
		$notifyid=intval($_REQUEST['notifyid']);
		$set=intval($_REQUEST['set']);
		
		if($set==1)
			$SQL->query("INSERT INTO ".pkSQLTAB_FORUM_NOTIFY." (forumnotify_userid, forumnotify_threadid, forumnotify_email) VALUES ('".$SQL->i(pkGetUservalue('id'))."','".$notifyid."','".$SQL->f(pkGetUservalue('email'))."')");
		else
			$SQL->query("DELETE FROM ".pkSQLTAB_FORUM_NOTIFY." WHERE forumnotify_threadid='".$notifyid."' AND forumnotify_userid='".$SQL->i(pkGetUservalue('id'))."'");
		
		pkHeaderLocation('forumsfavorites');
		}
	
	if(isset($_REQUEST['deleteid']))
		{
		$deleteid=intval($_REQUEST['deleteid']); 
		
		$SQL->query("DELETE FROM ".pkSQLTAB_FORUM_FAVORITE." WHERE forumfav_threadid='".$deleteid."'");
		
		pkHeaderLocation('forumsfavorites');
		}
	
	
	unset($sqlcommand);
	
	$getfav=$SQL->query("SELECT * FROM ".pkSQLTAB_FORUM_FAVORITE." WHERE forumfav_userid='".$SQL->i(pkGetUservalue('id'))."' ORDER by forumfav_threadid DESC");
	while($favinfo=$SQL->fetch_array($getfav))
		{
		$favinfo_hash[]=$favinfo;
		
		if($sqlcommand)
			$sqlcommand.="OR forumthread_id='".$favinfo['forumfav_threadid']."'";
		else
			$sqlcommand="SELECT * FROM ".pkSQLTAB_FORUM_THREAD." WHERE forumthread_id='".$favinfo['forumfav_threadid']."'";
		}
	
	if($sqlcommand)
		{
		$getforumthread=$SQL->query($sqlcommand);
		while($forumthread=$SQL->fetch_assoc($getforumthread))
			{
			$forumthread_hash[$forumthread['forumthread_id']]=$forumthread;
			}
		}
	
	if(is_array($favinfo_hash))
		{
		foreach($favinfo_hash as $favinfo)
			{
			if(!array_key_exists($favinfo['forumfav_threadid'],$forumthread_hash))
				{
				#prune: thread deleted
				
				$SQL->query("DELETE FROM ".pkSQLTAB_FORUM_FAVORITE." WHERE forumfav_threadid='".$favinfo['forumfav_threadid']."'");
				$SQL->query("DELETE FROM ".pkSQLTAB_FORUM_NOTIFY." WHERE forumnotify_threadid='".$favinfo['forumfav_threadid']."'");
				continue;
				}
			
			
			$forumthread=$forumthread_hash[$favinfo['forumfav_threadid']];
			$forumcat=$forumcat_cache[$forumthread['forumthread_catid']];
			
			
			if(getrights($forumcat['forumcat_rrights'])=="true" or userrights($forumcat['forumcat_mods'],$forumcat['forumcat_rrights'])=="true" or userrights($forumcat['forumcat_user'],$forumcat['forumcat_rrights'])=="true")
				{
				$getnotify=$SQL->query("SELECT * FROM ".pkSQLTAB_FORUM_NOTIFY." WHERE forumnotify_email='".$SQL->f(pkGetUservalue('email'))."' AND forumnotify_threadid='".$forumthread['forumthread_id']."' LIMIT 1");
				
				if(($SQL->num_rows($getnotify))>0)
					eval("\$notify_info= \"".pkTpl("forum/favorits_notify_on")."\";");
				else
					eval("\$notify_info= \"".pkTpl("forum/favorits_notify_off")."\";");
				
				if(empty($forumthread['forumthread_title']) || empty($forumthread['forumthread_autor']))
					{
					$info=$SQL->fetch_array($SQL->query("SELECT forumpost_title, forumpost_autor, forumpost_autorid FROM ".pkSQLTAB_FORUM_POST." WHERE forumpost_threadid='".$forumthread['forumthread_id']."' ORDER by forumpost_time ASC LIMIT 1"));
					
					$SQL->query("UPDATE ".pkSQLTAB_FORUM_THREAD." 
						SET forumthread_title='".$SQL->f($info['forumpost_title'])."',
							forumthread_autor='".$SQL->f($info['forumpost_autor'])."',
							forumthread_autorid='".intval($info['forumpost_autorid'])."'
						WHERE forumthread_id='".$forumthread['forumthread_id']."'");
					
					$forumthread['forumthread_title']=$info['forumpost_title'];
					$forumthread['forumthread_autor']=$info['forumpost_autor'];
					$forumthread['forumthread_autorid']=$info['forumpost_autorid'];
					
					unset($info);
					}
				
				$posts=$SQL->fetch_array($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_FORUM_POST." WHERE forumpost_threadid='".$forumthread['forumthread_id']."'"));
				$thread_replys=$posts[0]-1;
				
				if($forumthread['forumthread_replycount']!=$thread_replys)
					$SQL->query("UPDATE ".pkSQLTAB_FORUM_THREAD." SET forumthread_replycount='".$thread_replys."' WHERE forumthread_id='".$forumthread['forumthread_id']."'");
				
				if($posts[0]>$forumcat['forumcat_posts'] && $forumcat['forumcat_posts']!=0)
					$sidelink=" - ".sidelinksmall($posts[0], $forumcat['forumcat_posts'],"include.php?path=forumsthread&threadid=".$forumthread['forumthread_id']);
				
				if(empty($forumthread['forumthread_icon']))
					$forumthread_icon='blank.gif';
				else
					$forumthread_icon="icons/".basename($forumthread['forumthread_icon']);
				
				if($forumthread['forumthread_status']==0 || $forumthread['forumthread_status']==3)
					$threadstatus="close";
				else
					$threadstatus="open";
				
				if($forumthread['forumthread_status']==2 or $forumthread['forumthread_status']==3)
					$threadstatus.="fixed";
				
				if($FORUM->isUnreadedThread($forumthread['forumthread_catid'],$forumthread['forumthread_id'],$forumthread['forumthread_lastreply_time']))
					{
					$threadstatus.="new";
						
					eval("\$newpostlink= \"".pkTpl("forum/showcat_thread_row_newpostlink")."\";");
					}
								
				if($forumthread['forumthread_replycount']>=$forumcat['forumcat_threads'] or $forumthread['forumthread_viewcount']>=$forumcat['forumcat_views'])
					$threadstatus.="hot";
				
				if($forumthread['forumthread_autorid']>0)
					$userinfo=$SQL->fetch_array($SQL->query("SELECT user_id, user_nick FROM ".pkSQLTAB_USER." WHERE user_id='".$forumthread['forumthread_autorid']."'"));
				
				
				if($userinfo['user_id'])
					{
					$userinfo['user_nick']=pkEntities($userinfo['user_nick']);
					eval("\$thread_autor= \"".pkTpl("member_showprofil_textlink")."\";");
					}
				else
					$thread_autor=pkEntities($forumthread['forumthread_autor']);
				
				unset($userinfo);
				
				if($forumthread['forumthread_lastreply_autorid']!=0)
					{
					$userinfo=$SQL->fetch_array($SQL->query("SELECT user_id, user_nick FROM ".pkSQLTAB_USER." WHERE user_id='".$forumthread['forumthread_lastreply_autorid']."'"));
					}
				
				if($userinfo['user_id'])
					{
					$userinfo['user_nick']=pkEntities($userinfo['user_nick']);
					
					eval ("\$thread_last_autor= \"".pkTpl("member_showprofil_textlink")."\";");
					}
				else
					$thread_last_autor=pkEntities($forumthread['forumthread_lastreply_autor']);
					
				$thread_time=formattime($forumthread['forumthread_lastreply_time']);
				$thread_title=pkEntities($forumthread['forumthread_title']);
					
				eval("\$thread_row.= \"".pkTpl("forum/favorits_row")."\";");
				
				unset($sidelink);
				unset($userinfo);
				unset($thread_last_autor);
				unset($i);
				unset($newpostlink);
				}
			else
				{
				$SQL->query("DELETE FROM ".pkSQLTAB_FORUM_FAVORITE." WHERE forumfav_userid='".$SQL->i(pkGetUservalue('id'))."' AND forumfav_threadid='".$favinfo['forumfav_threadid']."'");
				}
			}
		
		if($thread_row!="")
			{
			eval ("\$site_body.= \"".pkTpl("forum/favorits")."\";");
			}
		}
	}
else
	{
	pkEvent('access_refused');
	}


include(pkDIRPUBLICINC.'forumsfooter'.pkEXT);
?>