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
# licence   : http://www.phpkit.com/licence/phpkit
# copyright : Copyright (c) 2002-2009 mxbyte gbr | http://www.mxbyte.com


if(!defined('pkFRONTEND') || pkFRONTEND!='public')
	die('Direct access to this location is not permitted.');
	

$error=0;
$ACTION=(isset($_POST['action'])) ? $_POST['action'] : 'view';

if(pkGetUserValue('id'))
	{
	pkHeaderLocation('','','event=registration_denied');
	}

if($config['user_registry']!=1 && $config['user_registry']!=2)
	pkHeaderLocation('','','event=registration_disabled');


if($ACTION==$_POST['refuse']) 
	pkHeaderLocation();


if(($ACTION==$_POST['accept'] && $config['user_disclaimer']==1) || $config['user_disclaimer']==0 || $ACTION==$_POST['send'] || $_REQUEST['disclaimer']==1) 
	{
	$disclaimer=1;
	
	if($ACTION==$_POST['send'])
		{
		if(isset($_POST['newuser'])) 
			$newuser=$_POST['newuser'];
		
		if(isset($_POST['newemail']))
			$newemail=$_POST['newemail'];
		
		if(isset($_POST['newemail2']))
			$newemail2=$_POST['newemail2'];
		
		if(isset($_POST['newpass']))
			$newpass=$_POST['newpass'];
		
		if(isset($_POST['newpass2']))
			$newpass2=$_POST['newpass2'];
		
		
		if(!pkCaptchaCodeValid($ENV->_post(pkCAPTCHAVARNAME)))
			$error=9;
		elseif(!checkusername($newuser)) 
			$error=3;
		elseif(!emailcheck($newemail,1))
			$error=5;
		elseif($config['user_registry']==2 && $newemail!=$newemail2) 
			$error=6;
		elseif($config['user_registry']==2 && ($newpass=="" or $newpass2==""))
			$error=7;
		elseif($config['user_registry']==2 && ($newpass!=$newpass2)) 
			$error=8;
		else
			{
			$user=$SQL->fetch_array($SQL->query("SELECT 
				COUNT(*) 
				FROM ".pkSQLTAB_USER." 
				WHERE user_name='".$SQL->f($newuser)."' OR 
					user_nick='".$SQL->f($newuser)."'
				LIMIT 1"));
			
			if($user[0]>0)
				$error=3;
			else
				{
				$email=$SQL->fetch_array($SQL->query("SELECT 
					COUNT(*) 
					FROM ".pkSQLTAB_USER."
					WHERE user_email='".$SQL->f($newemail)."' LIMIT 1"));
				
				if($email[0]>0)
					$error=4;
				else
					{
					pkLoadLang('email');

					pkMtSrand();
					$uid = md5(uniqid(mt_rand()));
					
					$password	 = pkGetConfig('user_registry')==2 ? $newpass : pkStringRandom(9);
					$sqlpassword = md5($password);					
					
					$SQL->query("INSERT INTO ".pkSQLTAB_USER." 
						(uid, user_name, user_nick, user_pw, user_email, user_status, signin, lastlog, user_activate)
						VALUES
						('".$SQL->f($uid)."','".$SQL->f($newuser)."','".$SQL->f($newuser)."',
						 '".$SQL->f($sqlpassword)."','".$SQL->f($newemail)."','user',
						 '".pkTIME."','".pkTIME."','".intval($config['user_activate'])."')");
					$info = $SQL->insert_id();
					
					
					usercount();
					newestuser();					

					$mail_link = pkLinkMail('','','user='.urlencode($newuser).'&userpw='.$password.'&firstlog=1&uid='.$uid);
					$link_login = pkLinkMail('login','firstlog');
					

					$mail_title = pkGetSpecialLang('registration_mail_title',pkGetConfig('site_name'));
					
					if(pkGetConfig('user_activate'))
						{
						$mail_addtext = pkGetSpecialLang('registration_mail_body_activate_true',$mail_link,$link_login);
						}
					else
						{
						$mail_addtext = pkGetSpecialLang('registration_mail_body_activate_false',pkGetConfig('site_name'),$info);#info = userid
						}
					
					if(pkGetConfig('user_registry')==2)
						{					
						$mail_text = pkGetSpecialLang('registration_mail_body_novalidation',
										$newuser,
										pkGetConfig('site_name'),
										$mail_addtext,
										$newuser,
										$password,
										pkGetConfig('site_name'),
										pkGetConfig('site_url')
										);
						}
					else
						{
						$mail_text = pkGetSpecialLang('registration_mail_body',
										$newuser,
										pkGetConfig('site_name'),
										$mail_addtext,
										$newuser,
										$password,
										$uid,
										pkGetConfig('site_name'),
										pkGetConfig('site_url')
										);
						}
					
					$receiver_alias = mailalias($newemail,$newuser);
					
					if(mailsender($receiver_alias,$mail_title,$mail_text))
						{
						$event	= pkGetConfig('user_activate')==1 ? 'registration_successful' : 'account_created';
						$link	= pkLinkMail('userinfo','','id='.$info);
					
						$mail_title	= pkGetSpecialLang('registration_mail_notify_title',
										pkGetConfig('site_name'),
										$newuser
										);
						
						$mail_text	= pkGetSpecialLang('registration_mail_notify_body',
										pkGetConfig('site_name'),
										$newuser,
										$newemail,
										$info,
										$link				
										); 


						notifymail('register',$mail_title,$mail_text);
						
						$pn_title = pkGetLang('new_user').': '.$newuser;
						$pn_text = pkGetSpecialLang('new_user_pn_text',$newuser,$info,$newuser);

						notifyim('register',$pn_title,$pn_text);
						}
					else
						{
						$event = 'email_error';
						}
					}
				}
			}
		
		if($error=='' && $config['user_registry']==1)
			pkHeaderLocation('','','event='.$event);
		
		if($error=='' && $config['user_registry']==2)
			pkHeaderlocation('login','','remove_path='.urlencode('path=userprofile&mode=edit').'&user='.urlencode($newuser).'&userpw='.urlencode($newpass).'&login=1');
		}

	$error_massage='';

	pkLoadLang('registration');

	$type=(pkGetConfig('user_registry')==2 ? 2 : '');
		
	$newuser=pkEntities($newuser);
	$newemail=pkEntities($newemail);
	$newemail2=pkEntities($newemail2);
	
	
	if(isset($_REQUEST['error']) && intval($_REQUEST['error'])>0) 
		$error=intval($_REQUEST['error']);
	
	if(isset($error) && $error>=1 && $error<=9)
		{
		$error_message = pkGetLang('registration_error_'.$error);
		}
	else
		{
		$error_message = pkGetLang('registration_message'.$type);
		}
		
	eval("\$error_message= \"".pkTpl("register_error")."\";");


	$lang_registration=pkGetLang('registration');

	$lang_username=pkGetLang('username');
	$lang_registration_username_hlp=pkGetSpecialLang('registration_username_hlp',pkGetConfigF('user_namemin'),pkGetConfigF('user_namemax'));
	$lang_password=pkGetLang('password');
	$lang_registration_password_hlp=pkGetLang('registration_password_hlp'.$type);
	$lang_email_address=pkGetLang('email_address');
	$lang_registration_emailaddress_hlp=pkGetLang('registration_emailaddress_hlp'.$type);

	$captcha=pkCaptchaField(NULL,2);	

	eval("\$site_body.= \"".pkTpl("register".$type)."\";");
	}
else
	{
	pkLoadLang('termsofuse');
	
	$lang_registration_termsofuse_hl=pkGetLang('registration_termsofuse_hl');
	$lang_registration_termsofuse=pkGetSpecialLang('registration_termsofuse',pkGetConfigF('site_name').' ('.pkGetConfig('site_urls').')');
	$lang_accept=pkGetLang('accept');
	$lang_refuse=pkGetLang('refuse');

	eval("\$site_body.= \"".pkTpl("register_termsofuse")."\";");
	}
?>
