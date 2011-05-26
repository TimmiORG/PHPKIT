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


if($ACTION==$_POST['save'])
	{
	#TODO: vars are not right here - not consistent and different from the config keys
	$hash = array(	'notify_register_m', 'notify_comment_m', 'notify_gbook_m', 'notify_forum_m', 'notify_submit_m',
					'notify_register_i', 'notify_comment_i', 'notify_gbook_i', 'notify_forum_i', 'notify_submit_i');
	
	foreach($hash as $key)
		{
		$value = '';
		$array = explode("\n",$ENV->_post($key));
		$array = is_array($array) ? $array : array();#explode returns FALSE on empty strings

		foreach($array as $line)
			{
			$line   = trim($line);
			$value .= empty($line) ? '' : $line."\n";
			}
		
		$save_values[$key] = trim($value);
		}
	
	return; #dont forget this
	}


$hash = array('register', 'comment', 'gbook', 'forum', 'submit');
$userinfo_hash = array();

$array = explode("\n",
					pkGetConfig('notify_register_i')."\n".
					pkGetConfig('notify_comment_i')."\n".
					pkGetConfig('notify_gbook_i')."\n".
					pkGetConfig('notify_forum_i')."\n".
					pkGetConfig('notify_submit_i')
					);
$array = is_array($array) ? $array : array();
$array = array_map('intval',$array);
$array = array_unique($array);

if(!empty($array))
	{
	$query = $SQL->query("SELECT user_id,user_nick FROM ".pkSQLTAB_USER." WHERE user_id IN(0".implode(',',$array).")");
	while(list($id,$nick) = $SQL->fetch_row($query))
		{
		$userinfo_hash[$id] = $nick;
		}
	}

foreach($hash as $key) #for each option
	{
	$array = explode("\n", pkGetConfig('notify_'.$key.'_i'));
	$array = is_array($array) ? $array : array();
	
	$var	= 'notify_by_'.$key.'_info'; #_by obsolete
	$$var	= ''; #predefine 
	
	foreach($array as $id)
		{
		if(!isset($userinfo_hash[$id]))
			{
			#user id doesnt exists
			continue;
			}
		
		$$var.= empty($$var) ? '' : ',';
		$$var = pkEntities($userinfo_hash[$id]);
		}#END foreach array
	}#END foreach hash
	

$notify_register_m	= pkGetConfigF('notify_register_m');
$notify_register_i	= pkGetConfigF('notify_register_i');

$notify_comment_m	= pkGetConfigF('notify_comment_m');
$notify_comment_i	= pkGetConfigF('notify_comment_i');

$notify_gbook_m		= pkGetConfigF('notify_gbook_m');
$notify_gbook_i		= pkGetConfigF('notify_gbook_i');

$notify_forum_m		= pkGetConfigF('notify_forum_m');
$notify_forum_i		= pkGetConfigF('notify_forum_i');

$notify_submit_m	= pkGetConfigF('notify_submit_m');
$notify_submit_i	= pkGetConfigF('notify_submit_i');

$userid = pkGetUservalueF('id');
?>