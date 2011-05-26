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


if($dir!="forum/")
	{
	pkLoadFunc('user');
	$user_navigation=pkUserNavigation();
	
	$dir='';
	}
else
	{
	pkLoadClass($FORUM,'forum');	
	$forumcat_cache=$FORUM->getTree();
	}

unset($mods);
unset($m); 

if(is_array($forumcat_cache))
	{
	foreach($forumcat_cache as $forumcat)
		{
		if(userrights($forumcat['forumcat_mods'],$forumcat['forumcat_rrights'])=="true" or userrights($forumcat['forumcat_user'],$forumcat[forumcat_rrights])=="true" or getrights($forumcat['forumcat_rrights'])=="true") 
			{
			$m.=$forumcat['forumcat_mods'];
			}
		}
	}

$mods=explode("-",$m);
$sqlcommand="SELECT * FROM ".pkSQLTAB_USER." WHERE user_status='admin' OR user_status='mod'";

if($mods!="")
	{
	foreach($mods as $m)
		{
		if($m!="")
			{
			$sqlcommand.=" OR user_id='".intval($m)."'";
			}
		}
	}
	

$getmods=$SQL->query($sqlcommand." ORDER by user_nick ASC");
while($userinfo=$SQL->fetch_array($getmods))
	{
	$userinfo['user_nick']=pkEntities($userinfo['user_nick']);
	
	if(isonline($userinfo['user_id']))
		{
		eval("\$online_status= \"".pkTpl("member_os_online")."\";");
		}
	else
		{
		eval("\$online_status= \"".pkTpl("member_os_offline")."\";");
		}

	if($userinfo['user_sex']=='m')
		{
		eval("\$info_profile= \"".pkTpl("member_showprofil_iconlink_m")."\";");
		}
	elseif($userinfo['user_sex']=='w')
		{
		eval("\$info_profile= \"".pkTpl("member_showprofil_iconlink_w")."\";");
		}
	else
		{
		eval("\$info_profile= \"".pkTpl("member_showprofil_iconlink")."\";");
		}
	
	if($userinfo['user_status']=="admin")
		{
		eval("\$admin_row.= \"".pkTpl($dir."team_row")."\";");
		}
	elseif($userinfo[user_status]=="mod")
		{
		eval("\$mod_row.= \"".pkTpl($dir."team_row")."\";");
		}
	else
		{
		unset($user_row_foren);
		
		foreach($forumcat_cache as $forumcat)
			{
			if((userrights($forumcat['forumcat_mods'],$forumcat['forumcat_rrights'])=="true" or userrights($forumcat['forumcat_user'],$forumcat['forumcat_rrights'])=="true" or getrights($forumcat['forumcat_rrights'])=="true") && ereg("-".$userinfo['user_id']."-",$forumcat['forumcat_mods']))
				{
				eval("\$user_row_foren.= \"".pkTpl($dir."team_user_row_foren")."\";");
				}
			
			eval("\$user_row.= \"".pkTpl($dir."team_user_row")."\";");
			}
		}
	}

if($mod_row!='')
	{
	eval("\$mod_block=\"".pkTpl($dir."team_modblock")."\";");
	}

if($user_row!='')
	{
	eval("\$user_block=\"".pkTpl($dir."team_userblock")."\";");
	}

eval("\$site_body.=\"".pkTpl($dir."team")."\";");
?>