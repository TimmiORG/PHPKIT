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
	$gbook_epp = $ENV->_post_id('gbook_epp');
	$gbook_epp = $gbook_epp ? $gbook_epp : 10;
	
	#set the save values
	$save_values['gbook_epp'] = $gbook_epp;

	$save_values['gbook_title']			= $ENV->_post('gbook_title');
	$save_values['gbook_welcome']		= sanitize($ENV->_post('gbook_welcome'));
	$save_values['gbook_eod']			= $ENV->_post_ibool('gbook_eod');
	$save_values['gbook_commenteod']	= $ENV->_post_ibool('gbook_commenteod');
	$save_values['gbook_smilies']		= $ENV->_post_ibool('gbook_smilies');
	$save_values['gbook_images']		= $ENV->_post_ibool('gbook_images');
	$save_values['gbook_ubb'] 			= $ENV->_post_ibool('gbook_ubb');

	$save_values['gbook_floodctrl']		= $ENV->_post_id('gbook_floodctrl');
	$save_values['gbook_maxchars']		= $ENV->_post_id('gbook_maxchars');
	
	return; #dont forget this
	}


$gb_eod1 = pkGetConfig('gbook_eod')==1 ? $_checked : '';
$gb_eod0 = $gb_eod1 ? '' : $_checked;

$gb_ceod1 = pkGetConfig('gbook_commenteod')==1 ? $_checked : '';
$gb_ceod0 = $gb_ceod1 ? '' : $_checked;

$gb_ubb1 = pkGetConfig('gbook_ubb')==1 ? $_checked : '';
$gb_ubb0 = $gb_ubb1 ? '' : $_checked;

$gb_smilies1 = pkGetConfig('gbook_smilies')==1 ? $_checked : '';
$gb_smilies0 = $gb_smilies1 ? '' : $_checked;

$gb_images1 = pkGetConfig('gbook_images') ? $_checked : '';
$gb_images0 = $gb_images1 ? '' : $_checked;

$gbook_title		= pkGetConfigF('gbook_title');
$gbook_welcome		= pkGetConfigF('gbook_welcome');
$gbook_floodctrl	= pkGetConfigF('gbook_floodctrl');
$gbook_maxchars		= pkGetConfigF('gbook_maxchars');
$gbook_epp			= pkGetConfigF('gbook_epp');
?>