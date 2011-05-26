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
	#set the save values
	$user_registry = $ENV->_post_id('user_registry');
	$user_registry = $user_registry==1 || $user_registry==2 ? $user_registry : 0;

	$save_values['user_registry'] = $user_registry;
	$save_values['user_activate'] = $ENV->_post_ibool('user_activate');
	$save_values['user_disclaimer'] = $ENV->_post_ibool('user_disclaimer');
	
	return; #dont forget this
	}


$info_registry1 = pkGetConfig('user_registry')==1 ? $_selected : '';
$info_registry2 = pkGetConfig('user_registry')==2 ? $_selected : '';
$info_registry0 = $info_registry1 || $info_registry2 ? '' : $_selected;

$info_activate1 = pkGetConfig('user_activate')==1 ? $_selected : '';
$info_activate0 = $info_activate1 ? '' : $_selected;

$info_disclaimer1 = pkGetConfig('user_disclaimer')==1 ? $_selected : '';
$info_disclaimer0 = $info_disclaimer1 ? '' : $_selected;
?>