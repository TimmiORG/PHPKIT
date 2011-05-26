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
	$save_values['site_eod'] = $ENV->_post_ibool('site_eod') ? 0 : 1;#also twisted - see below
	$save_values['site_message'] = $ENV->_post('site_message');

	return; #dont forget this
	} 

#@TODO: Change this in the future. Cause its twisted
#site_eod = site enabled or disabled - so 1 is online, 0 is offline/maintainence
$info_eod0 = pkGetConfig('site_eod')==1 ? $_checked : '';
$info_eod1 = $info_eod0 ? '' : $_checked;

$site_message = pkGetConfigF('site_message');
?>