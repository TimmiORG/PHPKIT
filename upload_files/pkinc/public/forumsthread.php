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


include(pkDIRPUBLICINC.'forumsheader'.pkEXT);


if(!$threadid>0 || ($threadid && !$forumthread['forumthread_id']))
	{
	pkEvent('thread_does_not_exists');
	include(pkDIRPUBLICINC.'forumsfooter'.pkEXT);
	return;
	}

if(!$FORUM->getCategoryRrights($catid))
	{
	pkEvent('access_refused');
	include(pkDIRPUBLICINC.'forumsfooter'.pkEXT);	
	return;
	}

	
#	if(!empty($forumthread['forumthread_title']))
#		$config['site_title'].=' ('.pkGetLang('forumsthread').$forumthread['forumthread_title'].')';
	
if(userrights($forumcat['forumcat_mods'])=="true" && $_POST['actionmod']==pkGetLang('go') && isset($_POST['quick_mod']) && $_POST['quick_mod']!="-1")
	{
	$quick_mod=$_POST['quick_mod'];
		
	if($quick_mod=='cs0' || $quick_mod=='cs1' || $quick_mod=='cs2' || $quick_mod=='cs3')
		{
		if($quick_mod=='cs0')
			$cs=0;
		elseif($quick_mod=='cs1')
			$cs=1;
		elseif($quick_mod=='cs2')
			$cs=2;
		elseif($quick_mod=='cs3')
			$cs=3;
			
		$SQL->query("UPDATE ".pkSQLTAB_FORUM_THREAD." SET forumthread_status='".$cs."' WHERE forumthread_id='".$threadid."'");
		}
	elseif(intval($quick_mod)>0)
		$SQL->query("UPDATE ".pkSQLTAB_FORUM_THREAD." SET forumthread_catid='".intval($quick_mod)."' WHERE forumthread_id='".$threadid."'");
	elseif($quick_mod=="del")
		pkHeaderLocation('forumsmoderate','','threadid='.$threadid.'&alter_delete=1');
	
	pkHeaderLocation('forumsthread','','threadid='.$threadid);
	}
	
$entries=(isset($_REQUEST['entries']) &&  intval($_REQUEST['entries'])>0) ? intval($_REQUEST['entries']) : 0;

