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


if(!defined('pkFRONTEND') || pkFRONTEND!='public')
	die('Direct access to this location is not permitted.');


$ACTION=(isset($_POST['action'])) ? $_POST['action'] : 'view';

if($ACTION==$_POST['cancel'])
	{
	if(isset($_POST['suggest_url']))
		{
		pkHeaderLocation('','',$_POST['suggest_url']);
		}

	pkHeaderLocation('start');
	}


if($ACTION==$_POST['send'] && ($captcha_check=pkCaptchaCodeValid($ENV->_post(pkCAPTCHAVARNAME))))
	{
	if(emailcheck($_POST['suggest_email']))
		{
		$suggest_title = pkGetSpecialLang('suggest_title_plain',pkGetConfig('site_name'));
		$mailto = mailalias($_POST['suggest_email'],$_POST['suggest_name']);
		
		if(mailsender($mailto,$suggest_title,$_POST['suggest_text']))
			{
			pkHeaderLocation('','','event=suggestion_sent'.(isset($_POST['suggest_url']) ? '&moveto='.urlencode($_POST['suggest_url']) : ''));
			}

		pkHeaderLocation('','','event=email_error');
		}
	
	pkHeaderLocation('','','event=multi_emailaddresses');
	}

if($ACTION==$_POST['send'] && !$captcha_check)
	{
	pkEvent('securitycode_invalid');
	}

if(isset($_REQUEST['suggest_path']))
	$suggest_url=pkEntities($_REQUEST['suggest_path']);
else
	$suggest_url=pkEntities($_POST['suggest_url']);

if(isset($_POST['suggest_email']))
	$suggest_email=pkEntities($_POST['suggest_email']);

if(isset($_POST['suggest_name']))
	$suggest_name=pkEntities($_POST['suggest_name']);
	
if(isset($_POST['suggest_text']))
	$suggest_text=pkEntities($_POST['suggest_text']);
else
	{
	pkLoadLang('email');
	
	$link=pkGetConfig('site_url').'/'.pkREQUESTEDFILE.'?'.$suggest_url;
	$suggest_text = pkGetSpecialLang('suggest_text',$suggest_name,$link);	
	}

$suggest_url = pkEntities($suggest_url);
$captcha = pkCaptchaField();

eval("\$site_body.= \"".pkTpl("suggest")."\";");
?>