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

pkLoadFunc('user');
$phpkit_status=phpkitstatus();
$usercounter[0]=$phpkit_status['user_counter'];

if(is_array($phpkit_status['online_user']))
	{
	unset($online_user);
	
	$online_usercount=count($phpkit_status['online_user']);

	foreach($phpkit_status['online_user'] as $userinfo)
		{
		if(pkUserOnline($userinfo['user_id'])) 
			{
			$userinfo['user_nick']=pkEntities($userinfo['user_nick']);		

			if($online_user)
				$online_user.=', ';
			else
			eval("\$online_user = \"".pkTpl("forum/fuss_onlineuser")."\";");
		
			eval("\$online_user.= \"".pkTpl("member_showprofil_textlink")."\";");
			}
		}
	}
else
	$online_usercount=$lang['no'];


if($phpkit_status['online_guests']>0)
	{
	if($phpkit_status['online_guests']==1)
		{
		$online_guest='1 '.$lang['guest'];
		}
	else
		{
		$online_guest=$phpkit_status['online_guests'].' '.$lang['guests'];
		}
	}
else
	{
	$online_guest=$lang['no'].' '.$lang['guests'];
	}


if(pkGetConfig('forum_showbd') && is_array($phpkit_status['bd_user']) && !empty($phpkit_status['bd_user']))
	{
	unset($bd_user);
	
	foreach($phpkit_status['bd_user'] as $status)
		{
		unset($age);
		$age=getAge($status['user_bd_day'],$status['user_bd_month'],$status['user_bd_year']);
#		$status['user_nick']=pkEntities($status['user_nick']);
		
		if($bd_user)
			$bd_user.=', ';
		
		eval("\$bd_user.= \"".pkTpl("forum/fuss_bduser")."\";");
		}
	
	eval("\$fuss_adds_inner= \"".pkTpl("forum/fuss_adds_bduser")."\";");
	}

$mv_time=formattime($config['site_mv_time']); 
$mv_count=$config['site_mv_count'];
$userinfo=$phpkit_status['newest_user'];
$userinfo['user_nick']=pkEntities($userinfo['user_nick']);

eval("\$new_user= \"".pkTpl("member_showprofil_textlink")."\";");

if($path=="forumsearch")
	$jump_search='selected';
elseif($path=="forumsdisplay")
	$jump_main='selected'; 
unset($forum_jump);

if(is_array($cat_hash=$FORUM->gettree()))
	{
	foreach($cat_hash as $catinfo)
		{
		$forum_jump.= '<option value="'.$catinfo['forumcat_id'].'"';
		$forum_jump.= $catid==$catinfo['forumcat_id'] ? ' selected="selected"' : '';
		$forum_jump.= '>'.str_repeat('-',$catinfo['level']).' '.pkEntities($catinfo['forumcat_name']).'</option>';
		}
	}

if($path=="forumsdisplay")
	eval("\$fuss_adds_outer=\"".pkTpl("forum/fuss_adds_main")."\";");

if($path=="forumscategory")
	{
	if($showcat_threads!="")
		eval("\$fuss_adds_outer=\"".pkTpl("forum/fuss_adds_subcat")."\";");
	elseif($showcat_subcat!='' && $showcat_threads=='')
		eval("\$fuss_adds_outer=\"".pkTpl("forum/fuss_adds_main")."\";");
	}

if($path=="forumsearch" && $mode="result" && $rshow==1)
	eval("\$fuss_adds_outer=\"".pkTpl("forum/fuss_adds_subcat")."\";");


$current_path=pkEntities($ENV->getvar('QUERY_STRING'));

eval("\$site_body.=\"".pkTpl("forum/fuss")."\";");
?>