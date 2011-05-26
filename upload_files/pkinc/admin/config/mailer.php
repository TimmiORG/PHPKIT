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
	$save_values['contact_page_title'] = $ENV->_post('contact_page_title');
	$save_values['contact_page_text'] = $ENV->_post('contact_page_text');

	$save_values['site_mail_txt'] = $ENV->_post('site_mail_txt');
	$save_values['site_mail_htm'] = $ENV->_post('site_mail_htm');

	$save_values['smtp_server'] = $ENV->_post('smtp_server');
		
	return; #dont forget this
	}	


$contact_page_title = pkGetConfigF('contact_page_title');
$contact_page_text = pkGetConfigF('contact_page_text');
$site_mail_txt = pkGetConfigF('site_mail_txt');
$site_mail_htm = pkGetConfigF('site_mail_htm');
$smtp_server = pkGetConfigF('smtp_server');
?>