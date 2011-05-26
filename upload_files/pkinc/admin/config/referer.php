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
	$save_values['referer_eod']		= $ENV->_post_ibool('referer_eod');
	$save_values['referer_filter']	= $ENV->_post('referer_filter');
	$save_values['referer_delete']	= $ENV->_post_id('referer_delete');
	
	return; #dont forget this
	}


$referer_eod1 = pkGetConfig('referer_eod')==1 ? $_selected : '';
$referer_eod0 = $referer_eod1 ? '' : $_selected;

$referer_filter = pkGetConfigF('referer_filter');
$referer_delete = pkGetConfigF('referer_delete');
?>