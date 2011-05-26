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
	$save_values['meta_author']			= $ENV->_post('meta_author');
	$save_values['meta_author_email']	= $ENV->_post('meta_author_email');
	$save_values['meta_publisher']		= $ENV->_post('meta_publisher');
	$save_values['meta_copyright']		= $ENV->_post('meta_copyright');
	$save_values['meta_keywords']		= $ENV->_post('meta_keywords');
	$save_values['meta_description']	= $ENV->_post('meta_description');
	$save_values['meta_favicon']		= $ENV->_post('meta_favicon');
	$save_values['meta_robots']			= $ENV->_post('meta_robots');
	$save_values['meta_robots_revisit']	= $ENV->_post('meta_robots_revisit');
	$save_values['meta_custom']			= $ENV->_post('meta_custom');
	
	return; #dont forget this
	}


$meta_author		= pkGetConfigF('meta_author');
$meta_author_email	= pkGetConfigF('meta_author_email');
$meta_publisher		= pkGetConfigF('meta_publisher');
$meta_copyright		= pkGetConfigF('meta_copyright');
$meta_keywords		= pkGetConfigF('meta_keywords');
$meta_description	= pkGetConfigF('meta_description');
$meta_favicon		= pkGetConfigF('meta_favicon');
$meta_robots		= pkGetConfigF('meta_robots');
$meta_robots_revisit = pkGetConfigF('meta_robots_revisit');
$meta_custom		= pkGetConfigF('meta_custom');
?>