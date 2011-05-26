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


if($ACTION==$_POST['action'])
	{
	$avatar_eod = $ENV->_post_id('avatar_eod');
	$avatar_eod = $avatar_eod==1 || $avatar_eod==2 ? $avatar_eod : 0;
	
	#avatar path
	$avatar_path = $ENV->_post('avatar_path');
	
	while(mb_substr($avatar_path,-1,1,'UTF-8')=='/') #remove ending slashes
		{
		$avatar_path = mb_substr($avatar_path,0,-1,'UTF-8');
		}


	#height & width
	$avatar_height	= $ENV->_post_id('avatar_height');
	$avatar_height	= $avatar_height<1 ? 80 : $avatar_height;
	$avatar_width	= $ENV->_post_id('avatar_width');
	$avatar_width	= $avatar_width<1 ? 80 : $avatar_width;
			
	
	#set the save values
	$save_values['avatar_eod']		= $avatar_eod;
	$save_values['avatar_path']		= $avatar_path; #validated above
	$save_values['avatar_height']	= $avatar_height; #validated above
	$save_values['avatar_width']	= $avatar_width; #validated above			
	$save_values['avatar_size']		= $ENV->_post_id('avatar_size');
	
	return; #dont forget this
	}


$path_error = '';
$path = realpath(pkDIRROOT.pkGetConfig('avatar_path'));

if(!@is_writable($path) || !@is_dir($path))
	{
	$path_error = pkGetLang('config_avatar_path_error');
	}


$info_avatar_eod1 = pkGetConfig('avatar_eod')==1 ? $_selected : '';
$info_avatar_eod2 = pkGetConfig('avatar_eod')==2 ? $_selected : '';
$info_avatar_eod0 = $info_avatar_eod1 || $info_avatar_eod2 ? '' : $_selected;

$avatar_path	= pkGetConfigF('avatar_path');
$avatar_size	= pkGetConfigF('avatar_size');
$avatar_height	= pkGetConfigF('avatar_height');
$avatar_width	= pkGetConfigF('avatar_width');
?>