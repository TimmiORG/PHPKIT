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
	$save_values['censor_username']	= $ENV->_post('censor_username');
	$save_values['censor_email']	= $ENV->_post('censor_email');
	$save_values['censor_ip']		= $ENV->_post('censor_ip');
	$save_values['censor_badword']	= $ENV->_post('censor_badword');
	
	return; #dont forget this
	}

$censor_username	= pkGetConfigF('censor_username');
$censor_email		= pkGetConfigF('censor_email');
$censor_ip			= pkGetConfigF('censor_ip');
$censor_badword		= pkGetConfigF('censor_badword');
?>