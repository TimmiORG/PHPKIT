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


pkLoadLang('contact');
pkLoadLang('email');
$modehash=array('suggest','user');

$mode = isset($_REQUEST['mode']) && in_array($_REQUEST['mode'],$modehash) ? $_REQUEST['mode'] : NULL;


switch($mode)
	{
	case 'user' :
		if(!pkGetUservalue('id'))
			{
			return pkEvent('access_refused');
			}

		pkLoadFunc('user');
		$user_navigation = pkUserNavigation();
	
		if(!pkGetConfig('member_mailer'))
			{
			return pkEvent('function_disabled');
			}


		$userid = isset($_REQUEST['userid']) && intval($_REQUEST['userid']) ? intval($_REQUEST['userid']) : 0;
		$ACTION = isset($_POST['action']) ? $_POST['action'] : 'view';
		
		$userinfo = $SQL->fetch_assoc($SQL->query("SELECT user_id, user_nick, user_email, user_emailshow FROM ".pkSQLTAB_USER." WHERE user_id='".$userid."' LIMIT 1"));
		
		if(!$userinfo['user_emailshow']==1)
			{
			return pkEvent('email_contact_undesired');
			}
		
		if($ACTION==$_POST['send'] && $_POST['mailer_title']!="" && $_POST['mailer_text']!="")
			{
			$senderinfo['user_id']		= pkGetUservalue('id');
			$senderinfo['user_nick']	= pkGetUservalue('nick');
			$senderinfo['user_email']	= pkGetUservalue('email');
				
			$mailer_adress	= mailalias($userinfo['user_email'],$userinfo['user_nick']);
			$mailer_header	= "From: ".mailalias($senderinfo['user_email'],$senderinfo['user_nick']);
			$mailer_title	= $_POST['mailer_title'];
			$mailer_text	= $_POST['mailer_text'];
			
			$mailer_body	= pkGetSpecialLang('user_mailer_body',$mailer_text,pkGetConfig('site_name'),pkGetConfig('site_url'));
			
			$bool	= mailsender($mailer_adress,$mailer_title,$mailer_body,$mailer_header);
			$event	= $bool ? 'email_sent' : 'email_error';
						
			pkHeaderLocation('','','event='.$event);
			}

		if($_POST['action']==$LANG['send'])
			{
			$mailer_title	= pkEntities($_POST['mailer_title']);
			$mailer_text	= pkEntities($_POST['mailer_text']);
			
			eval("\$mailer_msg= \"".pkTpl("mailer_msg")."\";");
			}
			
		$form_action = pkLink('contact','user');
		
		$userinfo['user_nick'] = pkEntities($userinfo['user_nick']);
		
			
		eval("\$site_body.= \"".pkTpl("mailer")."\";");
		break;
		#END case user
	
	case 'suggest' :
		$error=0;
		$ACTION = isset($_POST['action']) ? $_POST['action'] : 'view';
		
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
			else
				$error=1;
			}
		
		if($ACTION==$_POST['send'] && !$captcha_check)
			{
			pkEvent('securitycode_invalid');
			}
		
		if($error==1)
			{
			pkEvent('multi_emailaddresses');
			}
		
		if(isset($_REQUEST['suggest_path']))
			{
			$suggest_url = pkEntities(urldecode($_REQUEST['suggest_path']));
			}
		else
			{
			$suggest_url = pkEntities($_POST['suggest_url']);
			}
		
		if(isset($_REQUEST['suggest_email']) && pkEntities($_REQUEST['suggest_email'])!=pkGetLang('email_address'))
			{
			$suggest_email = pkEntities($_REQUEST['suggest_email']);
			}
		
		if(isset($_REQUEST['suggest_name']) && pkEntities($_REQUEST['suggest_name'])!=pkGetLang('receiver'))
			{
			$suggest_name = pkEntities($_REQUEST['suggest_name']);
			}
			
		if(isset($_POST['suggest_text']) && !empty($_POST['suggest_text']))
			{
			$suggest_text = pkEntities($_POST['suggest_text']);
			}
		else
			{
			$link = pkGetConfig('site_url').'/'.pkREQUESTEDFILE.'?'.$suggest_url;
			$suggest_text = pkGetSpecialLang('suggest_text',empty($suggest_name) ? ' ' : ' '.$suggest_name,$link);	
			}
		
		$form_action = pkLink('contact','suggest');
		
		$suggest_url = pkEntities($suggest_url);
		$captcha = pkCaptchaField();
		
		eval("\$site_body.= \"".pkTpl("suggest")."\";");
		break;
		#END case suggest
	default :
		pkLoadClass($BBCODE,'bbcode');
	
		$contact_email = '';
		$contact_name = '';
		$contact_subject = '';
		$contact_message = '';
		$copy_option = '';
		
		
		if(isset($_POST['action'])) 
			{
			$ACTION=$_POST['action'];
			
			$contact_message=$ENV->_post('contact_message');
			$contact_subject=$ENV->_post('contact_subject');
			$contact_email=$ENV->_post('contact_email');
			$contact_name=$ENV->_post('contact_name');
			}
		else
			{
			$ACTION='view';
			}		
		
		
		if($ACTION==$_POST['send'] && ($captcha=pkCaptchaCodeValid($ENV->_post(pkCAPTCHAVARNAME))) && emailcheck($contact_email) && trim($contact_name)!='' && trim($contact_subject)!='' && trim($contact_message)!='')
			{
			$contact_time = pkTimeFormat(pkTIME,'spoken');
			
			$contact_title			= pkGetSpecialLang('contact_main_subject',pkGetConfig('site_name'),$contact_subject);
			$contact_body_master	= pkGetSpecialLang('contact_main_body_master',$contact_time,$contact_name,pkGetConfig('site_name'),$contact_message);

			$header = 'From: '.mailalias($contact_email,$contact_name);
			
			if(mailsender('',$contact_title,$contact_body_master,$header)) 
				{
				if($_POST['contact_copy']==1)
					{
					$contact_body_sender = pkGetSpecialLang('contact_main_body_sender',pkGetConfig('site_name'),pkGetConfig('site_email'),$contact_message);					
					
					mailsender($contact_email,$contact_title,$contact_body_sender);
					}
				
				pkHeaderLocation('','','event=webmaster_message_sent');
				}
			
			pkHeaderLocation('','','event=email_error');		
			}
		
		
		if($ACTION!='view')
			{
			if($_POST['contact_copy']==1)
				$copy_option='checked';
		
			if(!$captcha)
				{
				eval("\$error_message=\"".pkTpl("captcha_error")."\";");
				}
			else
				{
				eval("\$error_message=\"".pkTpl("contact_".(emailcheck($contact_email) ? '' : 'mail')  ."error")."\";");
				}
			}
		else 
			{
			$contact_email=pkGetUservalue('email');
			$contact_name=pkGetUservalue('nick');
			$contact_subject=$ENV->_get('contact_subject');
			}
		
		
		$form_action = pkLink('contact');
		
		$contact_email		= pkEntities($contact_email);
		$contact_name		= pkEntities($contact_name);
		$contact_subject	= pkEntities($contact_subject);
		$contact_message	= pkEntities($contact_message);
		
		$captcha = pkCaptchaField();
		
		
		#set site title
		$page_title = pkGetConfigF('contact_page_title');
		$page_title = empty($page_title) ? pkGetLang('contact_page_title') : $page_title;
		$CMS->site_title_set($page_title,true);
		
		$page_text = pkGetConfig('contact_page_text');
		$page_text = empty($page_text) ? pkGetLang('contact_page_text') : $BBCODE->parse($page_text,1,1,1,1);;
		
		eval("\$site_body.=\"".pkTpl("contact")."\";");
		break;
		#case default
	}
?>