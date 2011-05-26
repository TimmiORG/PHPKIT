<?php
# PHPKIT WCMS | Web Content Management System
#
#
# YOU ARE NOT AUTHORISED TO CREATE ILLEGAL COPIES OF THIS
# FILE AND/OR TO REMOVE THIS INFORMATION
#
# SIE SIND NICHT BERECHTIGT, UNRECHTM�SSIGE KOPIEN DIESER
# DATEI ZU ERSTELLEN UND/ODER DIESE INFORMATIONEN ZU ENTFERNEN
#
# This file / the PHPKIT software is no freeware! For further 
# information please visit our website or contact us via email:
#
# Diese Datei / die PHPKIT Software ist keine Freeware! F�r weitere
# Informationen besuchen Sie bitte unsere Website oder kontaktieren uns per E-Mail:
#
# email     : info@phpkit.com
# website   : http://www.phpkit.com
# licence   : http://www.phpkit.com/licence
# copyright : Copyright (c) 2002-2009 mxbyte gbr | http://www.mxbyte.com


if(!defined('pkFRONTEND') || pkFRONTEND!='public')
	die('Direct access to this location is not permitted.');


if(!isset($_GET['type']) || (intval($_GET['type']) < 1 || intval($_GET['type']) > 4))
	{
	eval("\$site_body.= \"".pkTpl("content/submit_select")."\";");
	return;
	}


$submit_message='';
$error=0;


$type=intval($_REQUEST['type']);	
$ACTION=(isset($_REQUEST['action'])) ? $_REQUEST['action'] : 'view';
$error=(isset($_REQUEST['error']) && intval($_REQUEST['error'])>0) ? intval($_REQUEST['error']) : 0;

if($type==1)
	{
	$typename=$lang['article']; 

	if(!getrights($config['content_submit1']))
		$error=1;
	}
elseif($type==2)
	{
	$typename=$lang['news'];

	if(!getrights($config['content_submit2']))
		$error=1;
	}
elseif($type==3)
	{
	$typename=$lang['link'];

	if(!getrights($config['content_submit3']))
		$error=1;
	}
elseif($type==4)
	{
	$typename=$lang['downloads'];
	
	if(!getrights($config['content_submit4']))
		$error=1;
	}

if($error)
	{
	pkEvent('access_refused');	
	return;
	}


if(isset($_POST['submit']) && $ACTION==$_POST['submit'] && !$error)
	{
	if(!pkCaptchaCodeValid($ENV->_post(pkCAPTCHAVARNAME)))
		$error=1;
	elseif(!checkusername($_POST['content_autor'],1))
		$error=4;
	elseif(!emailcheck($_POST['content_email']))
		$error=3;
	elseif(trim($_POST['content_autor'])!='' && trim($_POST['content_email'])!='' && trim($_POST['content_title'])!='' && trim($_POST['content'])!='' && (($type==3 || $type==4) &&  trim($_POST['content_altdat'])  || $type==1 || $type==2)) 
		{
		$SQL->query("INSERT INTO ".pkSQLTAB_CONTENT_SUBMIT." 
			(content_submited_autor,content_submited_autorid,content_submited_email,content_submited_title,content_submited_text,content_submited_altdat,content_submited_type,content_submited_time)
			VALUES 
			('".$SQL->f($_POST['content_autor'])."',
			 '".intval($_POST['content_autorid'])."',
			 '".$SQL->f($_POST['content_email'])."',
			 '".$SQL->f($_POST['content_title'])."',
			 '".$SQL->f($_POST['content'])."',
			 '".$SQL->f($_POST['content_altdat'])."',
			 '".$type."',
			 '".pkTIME."')");
		
		$submitid = $SQL->insert_id();
		
		pkLoadLang('email');
		pkLoadLang('content');
		

		$link = pkLinkFull('','','goto='.urlencode('?path=contentsubmited&contentid='.$submitid),'','',true);	
		
		$mail_title = pkGetSpecialLang('content_submit_notify_mailtitle',pkGetConfig('site_name'),$_POST['content_title']);
		$mail_text = pkGetSpecialLang('content_submit_notify_mail',
			pkGetConfig('site_name'),
			$typename,
			$_POST['content_autor'],
			$_POST['content_email'],
			$_POST['content_title'],
			$link);	

		notifymail('submit',$mail_title,$mail_text);


		$link = pkLinkAdmin('','','goto='.urlencode('?path=contentsubmited&contentid='.$submitid));

		$pm_title = pkGetSpecialLang('content_submit_notify_pmtitle',$_POST['content_title']);
		$pm_text = pkGetSpecialLang('content_submit_notify_pm',
			$typename,
			$_POST['content_autor'],
			$_POST['content_email'],
			$_POST['content_title'],
			$link);	
		
		notifyim('submit',$pm_title,$pm_text);
		
		pkHeaderLocation('','','event=submit_info');
		}
	else
		$error=2; 
	}


if($error)
	{
	$errors=array(
		1=>'securitycode_invalid',
		2=>'',
		3=>'mailaddress_invalid',
		4=>'name_in_use',
		);

	pkEvent($errors[$error],false);

	$content = pkEntities($_POST['content']);		
	$content_autor = pkEntities($_POST['content_autor']);
	$content_email = pkEntities($_POST['content_email']);  
	$content_title = pkEntities($_POST['content_title']);
	$content_altdat = pkEntities($_POST['content_altdat']);
	}
else 
	{
	$content_autor = pkGetUservalueF('nick');
	$content_email = pkGetUservaluef('email');
	
	pkLoadLang('content');
	eval("\$submit_message= \"".pkTpl("content/submit_message")."\";");
	}

$content_autorid=pkGetUservalueF('id');
 
$sign_format='';
if($config['text_ubb']==1)
	eval("\$sign_format.= \"".pkTpl("format_text")."\";");

if($config['text_smilies']==1)
	{
	$smilies=new smilies();
	$sign_format.=$smilies->getSmilies(1);
	}

if($sign_format!='')
	eval("\$sign_format= \"".pkTpl("format_table")."\";");

$captcha=pkCaptchaField();
 
eval("\$submit_body= \"".pkTpl("content/submit_type".$type."")."\";");
eval("\$site_body.= \"".pkTpl("content/submit")."\";");
?>