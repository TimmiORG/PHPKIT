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
	$comment_order = $ENV->_post('comment_order');
	$comment_order = $comment_order=='ASC' || $comment_order=='DESC' ? $comment_order : 'ASC';
	
	
	#set the save values
	$save_values['comment_order']		= $comment_order; #validated above
	$save_values['comment_bb']			= $ENV->_post_ibool('comment_bbcode');
	$save_values['comment_smilies']		= $ENV->_post_ibool('comment_smilies');
	$save_values['comment_images']		= $ENV->_post_ibool('comment_images');
	$save_values['comment_register']	= $ENV->_post_ibool('comment_register');
	
	$save_values['comment_floodctrl']	= $ENV->_post_id('comment_floodctrl');
	$save_values['comment_maxchars']	= $ENV->_post_id('comment_maxchars');
	
	return; #dont forget this
	}
	

$info_orderD = pkGetConfig('comment_order')=='DESC' ? $_selected : '';
$info_orderA = $info_orderD ? '' : $_selected;

$info_bbcode1 = pkGetConfig('comment_bb')==1 ? $_selected : '';
$info_bbcode0 = $info_bbcode1 ? '' : $_selected;

$info_smilies1 = pkGetConfig('comment_smilies')==1 ? $_selected : '';
$info_smilies0 = $info_smilies1 ? '' : $_selected;

$info_images1 = pkGetConfig('comment_images')==1 ? $_selected : '';
$info_images0 = $info_images1 ? '' : $_selected;

$info_register1 = pkGetConfig('comment_register')==1 ? $_selected : '';
$info_register0 = $info_register1 ? '' : $_selected;
		
$comment_floodctrl	= pkGetConfigF('comment_floodctrl');
$comment_maxchars	= pkGetConfigF('comment_maxchars');
?>