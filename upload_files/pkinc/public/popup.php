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


$pkDISPLAYPOPUP=true;

$modehash=array('smilies','finduser','help','forumsticker');
$mode=(isset($_REQUEST['mode']) && in_array($_REQUEST['mode'],$modehash)) ? $_REQUEST['mode'] : NULL;

$window_w_size=(isset($_REQUEST['window_w_size']) && intval($_REQUEST['window_w_size'])>0) ? intval($_REQUEST['window_w_size']) : 0;
$window_h_size=(isset($_REQUEST['window_h_size']) && intval($_REQUEST['window_h_size'])>0) ? intval($_REQUEST['window_h_size']) : 0;


switch($mode)
	{
	case 'forumsticker' :
		pkLoadClass($FORUM,'forum');
		pkLoadLang('forum');
		
		$forumsticker_bit=$rowclass='';
		
		$lang_in_forum=pkGetLang('in_forum');
		$lang_last_posttime=pkGetLang('last_posttime');
		
		$result=$SQL->query("SELECT 
				forumthread_id, 
				forumthread_title,
				forumthread_lastreply_time,
				forumthread_catid
			FROM ".pkSQLTAB_FORUM_THREAD."
			WHERE forumthread_status IN(1,2) AND
				forumthread_catid IN(0".implode(',',$FORUM->getCategories()).")
			ORDER BY forumthread_lastreply_time DESC
			LIMIT 10");
		while(list($id,$title,$time,$catid)=$SQL->fetch_row($result))
			{
			$rowclass=pkRowClass($rowclass);
			
			$title=pkEntities($title);
			$catname=$FORUM->getCategoryNameF($catid);
			$last_posttime=pkTimeFormat($time);
			$status=($FORUM->isUnreadedThread($catid,$id,$time)) ? 'new' : 'open';
			
			$link=pkHtmlLink(pkLink('forumsthread','','threadid='.$id.'&postid=new'),$title,'pkpublic','','',$title);
			$catlink=pkHtmlLink(pkLink('forumscategory','','catid='.$catid),$catname,'pkpublic','','',$catname);

			eval("\$forumsticker_bit.=\"".pkTpl("forumsticker_bit")."\";");
			}

		eval("\$site_body.=\"".pkTpl("forumsticker")."\";");	
		break;
	case 'finduser' :
		if($_GET['search_user']!='')
			{
			$usercount=0;
			$search_result='';
			$search_user=$_GET['search_user'];
			$getuserinfo=$SQL->query("SELECT 
				user_nick 
				FROM ".pkSQLTAB_USER." 
				WHERE user_nick LIKE '%".$SQL->f($search_user)."%' AND 
					user_imoption=1
					ORDER by user_nick ASC LIMIT 50");
			while($userinfo=$SQL->fetch_array($getuserinfo))
				{
				$usercount++;
				$search_result.='<option value="'.pkEntities($userinfo['user_nick']).'">'.pkEntities($userinfo['user_nick']).'</option>';
				}
			
			if($search_result!='')
				{
				eval("\$search_result=\"".pkTpl("finduser_result")."\";");
		
				if($usercount==50) 
					eval("\$search_result.=\"".pkTpl("finduser_notifcation")."\";");
				} 
			}
		
		
		$getbuddies=$SQL->query("SELECT 
			buddy_friendid 
			FROM ".pkSQLTAB_USER_FRIENDLIST." 
			WHERE buddy_userid='".intval(pkGetUservalue('id'))."'");
			
		$sqlcommand='';
		
		while($buddy=$SQL->fetch_array($getbuddies))
			{
			$buddy_chache[$buddy['buddy_friendid']]=$buddy;
			$sqlcommand.=(empty($sqlcommand) ? '' : ',').$buddy['buddy_friendid'];
			}
		
		unset($buddy_list);
		if($sqlcommand!='')
			{
			$getuserinfo=$SQL->query("SELECT 
					user_nick, 
					user_imoption
					FROM ".pkSQLTAB_USER."
					WHERE user_id IN (".$sqlcommand.")
					AND user_imoption=1");
			while($userinfo=$SQL->fetch_array($getuserinfo))
				{
				$buddy_list.='<option value="'.pkEntities($userinfo['user_nick']).'">'.pkEntities($userinfo['user_nick']).'</option>';
				}
			
			if($buddy_list!='')
				eval("\$buddy_list=\"".pkTpl("finduser_buddylist")."\";");
			}
		
		$search_user=pkEntities($search_user);
		
		eval("\$site_body.=\"".pkTpl("finduser")."\";");
		break;
		#END case finduser
	case 'help' :
		include(pkDIRPUBLIC.'help'.pkEXT);
		break;
		#END case help
	case 'smilies' :
		$smilies=new smilies();
		$format_smilies=$smilies->getSmilies('0');
		eval("\$site_body.=\"".pkTpl("smiliewindow")."\";");	
		break;
		#END case smilies
	default :
		pkEvent('page_not_found');
		break;
	}
	
$site_header_script='<script src="fx/popup.js" type="text/javascript"></script>';

if(empty($site_body_onload))
	$site_body_onload=' onLoad="pkWinOnTop(); pkWinResize(\''.$window_w_size.'\',\''.$window_h_size.'\');"';
?>