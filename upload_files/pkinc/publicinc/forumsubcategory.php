<?php
# PHPKIT WCMS | Web Content Management System
#
#
# YOU ARE NOT AUTHORISED TO CREATE ILLEGAL COPIES OF THIS
# FILE AND/OR TO REMOVE THIS INFORMATION
#
# SIE SIND NICHT BERECHTIGT, UNRECHTM�SSIGE KOPIEN DIESER
# DATEI ZU ERSTELLEN UND/ODER DIESE INFORMATIONEN ZU ENTFERNEN
#
# This file / the PHPKIT software is no freeware! For further 
# information please visit our website or contact us via email:
#
# Diese Datei / die PHPKIT Software ist keine Freeware! F�r weitere
# Informationen besuchen Sie bitte unsere Website oder kontaktieren uns per E-Mail:
#
# email     : info@phpkit.com
# website   : http://www.phpkit.com
# licence   : http://www.phpkit.com/licence
# copyright : Copyright (c) 2002-2009 mxbyte gbr | http://www.mxbyte.com


if(!defined('pkFRONTEND') || pkFRONTEND!='public')
	die('Direct access to this location is not permitted.');

// presets
$sqlcommand = '';

$mode_forumsubcategory=(isset($mode_forumsubcategory) && (intval($mode_forumsubcategory)==2 || intval($mode_forumsubcategory)==1)) ? intval($mode_forumsubcategory) : 0;


