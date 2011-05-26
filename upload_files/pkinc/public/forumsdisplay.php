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


$path='forumsdisplay';

include(pkDIRPUBLICINC.'forumsheader'.pkEXT);


if(is_array($forumcat_cache) && !empty($forumcat_cache))
	{
	$threads=$users=array();
	
	foreach($FORUM->getTree() as $forumcat)
		{
		if($i=$forumcat['tree']['threadid']) 
			$threads[$i]=$i;

		if($i=$forumcat['tree']['authorid'])
			$users[$i]=$i;
		}

	if(!empty($threads))
		{
		$result=$SQL->query("SELECT 
			forumthread_id,
			forumthread_title,
			forumthread_lastreply_autorid,
			forumthread_lastreply_time,
			forumthread_lastreply_autor
			FROM ".pkSQLTAB_FORUM_THREAD." WHERE forumthread_id IN(".implode(',',$threads).")");
		while($thread=$SQL->fetch_assoc($result))
			{
			$forumthread_cache[$thread['forumthread_id']]=$thread;
			}
		}
	
	if(!empty($users))
		{
		$result=$SQL->query("SELECT 
			user_id,
			user_nick
			FROM ".pkSQLTAB_USER." WHERE user_id IN(".implode(',',$users).")");
		while($user=$SQL->fetch_assoc($result))
			$userinfo_cache[$user['user_id']]=$user;			
		}

		
	foreach($forumcat_cache as $forumcat)
		{
		if($forumcat['forumcat_subcat']=="0")
			{
			$mode_forumsubcategory=$forumcat['forumcat_option'];
			include(pkDIRPUBLICINC.'forumsubcategory'.pkEXT);
			
			if($FORUM->getCategoryRrights($forumcat['forumcat_id']) || $subcat_row!='')
				{
				if($forumcat['forumcat_status']==1)
					{
					if($FORUM->isUnreadedCategory($forumcat['forumcat_id']))
						{
						$cat_icon="catnew"; 
						$cat_icon_alt=$lang['open'];
						}
					else
						{
						$cat_icon="catopen"; 
						$cat_icon_alt=$lang['open'];
						}
					}
				else
					{
					$cat_icon="catclose";
					$cat_icon_alt=$lang['closed'];
					}
				
				if($forumcat['forumcat_description_show']==1)
					{
					$cat_description=$forumcat['forumcat_description']; 
					
					eval("\$cat_description=\"".pkTpl("forum/main_row_description")."\";");
					}
				
				$threads=$forumcat['forumcat_threadcount'];
				$postings=$forumcat['forumcat_postcount'];
				
				
				if(!$forumcat['forumcat_lastreply_threadid'] || $forumthread_cache[$forumcat['forumcat_lastreply_threadid']]==0)
					{
					eval("\$cat_reply_info= \"".pkTpl("forum/main_thread_empty")."\";");
					}
				else
					{
					$forumthread=$forumthread_cache[$forumcat['forumcat_lastreply_threadid']];
					$userinfo=$userinfo_cache[$forumthread['forumthread_lastreply_autorid']];
					
					$forumthread_title=pkEntities(pkStringCut($forumthread['forumthread_title'],$config['forum_threadtitle_cut']));
					$forumthread_time=pkTimeFormat($forumthread['forumthread_lastreply_time']);
					
					if($forumthread['forumthread_lastreply_autorid']>0 && !empty($userinfo['user_nick']))
						{
						$forumthread_autor=pkEntities(pkStringCut($userinfo['user_nick'],$config['forum_threadautor_cut']));
					
						eval("\$forumthread_autor= \"".pkTpl("forum/main_row_autor")."\";");
						}
					else
						{
						$forumthread_autor=pkEntities(pkStringCut($forumthread['forumthread_lastreply_autor'],$config['forum_threadautor_cut']));
						
						eval("\$forumthread_autor= \"".pkTpl("forum/main_row_guestautor")."\";");
						}
					
					eval("\$cat_reply_info= \"".pkTpl("forum/main_thread_link")."\";");
					}
				
				
				if($config['forum_showmod']==1)
					{
					$mods=new moderators(); 
					$cat_mod=$mods->getMods(1,$forumcat['forumcat_id']);
					
					eval("\$mod_col2= \"".pkTpl("forum/main_mod_col2")."\";");
					}
					
				$forumcat_name=pkEntities($forumcat['forumcat_name']);
				$forumcat_threadcount=$FORUM->getCategoryThreadcount($forumcat['forumcat_id']);
				$forumcat_postcount=$FORUM->getCategoryPostcount($forumcat['forumcat_id']);

	
				eval("\$main_row.= \"".pkTpl("forum/main_row")."\";");
				
				$main_row.=$subcat_row;
				
				unset($savecatinfo);
				unset($threads);
				unset($subcat_threadcount);
				unset($postings);
				unset($subcat_postcount);
				unset($cat_description);
				unset($mods);
				unset($cat_mod);
				unset($cat_reply);
				unset($subcat_row);
				unset($cat_reply_autor);
				unset($cat_reply_time);
				unset($cat_reply_autor);
				unset($cat_reply_thread);
				unset($forumthread);
				unset($forumthread_title);
				unset($forumthread_time);
				unset($forumthread_autor);
				unset($cat_reply_info);
				unset($mods);
				}
			}
		}
	}


if($config['forum_showmod']=="1")
	{
	$mc="50";
	eval("\$mod_col= \"".pkTpl("forum/main_mod_col")."\";");
	}	
else
	$mc="60";


eval ("\$site_body.= \"".pkTpl("forum/main")."\";");

include(pkDIRPUBLICINC.'forumsfooter'.pkEXT);
?>