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


if(!getrights($config['member_infoshow'])=="true")
	pkHeaderLocation('','','event=access_refused');

// presets
$member_letterlinks = $searchstr = $row  = $member_overview_rows = $order1 = $order2 = $order3 = '';
$counter = array('','');
$a = array('','');
$letterhash=array($lang['all'],"A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
$orderhash=array('os','osup','signin','signinup','nickup','up');

$epp=pkGetConfig('member_epp');
$phpkit_status=phpkitstatus();
$total_user=$phpkit_status['user_counter'];


$entries=(isset($_REQUEST['entries']) && intval($_REQUEST['entries'])>0) ? intval($_REQUEST['entries']) : 0;
$letter=(isset($_REQUEST['letter']) && in_array($_REQUEST['letter'],$letterhash)) ? $_REQUEST['letter'] : '';
$search=(isset($_REQUEST['usernick'])) ? $_REQUEST['usernick'] : '';
$order=(isset($_REQUEST['order']) && in_array($_REQUEST['order'],$orderhash)) ? $_REQUEST['order'] : 'up';


foreach($letterhash as $h)
	{
	if($h==$letter || ($letter=="" && $h==$lang['all'])) 
		{
		$a[]="<b>(";
		$a[].=")</b>";
		}
	
	$link_letter=pkLink('userslist','','order='.$order.'&letter='.$h.'&entries='.$entries);
	
	eval("\$member_letterlinks.= \"".pkTpl("member_letter_link")."\";");
	$a = array('','');
	}
	

if($letter!=$lang['all'] && !empty($letter)) 
	$searchstr=" AND user_nick LIKE '".$SQL->f($letter)."%' ";
	
if($search!=$lang['search_user'])
	{
	$searchstr.=" AND user_nick LIKE '%".$SQL->f($search)."%' ";
	$counter=$SQL->fetch_array($SQL->query("SELECT COUNT(*) FROM ".pkSQLTAB_USER." WHERE user_activate='1' ".$searchstr));
	}
	
if($entries>$counter[0])
	$entries=0;
	

$total_side=sidelinkfull($counter[0], $epp, $entries, "include.php?path=userslist&order=".$order."&usernick=".pkEntities($search)."&letter=".$letter,"small");


if($order=="os")
	{
	$order2="up";
	$order="logtime DESC";
	}
elseif($order=="osup")
	{
	$order="logtime ASC";
	}
elseif($order=="signin")
	{
	$order3="up";
	$order="signin DESC";
	}
elseif($order=="signinup") 
	{
	$order="signin ASC";
	}
elseif($order=="nickup") 
	{
	$order="TRIM(user_nick) DESC";
	}
else
	{
	$order1="up";
	$order="TRIM(user_nick) ASC";
	}

$getuserinfo=$SQL->query("SELECT 
		*
	FROM ".pkSQLTAB_USER."
	WHERE user_activate=1 ".$searchstr."
	ORDER by ".$order."
	LIMIT ".$entries.", ".$epp);

$member_overview_rows = '';
while($userinfo=$SQL->fetch_array($getuserinfo))
	{
	$userinfo['user_nick']=pkEntities($userinfo['user_nick']);  
	$row=rowcolor($row);
	
	eval("\$info_nick= \"".pkTpl("member_showprofil_textlink")."\";");
	
	if($userinfo['user_sex']=="m") 
		eval("\$info_profile= \"".pkTpl("member_showprofil_iconlink_m")."\";");
	elseif($userinfo['user_sex']=="w") 
		eval("\$info_profile= \"".pkTpl("member_showprofil_iconlink_w")."\";");
	else
		eval("\$info_profile= \"".pkTpl("member_showprofil_iconlink")."\";");
	
	
	if($userinfo['user_hpage']!='') 
		{
		if(strstr(strtolower($userinfo['user_hpage']),'http://'))
			{
			$info_link=pkEntities($userinfo['user_hpage']);
			}
		else
			{
			$info_link='http://'.pkEntities($userinfo['user_hpage']);
			}
		
		eval("\$info_hpage= \"".pkTpl("member_hpage_iconlink")."\";");
		}
	else
		{
		$info_hpage='&nbsp;';
		}

	
	if($userinfo['user_emailshow']==1)
		{
		if($config['member_mailer']==1)
			{
			eval("\$info_email=\"".pkTpl("member_email_iconlink2")."\";");
			}
		else
			{
			eval("\$info_email=\"".pkTpl("member_email_iconlink")."\";");
			}
		}
	else
		{
		$info_email='&nbsp;';
		}

	if($userinfo['user_imoption']==1)
		{
		eval("\$info_im= \"".pkTpl("member_sendim_iconlink")."\";");
		}
	else
		{
		eval("\$info_im= \"".pkTpl("member_sendim_nolink")."\";");
		}
	
	eval("\$info_buddie= \"".pkTpl("member_buddie_iconlink")."\";");
	
	if(isonline($userinfo['user_id']))
		eval("\$info_os= \"".pkTpl("member_os_online")."\";");
	else
		eval("\$info_os= \"".pkTpl("member_os_offline")."\";");
		
	
	$info_signin=formattime($userinfo['signin'],'','date');
	eval("\$member_overview_rows.= \"".pkTpl("member_row")."\";");
	}

pkLoadFunc('user');
$user_navigation=pkUserNavigation();

$usernick=pkEntities($search);

$lang_users=pkGetLang('users');
$lang_search_user=pkGetLang('search_user');

eval("\$site_body.= \"".pkTpl("member")."\";");
?>