switch($mode_forumsubcategory)
	{
	case 2 :
		if(!is_array($forumcat_cache))
			return;
		
		foreach($forumcat_cache as $forumsubcat)
			{
			if($forumsubcat['forumcat_subcat']==$forumcat['forumcat_id'])
				{
				$subcat_postcount=$subcat_postcount+$forumsubcat['forumcat_postcount'];
				$forumsubcat['forumcat_name']=pkEntities($forumsubcat['forumcat_name']);
					
				if($forumsubcat['forumcat_lastreply_time']>$savecatinfo[0])
					{
					$savecatinfo[0]=$forumsubcat['forumcat_lastreply_time'];
					$savecatinfo[1]=$forumsubcat['forumcat_lastreply_threadid'];
					$savecatinfo[2]=$forumsubcat['forumcat_lastreply_autor'];
					$savecatinfo[3]=$forumsubcat['forumcat_lastreply_autorid'];
					}
					
				if(getrights($forumsubcat['forumcat_rrights'])=="true" || userrights($forumsubcat['forumcat_mods'],$forumsubcat['forumcat_rrights'])=="true" || userrights($forumsubcat['forumcat_user'],$forumsubcat['forumcat_rrights'])=="true")
					eval("\$subcat_links.= \"".pkTpl("forum/subcat2_textlink")."\";");
					
				$subcat_threadcount=$subcat_threadcount+$forumsubcat['forumcat_threadcount'];
				}
			}
			
		if($subcat_links!='') 
			{
			if($path=='forumsdisplay')
				{
				$colspan=$config['forum_showmod']==1 ? ' colspan="2"' : '';
				eval("\$subcat_row.= \"".pkTpl("forum/subcat2_main")."\";");
				}
			else 
				eval("\$subcat_row.= \"".pkTpl("forum/subcat2_sub")."\";");
			}
		unset($subcat_links);	
		break;
	case 1 :
		if(!is_array($forumcat_cache))
			return;
		
		unset($save_new_threads);
		
		if(!$forumthread_cache || !$userinfo_cache)
			{
			$sqlcommand = '';
			
			foreach($forumcat_cache as $forumsubcat)
				{
				if($forumsubcat['forumcat_subcat']==$forumcat['forumcat_id'])
					{
					if($sqlcommand)
						$sqlcommand.=" OR forumthread_id='".$forumsubcat['forumcat_lastreply_threadid']."'";
					else
						$sqlcommand="SELECT * FROM ".pkSQLTAB_FORUM_THREAD." WHERE forumthread_id='".$forumsubcat['forumcat_lastreply_threadid']."'";
					}
				}
			
			
			if($sqlcommand)
				{
				$getforumthread=$SQL->query($sqlcommand);
				unset($sqlcommand);
				while($forumthread=$SQL->fetch_array($getforumthread))
					{
					$forumthread_cache[$forumthread['forumthread_id']]=$forumthread;
					
					if($sqlcommand)
						$sqlcommand.=" OR user_id='".$forumthread['forumthread_lastreply_autorid']."'";
					else
						$sqlcommand="SELECT user_nick, user_id FROM ".pkSQLTAB_USER." WHERE user_id='".$forumthread['forumthread_lastreply_autorid']."'";
					}
				}
			
			if($sqlcommand)
				{
				$getusernick=$SQL->query($sqlcommand);
				while($userinfo=$SQL->fetch_array($getusernick))
					{
					$userinfo_cache[$userinfo['user_id']]=$userinfo;
					}
				}
			}
		
		
		foreach($forumcat_cache as $forumsubcat)
			{
			if($forumsubcat['forumcat_subcat']==$forumcat['forumcat_id'])
				{
				$subcat_threadcount=$FORUM->getCategoryThreadcount($forumsubcat['forumcat_id']);
				$subcat_postcount=$FORUM->getCategoryPostcount($forumsubcat['forumcat_id']);
								
				
				if($forumsubcat['forumcat_lastreply_time']>$savecatinfo[0])
					{
					$savecatinfo[0]=$forumsubcat['forumcat_lastreply_time'];
					$savecatinfo[1]=$forumsubcat['forumcat_lastreply_threadid'];
					$savecatinfo[2]=$forumsubcat['forumcat_lastreply_autor'];
					$savecatinfo[3]=$forumsubcat['forumcat_lastreply_autorid'];
					}
				
				if(getrights($forumsubcat['forumcat_rrights'])=="true" || userrights($forumsubcat['forumcat_mods'],$forumsubcat['forumcat_rrights'])=="true" || userrights($forumsubcat['forumcat_user'],$forumsubcat['forumcat_rrights'])=="true")
					{
					if(!$forumsubcat['forumcat_lastreply_threadid'] || $forumthread_cache[$forumsubcat['forumcat_lastreply_threadid']]=='')
						{
						eval("\$cat_reply_info= \"".pkTpl("forum/main_thread_empty")."\";");
						}
					else
						{
						$forumthread=$forumthread_cache[$forumsubcat['forumcat_lastreply_threadid']];
						$userinfo=$userinfo_cache[$forumsubcat['forumcat_lastreply_autorid']];
						
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
					
					if($forumsubcat['forumcat_status']==1)
						{
						if($FORUM->isUnreadedCategory($forumsubcat['forumcat_id']))
							{
							$cat_icon="catnew";
							$cat_icon_alt="Forum offen und neue Posts";
							}
						else
							{
							$cat_icon="catopen";
							$cat_icon_alt="Forum offen";
							}
						}
					else
						{
						$cat_icon="catclose";
						$cat_icon_alt="Forum geschlossen";
						}
					
					
					if($config['forum_showmod']==1)
						{
						$mods=new moderators();
						$cat_mod=$mods->getMods(1,$forumsubcat['forumcat_id']);
						$mc="50";
						
						eval("\$mod_col= \"".pkTpl("forum/subcat_row_mod_col")."\";");
						}
					else
						$mc="60";
					
					
					if($forumsubcat['forumcat_description_show']==1 && !empty($forumsubcat['forumcat_description']))
						{
						$subcat_description=$forumsubcat['forumcat_description'];
						
						eval("\$subcat_description= \"".pkTpl("forum/subcat_row_description")."\";");
						}
						
					$forumsubcat['forumcat_name']=pkEntities($forumsubcat['forumcat_name']);
					
					
					eval("\$subcat_row.= \"".pkTpl("forum/subcat_row")."\";");
					
					unset($subcat_description);
					unset($cat_mod);
					unset($cat_reply_thread);
					unset($cat_reply_autor);
					unset($forumthread_title);
					unset($forumthread_time);
					unset($forumthread_autor);
					unset($cat_reply_info);
					}
				}
			}

		break;
	}
?>