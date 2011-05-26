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


#force subcat to be displayed
$mode_forumsubcategory=1;
$subcat_row=$thread_row='';


$entries=(isset($_REQUEST['entries']) && intval($_REQUEST['entries'])>0) ? intval($_REQUEST['entries']) : 0;


include(pkDIRPUBLICINC.'forumsubcategory'.pkEXT);


if($config['forum_showmod']==1)
	{
	$mc=50; 
	eval("\$subcat_mod_col= \"".pkTpl("forum/showcat_subcat_mod_col")."\";");
	}
else
	{
	$mc=60;
	}

if(!empty($subcat_row))
	{
	eval("\$showcat_subcat= \"".pkTpl("forum/showcat_subcat_headfull")."\";");
	}


if((getrights($forumcat['forumcat_rrights'])=="true" || userrights($forumcat['forumcat_mods'],$forumcat['forumcat_rrights'])=="true" || userrights($forumcat['forumcat_user'],$forumcat['forumcat_rrights'])=="true"))
	{
	$threads=$SQL->fetch_array($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_FORUM_THREAD." WHERE forumthread_catid='".$catid."'"));

	if($forumcat['forumcat_threads_option']==1)
		{
		$getforuminfo=$SQL->query("SELECT * FROM ".pkSQLTAB_FORUM_INFO." WHERE foruminfo_catids LIKE '%-".$forumcat['forumcat_id']."-%' OR foruminfo_catids=0");
		while($foruminfo=$SQL->fetch_array($getforuminfo))
			{
			$info_time=formattime($foruminfo['foruminfo_time']);
			$foruminfo['foruminfo_title']=pkEntities($foruminfo['foruminfo_title']);
			
			eval("\$thread_row.= \"".pkTpl("forum/showcat_info_row")."\";");
			
			unset($info_time);
			}
		}
	
	$fixed=$SQL->fetch_array($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_FORUM_THREAD." WHERE forumthread_catid='".$catid."' AND (forumthread_status=2 OR forumthread_status=3)"));
	$entfixed=$entries; 
	$entother=$entries-$fixed[0];
	
	if($entother<0)
		$entother=0;
	
	if(($fixed[0]-$entries)>=$forumcat['forumcat_threads'])
		{
		$maxfixed=$forumcat['forumcat_threads'];
		$maxother=0;
		}
	else
		{
		$maxfixed=$fixed[0]-$entfixed; 
		
		if($maxfixed<0)
			$maxfixed=0;
		
		$maxother=$forumcat['forumcat_threads']-$maxfixed;
		}
	
	if($maxfixed==0)
		$loaded=1;
	unset($sqlcommand);
	
	while($loaded<2)
		{
		if($loaded==1)
			{
			$sql="SELECT * FROM ".pkSQLTAB_FORUM_THREAD." WHERE forumthread_catid='".$catid."' AND (forumthread_status=0 OR forumthread_status=1) ORDER BY forumthread_lastreply_time DESC LIMIT ".$entother.",".$maxother;
			$loaded=2;
			}
		else
			{
			$sql="SELECT * FROM ".pkSQLTAB_FORUM_THREAD." WHERE forumthread_catid='".$catid."' AND (forumthread_status=2 OR forumthread_status=3) ORDER BY forumthread_lastreply_time DESC LIMIT ".$entfixed.",".$maxfixed;
			$loaded=1;
			}
		
		$getforumthread=$SQL->query($sql);
		while($forumthread=$SQL->fetch_array($getforumthread))
			{
			$thread_cache[]=$forumthread;
			
			if($forumthread['forumthread_lastreply_autorid']>0)
				{
				if($sqlcommand)
					$sqlcommand.=" OR user_id='".$forumthread['forumthread_lastreply_autorid']."'";
				else
					$sqlcommand=" user_id='".$forumthread['forumthread_lastreply_autorid']."'";
				}
			
			if($forumthread['forumthread_autorid']>0)
				{
				if($sqlcommand)
					$sqlcommand.=" OR user_id='".$forumthread['forumthread_autorid']."'";
				else
					$sqlcommand=" user_id='".$forumthread['forumthread_autorid']."'";
				}
			}
		}
	
	if($sqlcommand)
		{
		$getuserinfo=$SQL->query("SELECT user_id, user_nick FROM ".pkSQLTAB_USER." WHERE ".$sqlcommand);
		while($userinfo=$SQL->fetch_array($getuserinfo))
			{
			$user_cache[$userinfo['user_id']]=$userinfo;
			}
		}
	
	
	if(is_array($thread_cache))
		{
		foreach($thread_cache as $forumthread)
			{
			if($forumthread['forumthread_title']=='' || $forumthread['forumthread_autor']=='')
				{
				$info=$SQL->fetch_array($SQL->query("SELECT forumpost_title, forumpost_autor, forumpost_autorid 
					FROM ".pkSQLTAB_FORUM_POST." 
					WHERE forumpost_threadid='".$forumthread['forumthread_id']."' 
					ORDER by forumpost_time ASC 
					LIMIT 1"));
					
				$SQL->query("UPDATE ".pkSQLTAB_FORUM_THREAD."
					SET forumthread_title='".$SQL->f($info['forumpost_title'])."',
						forumthread_autor='".$SQL->f($info['forumpost_autor'])."',
						forumthread_autorid='".intval($info['forumpost_autorid'])."'
					WHERE forumthread_id='".$forumthread['forumthread_id']."'");
				
				$forumthread['forumthread_title']=$info['forumpost_title'];
				$forumthread['forumthread_autor']=$info['forumpost_autor'];
				$forumthread['forumthread_autorid']=$info['forumpost_autorid'];
				}
			
			if($forumthread['forumthread_lastreply_time']>$savecatinfo[0] && $entries==0)
				{
				$savecatinfo[0]=$forumthread['forumthread_lastreply_time'];
				$savecatinfo[1]=$forumthread['forumthread_id'];
				$savecatinfo[2]=$forumthread['forumthread_lastreply_autor'];
				$savecatinfo[3]=$forumthread['forumthread_lastreply_autorid'];
				}
			
			$thread_replys=$forumthread['forumthread_replycount'];
			$posts[0]=$forumthread['forumthread_replycount']+1;
			
			if($forumthread['forumthread_replycount']>$forumcat['forumcat_posts'] && $FORUM->getLayout()!=1)
				$sidelink=sidelink($posts[0], $forumcat['forumcat_posts'],0,'include.php?path=forumsthread&threadid='.$forumthread['forumthread_id']);
			
			if($forumthread['forumthread_icon']=='')
				$forumthread_icon='blank.gif';
			else
				$forumthread_icon="icons/".basename($forumthread['forumthread_icon']);
			
			if($forumthread['forumthread_status']==0 || $forumthread['forumthread_status']==3)
				$threadstatus='close';
			else
				$threadstatus='open';
			
			if($forumthread['forumthread_status']==2 || $forumthread['forumthread_status']==3)
				$threadstatus.='fixed';
			
			if($FORUM->isUnreadedThread($forumthread['forumthread_catid'],$forumthread['forumthread_id'],$forumthread['forumthread_lastreply_time']))
				{
				$threadstatus.='new';
				eval("\$newpostlink= \"".pkTpl("forum/showcat_thread_row_newpostlink")."\";");
				}
			
			if($forumthread['forumthread_replycount']>=$forumcat['forumcat_replys'] || $forumthread['forumthread_viewcount']>=$forumcat['forumcat_views'])
				$threadstatus.='hot';
			
			if($forumthread['forumthread_autorid']!=0)
				$userinfo=$user_cache[$forumthread['forumthread_autorid']];
			
			if($userinfo['user_id']>0)
				{
				$userinfo['user_nick']=pkEntities(pkStringCut($userinfo['user_nick'],$config['forum_threadautor_cut']));
				
				eval("\$thread_autor= \"".pkTpl("member_showprofil_textlink")."\";");
				unset($userinfo);
				}
			else
				$thread_autor=pkEntities(pkStringCut($forumthread['forumthread_autor'],$config['forum_threadautor_cut']));
			
			if($forumthread['forumthread_lastreply_autorid']>0)
				$userinfo=$user_cache[$forumthread['forumthread_lastreply_autorid']];
			
			if(trim($userinfo['user_nick'])!='')
				{
				$userinfo['user_nick']=pkEntities(pkStringCut($userinfo['user_nick'],$config['forum_threadautor_cut']));
				$thread_last_autor=$lang['by'].' ';
				
				eval("\$thread_last_autor.= \"".pkTpl("member_showprofil_textlink")."\";");
				}
			else
				$thread_last_autor=$lang['by'].' '.pkEntities(pkStringCut($forumthread['forumthread_lastreply_autor'],$config['forum_threadautor_cut']));
				
			$thread_time=formattime($forumthread['forumthread_lastreply_time']);
			$thread_title=pkEntities($forumthread['forumthread_title']);
			
			eval("\$thread_row.= \"".pkTpl("forum/showcat_thread_row")."\";");
			
			unset($thread_title);
			unset($userinfo);
			unset($sidelink);
			unset($userinfo);
			unset($thread_last_autor);
			unset($i);
			unset($newpostlink);
			}
		
		$sidelink=sidelinkfull($threads[0], $forumcat['forumcat_threads'], $entries, 'include.php?path=forumscategory&catid='.$catid,'sitebodysmall');
		}
	
	if(!empty($thread_row))
		eval("\$showcat_threads=\"".pkTpl("forum/showcat_thread")."\";");
	
	if($showcat_subcat!='' && $showcat_threads!='')
		$showcat_subcat.='<br />';
	elseif($showcat_subcat=='' && $showcat_threads=='')
		eval("\$site_body.= \"".pkTpl("forum/showcat_empty")."\";");


	$postings=$SQL->fetch_array($SQL->query("SELECT SUM(forumthread_replycount) FROM ".pkSQLTAB_FORUM_THREAD." WHERE forumthread_catid='".$catid."'"));
	$postings=$postings[0]+$threads[0];
	$threadcount=$threads[0];
		
	if($savecatinfo[0]!=0)
		$sqlcommand=",forumcat_lastreply_time='".intval($savecatinfo[0])."',
		forumcat_lastreply_threadid='".intval($savecatinfo[1])."',
		forumcat_lastreply_autor='".$SQL->f($savecatinfo[2])."',
		forumcat_lastreply_autorid='".intval($savecatinfo[3])."'";
	else
		unset($sqlcommand);

	$SQL->query("UPDATE ".pkSQLTAB_FORUM_CATEGORY."
		SET forumcat_threadcount='".$threadcount."',
			forumcat_postcount='".$postings."'".
			$sqlcommand."
		WHERE forumcat_id='".$forumcat['forumcat_id']."'");
	}
elseif(empty($subcat_row))
	{
	pkEvent('access_refused');
	}
	
eval("\$site_body.= \"".pkTpl("forum/showcat")."\";");

include(pkDIRPUBLICINC.'forumsfooter'.pkEXT);
?>