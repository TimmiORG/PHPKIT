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



if(pkGetUservalue('id'))
	return pkEvent('already_logged_in');


$modehash=array('firstlog','lostpassword','newpassword');
$mode=(isset($_REQUEST['mode']) && in_array($_REQUEST['mode'],$modehash)) ? $_REQUEST['mode'] : NULL;


switch($mode)
	{
	case 'lostpassword' :
		$ACTION=(isset($_POST['action'])) ? $_POST['action'] : 'view';

		if($ACTION==$_POST['submit'])
			{
			if(emailcheck($_POST['lostem']))
				{
				pkLoadLang('email');				
				
				$email=$SQL->fetch_assoc($SQL->query("SELECT
						user_nick,
						user_name,
						user_email,
						user_activate,
						user_id FROM ".pkSQLTAB_USER."
					WHERE user_email='".$SQL->f($_POST['lostem'])."'
					LIMIT 1"));
				
				if($email['user_activate']==1 && !empty($email['user_email']))
					{
					$uid=pkStringRandom(16);
					
					$SQL->query("UPDATE ".pkSQLTAB_USER."
						SET uid='".$SQL->f($uid)."',
							lastlog='".pkTIME."'
						WHERE user_id='".$SQL->id($email['user_id'])."'");
					
					$link = pkLinkMail('login','newpassword','uid='.$uid);

					$mail_title	= pkGetSpecialLang('lostpassword_mail_title',
									pkGetConfig('site_name')
									);

					$mail_text	= pkGetSpecialLang('lostpassword_mail_text',
									$email['user_nick'],
									pkGetConfig('site_name'),
									$link,
									$email['user_name'],
									pkGetConfig('site_name'),
									pkGetConfig('site_url')					
									);
					
					
					if(mailsender(mailalias($email['user_email'],$email['user_nick']),$mail_title,$mail_text))
						{
						pkHeaderLocation('','','event=password_sent');
						}
					else
						{
						pkHeaderLocation('','','event=email_error');
						}
					}
				elseif($email['user_activate']!=1 && $email['user_email']!='')
					{
					pkHeaderLocation('','','event=account_inactive');
					}
				else
					{
					pkHeaderLocation('login','lostpassword','error=2');
					}
				}
			elseif(empty($_POST['lostem']))
				{
				pkHeaderLocation('login','lostpassword','error=1');
				}
			else
				{
				pkHeaderLocation('login','lostpassword','error=3');
				}
			}
		elseif($ACTION==$_POST['cancel'])
			{
			pkHeaderLocation('start');
			}
		else 
			{
			$error=(isset($_GET['error']) && intval($_GET['error'])>0 && intval($_GET['error'])<4) ? intval($_GET['error']) : '';

			eval("\$message= \"".pkTpl("subpass_message".$error)."\";");
			eval("\$site_body.= \"".pkTpl("subpass")."\";");
			}
		break;
		#END case lostpassword
	case 'newpassword' :
		$uid=isset($_REQUEST['uid']) && !empty($_REQUEST['uid']) ? $_REQUEST['uid'] : '';
		$ACTION=(isset($_POST['action']) && isset($_POST['change']) && $_POST['action']==$_POST['change']) ? 'change' : '';

		list($id,$user_name)=$SQL->fetch_row($SQL->query("SELECT user_id, user_name FROM ".pkSQLTAB_USER." WHERE uid='".$SQL->f($uid)."' LIMIT 1"));
			
		if(!$id || empty($uid))
			{
			pkEvent('access_refused');
			return;
			}
	
		if($ACTION=='change')
			{
			$newpassword=(isset($_POST['newpassword']) && !empty($_POST['newpassword'])) ? $_POST['newpassword'] : '';
			$newpassword2=(isset($_POST['newpassword2']) && !empty($_POST['newpassword2'])) ? $_POST['newpassword2'] : '';			
			
			
			if($newpassword===$newpassword2 && strlen($newpassword)>=3)
				{
				$SQL->query("UPDATE ".pkSQLTAB_USER." SET uid='', user_pw='".$SQL->f(md5($newpassword))."' WHERE user_id='".$SQL->f($id)."' AND uid='".$SQL->f($uid)."'");

				pkHeaderLocation('','','event=password_changed');
				}
			else
				{
				pkHeaderLocation('login','newpassword','uid='.$uid);
				}
			
			return;
			}

			
		pkLoadLang('profile');

		$uid=pkEntities($uid);

		$lang_change_password_for=pkGetSpecialLang('change_password_for',pkEntities($user_name));
		$lang_new_password=pkGetLang('new_password');
		$lang_new_password_repeat=pkGetLang('new_password_repeat');
		$lang_change_password=pkGetLang('change_password');
		
		eval("\$site_body.=\"".pkTpl("subpass_newpassword")."\";");
		break;
		#END case newpassword
	case 'firstlog' : 
	default :
		pkLoadLang('event');
		
		$form_action=pkLink('','',($mode=='firstlog' ? 'firstlog' : 'login').'=1');


		$login_message=$firstlogin_activation='';

		$username=($ENV->_isset_get('username')) ? pkEntities(urldecode($ENV->_get('username'))) : NULL;

		$error=$ENV->_get('error');
		$error=is_array($error) ? $error : array();
		
		if(in_array(1,$error) || in_array(2,$error) || in_array(3,$error) || in_array(4,$error))
			{
			foreach($error as $i)
				($i>=1 && $i<=4) ? $login_message.=pkGetLang('login_error'.$i) : NULL;
			}
		else
			{
			$login_message=pkGetLang('login_error0');
			}
	
		$lang_login=pkGetLang('login');
		$lang_username=pkGetLang('username');
		$lang_password=pkGetLang('password');
				
		$register_now=(pkGetConfig('user_registry')) ? pkHtmlLink(pkLink('registration'),pkGetLang('register_now')) : NULL;
		$lang_username_or_password_lost=pkGetLang('username_or_password_lost');
		$bl_login=pkGetLang('bl_login');
		$lang_save_logindata_permanently=pkGetLang('save_logindata_permanently');
		
		if($mode=='firstlog')
			{
			$lang_activationcode=pkGetLang('activationcode');
			$lang_activationcode_description=pkGetLang('activationcode_description');

			eval("\$firstlogin_activation=\"".pkTpl("login_firstlogin")."\";");
			}
		
		eval("\$site_body.=\"".pkTpl("login")."\";");
		break;
		#END default
	}
?>