if(isset($_REQUEST['postid']))
	{
	$postid=($_REQUEST['postid']=='new' || $_REQUEST['postid']=='last') ? $_REQUEST['postid'] : (intval($_REQUEST['postid'])>0 ? intval($_REQUEST['postid']) : 0);
		

	if($postid=='new')
		$sqlcommand="AND forumpost_time>='".$FORUM->getUnreadedThreadtime($catid,$threadid)."' ORDER BY forumpost_time ".pkGetConfig('forum_postorder');
	elseif($postid=='last')
		$sqlcommand="ORDER BY forumpost_time DESC";
	else
		$sqlcommand=" AND forumpost_id='".$postid."'";


	$info=$SQL->fetch_assoc($SQL->query("SELECT
			forumpost_time,
			forumpost_id FROM ".pkSQLTAB_FORUM_POST." 
		WHERE forumpost_threadid='".$threadid."' ".
			$sqlcommand."
		LIMIT 1"));
		
	if($info['forumpost_id']>0)
		{
		list($counter)=$SQL->fetch_row($SQL->query("SELECT
				COUNT(*) 
			FROM ".pkSQLTAB_FORUM_POST."
			WHERE forumpost_time".(pkGetConfig('forum_postorder')=='ASC' ? '<': '>')."='".$info['forumpost_time']."' AND 
				forumpost_threadid='".$threadid."'"));
				
		$epp=($forumcat['forumcat_posts']<=0) ? 15 : $forumcat['forumcat_posts'];
		$entries=floor(($counter-1)/$epp)*$epp;

		if($FORUM->getLayout()!=1)
			{
			pkHeaderLocation('forumsthread','','threadid='.$threadid.'&entries='.$entries,'post'.$info['forumpost_id']);
			}
		}
	else
		unset($postid);
	}
	
$postcount=$SQL->fetch_array($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_FORUM_POST." WHERE forumpost_threadid='".$threadid."'"));
$replycount=$postcount[0]-1;
	
	
if(!$postcount[0]>0 && !$catid)
	pkHeaderLocation('forumsdisplay');

if(!$postcount[0]>0 && $catid)
	pkHeaderLocation('forumscategory','','catid='.$catid);

		

pkLoadClass($BBCODE,'bbcode');
pkLoadFunc('user');
		
		
if(stripos('showthread',$record_referer) === FALSE && stripos('threadid='.$threadid,$record_referer) === FALSE)
	{
	$lastpost=$SQL->fetch_array($SQL->query("SELECT forumpost_autor, forumpost_autorid, forumpost_time FROM ".pkSQLTAB_FORUM_POST." WHERE forumpost_threadid='".$threadid."' ORDER by forumpost_time DESC LIMIT 1"));

	$SQL->query("UPDATE ".pkSQLTAB_FORUM_THREAD." 
		SET forumthread_viewcount=forumthread_viewcount+1,
			forumthread_replycount='".$replycount."',
			forumthread_lastreply_autor='".$SQL->f($lastpost['forumpost_autor'])."',
			forumthread_lastreply_autorid='".intval($lastpost['forumpost_autorid'])."',
			forumthread_lastreply_time='".intval($lastpost['forumpost_time'])."'
		WHERE forumthread_id='".$threadid."'");
	}
		

$sqlcommand=($FORUM->getLayout()!=1) ? " LIMIT ".$entries.", ".$forumcat['forumcat_posts'] : '';
$getforumpost=$SQL->query("SELECT * FROM ".pkSQLTAB_FORUM_POST." WHERE forumpost_threadid='".$forumthread['forumthread_id']."' ORDER by forumpost_time ".$config['forum_postorder'].$sqlcommand);
		
unset($firstpost);
unset($sqlcommand);
		
while($forumpost=$SQL->fetch_array($getforumpost))
	{
	$post_cache[$forumpost['forumpost_id']]=$forumpost;
			
	if(!$firstpost)
		$firstpost=$forumpost;
			
	if($forumpost['forumpost_autorid']>0)
		{
		if($sqlcommand)
			$sqlcommand.=" OR user_id='".$forumpost['forumpost_autorid']."'";
		else
			$sqlcommand="SELECT * FROM ".pkSQLTAB_USER." WHERE user_id='".$forumpost['forumpost_autorid']."'";
		}
	}
		
if($sqlcommand)
	{
	$getuserinfo=$SQL->query($sqlcommand); 
	while($userinfo=$SQL->fetch_array($getuserinfo))
		{
		$user_cache[$userinfo['user_id']]=$userinfo;
		}
	}

		
foreach($post_cache as $forumpost)
	{
	if($FORUM->getLayout()==0 || ($FORUM->getLayout()==1 && ($postid==$forumpost['forumpost_id'] || $postid=="")))
		{
		$row=rowcolor($row);
				
		if($forumpost['forumpost_autorid']>0)
			$userinfo=$user_cache[$forumpost['forumpost_autorid']];
				
		if($userinfo['user_id']>0)
			{
			$userinfo['user_nick']=pkEntities($userinfo['user_nick']);
					
			if(isonline($userinfo['user_id']))
				eval("\$info_os= \"".pkTpl("member_os_online")."\";");
			else
				eval("\$info_os= \"".pkTpl("member_os_offline")."\";");
					
			if($userinfo['user_status']=='admin' && $userinfo['user_sex']=='w')
				eval("\$post_autor_status= \"".pkTpl("forum/showthread_userstatus_admin_w")."\";");
			elseif($userinfo['user_status']=='admin')
				eval("\$post_autor_status= \"".pkTpl("forum/showthread_userstatus_admin")."\";");
			elseif($userinfo['user_status']=="mod" && $userinfo['user_sex']=='w')
				eval("\$post_autor_status= \"".pkTpl("forum/showthread_userstatus_mod_w")."\";");
			elseif($userinfo['user_status']=="mod")
				eval("\$post_autor_status= \"".pkTpl("forum/showthread_userstatus_mod")."\";");
					
					
			if($userinfo['user_posts']==0)
				{
				$postings=$SQL->fetch_array($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_FORUM_POST." WHERE forumpost_autorid='".$userinfo['user_id']."'"));
						
				$SQL->query("UPDATE ".pkSQLTAB_USER." SET user_posts='".$postings[0]."' WHERE user_id='".$userinfo['user_id']."' LIMIT 1");
				$userinfo['user_posts']=$postings[0];
				}
					
			$post_count=postcount($userinfo['user_posts'],$userinfo['user_postdelay'],0);
			
			if(trim($userinfo['user_hpage'])!='')
				{
				if(stripos("http://",$userinfo['user_hpage']) !== FALSE)
					$info_link=pkEntities($userinfo['user_hpage']);
				else
					$info_link='http://'.pkEntities($userinfo['user_hpage']);

				eval("\$info_hpage= \"".pkTpl("forum/member_hpage_iconlink")."\";");
				}
					
			if($userinfo['user_emailshow']==1)
				{
				if($config['member_mailer']==1)
					eval("\$info_email= \"".pkTpl("forum/member_email_iconlink2")."\";");
				else
					eval("\$info_email= \"".pkTpl("forum/member_email_iconlink")."\";");
				}
					
			if($userinfo['user_icqid']>0)
				eval("\$info_icq= \"".pkTpl("forum/member_icq_iconlink")."\";");
				
			if($userinfo['user_imoption']==1)
				eval("\$info_im= \"".pkTpl("forum/member_sendim_iconlink")."\";");
					
			if($config['avatar_eod']!=0 && $userinfo['user_avatar']!="" && filecheck($config['avatar_path'].'/'.$userinfo['user_avatar']))
				{
				$avatar_dimension=@getimagesize($config['avatar_path']."/".$userinfo['user_avatar']);
						
				eval("\$avatar_show=\"".pkTpl("user_avatar_show")."\";");
				}
					
					
			$info_sig=pkUserSignature($userinfo['user_sig']); 
					
			eval("\$info_user=\"".pkTpl("forum/member_userinfo_iconlink")."\";");	 
			eval("\$post_autor=\"".pkTpl("forum/member_showprofil_textlink")."\";");
			eval("\$info_buddie=\"".pkTpl("forum/member_buddie_iconlink")."\";");
			}
				
				
		if(($forumthread['forumthread_status']==1 || $forumthread['forumthread_status']==2) && (getrights($forumcat['forumcat_wrights'])=="true" || userrights($forumcat['forumcat_mods'],$forumcat['forumcat_rrights'])=="true" || userrights($forumcat['forumcat_user'],$forumcat['forumcat_rrights'])=="true"))
			eval("\$quote_answer=\"".pkTpl("forum/showthread_quote")."\";");
				
		if((pkGetUservalue('id') && $forumpost['forumpost_autorid']==pkGetUservalue('id') && ($forumthread['forumthread_status']==1 || $forumthread['forumthread_status']==2)) || userrights($forumcat['forumcat_mods'])=="true")
			eval("\$post_edit= \"".pkTpl("forum/showthread_edit")."\";");
					
		if($forumpost['forumpost_icon']!='')
			{
			$post_icon="icons/".$forumpost['forumpost_icon'];
					
			eval("\$post_icon= \"".pkTpl("forum/showthread_row_posticon")."\";");
			}
				
		if($post_autor=='')
			{
			eval("\$info_os= \"".pkTpl("guest_os_icon")."\";"); 
					
			$post_autor=pkEntities($forumpost['forumpost_autor']);
			$post_count=$lang['guest'];
			}
			
		if($forumpost['forumpost_editcount']>0)
			{
			$edit_time=formattime($forumpost['forumpost_edittime']);
			$forumpost['forumpost_editautor']=pkEntities($forumpost['forumpost_editautor']);
					
			eval("\$edit_message= \"".pkTpl("forum/showthread_row_editmessage")."\";");
			}
			
		if(userrights($forumcat['forumcat_mods'])=="true")
			eval("\$post_ip= \"".pkTpl("forum/showthread_ip")."\";");
		else
			eval("\$post_ip= \"".pkTpl("forum/showthread_report")."\";");
				
				
		$post_time=formattime($forumpost['forumpost_time']);
		$post_title=pkEntities($forumpost['forumpost_title']);
		$post_text=$BBCODE->parse($forumpost['forumpost_text'],0,$forumpost['forumpost_bbcode'],$forumpost['forumpost_smilies'],$config['forum_images'],1,pkGetConfig('forum_imageresize'),pkGetConfig('forum_textwrap'));

		eval("\$showthread_row.= \"".pkTpl("forum/showthread_row")."\";");
			
		$lastreadedposting=$forumpost['forumpost_time']; 
				
		unset($avatar_show);
		unset($post_icon);
		unset($info_sig);
		unset($post_autor_status);
		unset($edit_time);
		unset($post_count);
		unset($post_autor);
		unset($info_os);
		unset($userinfo);
		unset($post_edit);
		unset($info_user);
		unset($info_email);
		unset($info_im);
		unset($info_hpage);
		unset($info_icq);
		unset($info_buddie);
		unset($edit_message);
		unset($post_ip);
				
		if($FORUM->getLayout()==1) 
			break;
		}
	}
		
		
$FORUM->setReaded($catid,$threadid,$lastreadedposting);
		
		
if($FORUM->getLayout()==1)
	{
	foreach($post_cache as $forumpost)
		{
		if(!$postid>0)
			$postid=$firstpost['forumpost_id'];
				
		if($forumpost['forumpost_autorid']>0)
			$userinfo=$user_cache[$forumpost['forumpost_autorid']];
				
		if($userinfo['user_id']>0)
			{
			$userinfo['user_nick']=pkEntities($userinfo['user_nick']);
			eval("\$post_autor= \"".pkTpl("forum/member_showprofil_textlink")."\";");
			}
				
		if($post_autor=='')
			{
			$post_autor=pkEntities($forumpost['forumpost_autor']);
			$post_count=$lang['guest'];
			}
				
		if(empty($forumpost['forumpost_title']))
			$forumpost['forumpost_title']=pkEntities($firstpost['forumpost_title']);
		else
			$forumpost['forumpost_title']=pkEntities($forumpost['forumpost_title']);
				
		if($forumpost['forumpost_id']!=$firstpost['forumpost_id'])
			{
			eval("\$tree_spacer= \"".pkTpl("forum/showthread_tree_row_spacer")."\";");
			
			if($forumpost['forumpost_reply']>0)
				{
				$replyto=$forumpost['forumpost_reply']; 
						
				while($replyto>0)
					{
					eval("\$tree_spacer.= \"".pkTpl("forum/showthread_tree_row_iconline")."\";");
							
					$id=$post_cache[$replyto];
					$replyto=$id['forumpost_reply'];
					}
				}
			}
				
		if($forumpost['forumpost_icon']!='')
			{
			$post_icon='icons/'.$forumpost['forumpost_icon'];
					
			eval("\$post_icon= \"".pkTpl("forum/showthread_tree_row_posticon")."\";");
			}
		else
			eval("\$post_icon= \"".pkTpl("forum/showthread_tree_row_icondir")."\";");
				
				
		$post_time=formattime($forumpost['forumpost_time']);
				
		$link=pkLink('forumsthread','','threadid='.$threadid.'&postid='.$forumpost['forumpost_id'],'post'.$forumpost['forumpost_id']);

				
		if($postid==$forumpost['forumpost_id'])
			eval("\$showthread_tree_row.= \"".pkTpl("forum/showthread_tree_row_highlight")."\";");
		else
			eval("\$showthread_tree_row.= \"".pkTpl("forum/showthread_tree_row")."\";");
				
		unset($post_time);
		unset($post_autor);
		unset($tree_spacer);
		unset($id);
		}
			
	eval("\$showthread_tree= \"".pkTpl("forum/showthread_tree")."\";");
	}
else 
	$sidelink=sidelinkfull($postcount[0],$forumcat['forumcat_posts'],$entries,'include.php?path=forumsthread&threadid='.$forumthread['forumthread_id'],'sitebodysmall');
		
if(pkGetUservalue('sigoption'))
	{
	$setsig=0;
	$sigoption=$lang['hide'];
	}
else
	{
	$setsig=1;
	$sigoption=$lang['show'];
	}
			
$threadinformation=pkGetSpecialLang('threadinformation',$postcount[0],$forumthread['forumthread_status']);
$current_path=pkEntities($ENV->getvar('QUERY_STRING'));
		
if($FORUM->getLayout()==1)
	eval("\$board_style=\"".pkTpl("forum/showthread_boardstyle_board")."\";");
else
	eval("\$board_style=\"".pkTpl("forum/showthread_boardstyle_tree")."\";");


$forumthread['forumthread_title']=pkEntities($forumthread['forumthread_title']);
		
if(pkGetUservalue('id'))
	{
	eval("\$add_favorit=\"".pkTpl("forum/showthread_addfavorit")."\";");

	if(userrights($forumcat['forumcat_mods']))
		{
		if(is_array($forumcat_cache_byname))
			{
			foreach($forumcat_cache_byname as $catinfo)
				{
				if((getrights($catinfo['forumcat_rrights'])=="true" || userrights($catinfo['forumcat_mods'],$catinfo['forumcat_rrights'])=="true" || userrights($catinfo['forumcat_user'],$catinfo['forumcat_rrights'])=="true") && $catinfo['forumcat_id']!=$catid)
					{
					$catinfo['forumcat_name']=str_repeat('-',$catinfo['level']).' '.pkEntities($catinfo['forumcat_name']);
						
					eval("\$quickmod_catlist.= \"".pkTpl("forum/showthread_quickmod_option")."\";");
					}
				}
			}
			
		$lang_go=pkGetLang('go');
				
		eval("\$showthread_quickmod=\"".pkTpl("forum/showthread_quickmod")."\";");
		eval("\$thread_moderate=\"".pkTpl("forum/showthread_moderate")."\";");
		}
	}
		
$removeto=pkEntities(urlencode($ENV->getvar('QUERY_STRING')));
	
eval("\$site_body.= \"".pkTpl("forum/showthread")."\";");
		

include(pkDIRPUBLICINC.'forumsfooter'.pkEXT);
?>