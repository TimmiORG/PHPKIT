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


$boxlinks = array();
pkLoadFunc('user');
$phpkit_status = phpkitstatus();

#userinfo
if(pkGetUservalue('id'))
	{
	$since = number_format(((pkTIME-pkGetUservalue('logtime'))/60),0,",",".");
	$time = formattime(pkGetUservalue('logtime'),'','time');
	$usernick = pkEntities(pkStringCut(pkGetUservalue('nick')));
	
	$lang_online_since = pkGetSpecialLang('online_since',$since,$time);
	
	eval("\$boxlinks[0]=\"".pkTpl("navigation/status_userinfo")."\";");
	}


#visitors
$ctoday		= pkNumberFormat($phpkit_status['counter_today']);
$cyesterday	= pkNumberFormat($phpkit_status['counter_yesterday']);
$ctotal		= pkNumberFormat($phpkit_status['counter_total']);

eval("\$boxlinks[1]=\"".pkTpl("navigation/status_visitors")."\";");


#online users
$online_list='';

if(is_array($phpkit_status['online_user']))
	{
	foreach($phpkit_status['online_user'] as $userinfo)
		{
		if(pkUserOnline($userinfo['user_id']))
			{
			$online_list.= (empty($online_list) ? '' : ', ').pkUserProfilelink($userinfo['user_id'],$userinfo['user_nick'],NULL,true,'small');
			}
		} 

	if(!empty($online_list))
		{
		$online_list.=' '.pkGetLang('and').' ';
		}
	}

$online_list.= pkGetSpecialLang('guests',$phpkit_status['online_guests']);

$lang_users_and_guests = pkGetLang('users_and_guests');
$lang_registered_users_online = pkGetSpecialLang('registered_users_online',intval($phpkit_status['user_counter']),$online_list); 

eval("\$boxlinks[2]=\"".pkTpl("navigation/status_reguser")."\";");

return $boxlinks;
?>