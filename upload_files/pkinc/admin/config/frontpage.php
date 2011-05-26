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
	$site_frontpage = '';
	$array = explode("\n",$ENV->_post('site_frontpage'));
	$array = is_array($array) ? $array : array(); #explode returns FALSE on empty strings
		
	foreach($array as $line)
		{
		$line = trim($line);
		$site_frontpage.= empty($line) ? '' : $line."\n";
		}
	

	#set the save values
	$save_values['site_frontpage'] = $site_frontpage;
	$save_values['site_frontpage_title'] = $ENV->_post('site_frontpage_title');
	$save_values['site_frontpage_link'] = $ENV->_post('site_frontpage_link');	
	$save_values['welcome_eod'] = $ENV->_post('welcome_eod');
	$save_values['welcome_title'] = $ENV->_post('welcome_title');
	$save_values['welcome_text'] = $ENV->_post('welcome_text');
	
	return; #dont forget this
	}


$welcome_title	= pkGetConfigF('welcome_title');
$welcome_text	= pkGetConfigF('welcome_text');
$site_frontpage	= pkGetConfigF('site_frontpage');
$site_frontpage_title = pkGetConfigF('site_frontpage_title');
$site_frontpage_link = pkGetConfigF('site_frontpage_link');

$welcome_eod1	= pkGetConfig('welcome_eod')==1 ? $_checked : '';
$welcome_eod0	= $welcome_eod1 ? '' : ' checked="checked"';
?>