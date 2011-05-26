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


if(!defined('pkFRONTEND') || (pkFRONTEND!='admin' && pkFRONTEND!='setup'))
	die('Direct access to this location is not permitted.');


#function pkAdminLogin( void )
#return void
function pkAdminLogin()
	{
	global $ENV,$LANG;
	
	pkLoadLang('adminevent');
	
	if($ENV->_get('event')=='login' || $ENV->_get('event')=='logout')
		$ENV->debugcookie();
	
	$error = $ENV->_get('error');
	$error = is_array($error) ? $error : array();

	$remove_path = $ENV->getvar('QUERY_STRING');
	$remove_path = preg_match("/moveto=/i",$remove_path) ? '' : pkEntities($remove_path);
	$username = $ENV->_isset_get('username') ? pkEntities(urldecode($ENV->_get('username'))) : pkGetUservalueF('name');
	
	$event_title = pkGetConfigF('site_name').' | '.pkGetLang('PHPKIT_administration');
	$event_type = 'login';

	$login_message='';

	if(in_array(1,$error) || in_array(2,$error) || in_array(3,$error) || in_array(4,$error))
		{
		foreach($error as $i)
			{
			($i>=1 && $i<=4) ? $login_message.=pkGetLang('login_error'.$i) : NULL;
			}
		}	
		
	$lang_username=pkGetLang('username');
	$lang_password=pkGetLang('password');
	$lang_save_logindata_permanently=pkGetLang('save_logindata_permanently');
	$lang_copyright=pkGetLang('PHPKIT_copyright');
	$lang_website=pkGetLang('website');
	$bl_login=pkGetLang('bl_login');

	$form_action=pkLink();
	$link_back=pkLinkAdmin();
	$link_public=pkLinkPublic();

	$site_meta=pkAdminSiteMeta();
	$site_refresh='';
	$site_header_script='<script src="fx/event.js" type="text/javascript"></script>';
	$site_body_onload='onLoad="pkFreeFrames();"';
	
	$log_setcookie=$ENV->_cookie('user_id') ? ' checked="checked"' : '';
	$error_name=(in_array(1,$error) || in_array(2,$error) || in_array(4,$error)) ? ' class="error"' : '';
	$error_pass=(in_array(1,$error) || in_array(3,$error) || in_array(4,$error)) ? ' class="error"' : '';
	
	eval("\$event_subject=\"".pkTpl("login_small")."\";");
	eval("\$event=\"".pkTpl("event")."\";");
	exit($event);
	}
#END function pkAdminLogin	


#function pkAdminSiteMeta( void )
#return void
function pkAdminSiteMeta()
	{
	$lang_PHPKIT_meta_author		= pkGetLang('PHPKIT_meta_author');
	$lang_PHPKIT_meta_author_link	= pkGetLang('PHPKIT_meta_author_link');
	$lang_PHPKIT_meta_description	= pkGetLang('PHPKIT_meta_description');
	$lang_PHPKIT_meta_generator		= pkGetLang('PHPKIT_meta_generator');
	$lang_PHPKIT_meta_copyright		= pkGetLang('PHPKIT_meta_copyright');
		
	$meta_date	= pkTimeFormat(pkTIME,'RFC822');
	$site_title	= pkGetConfigF('site_name').' | '.pkGetLang('administration');
	
	$str = '<title>'.$site_title.'</title>
<meta name="author" content="'.$lang_PHPKIT_meta_author.'" />
<meta name="author link" content="'.$lang_PHPKIT_meta_author_link.'" />
<meta name="description" content="'.$lang_PHPKIT_meta_description.'" />
<meta name="generator" content="'.$lang_PHPKIT_meta_generator.'" />
<meta name="copyright" content="'.$lang_PHPKIT_meta_copyright.'" />
<meta name="date" content="'.$meta_date.'" />';

	return $str;
	}
#END function pkAdminSiteMeta	


#function pkEvent( string eventkey [, string type ] )
#return void
function pkEvent($eventkey,$type='')
	{
	global $site_body,$site_header_script;

	pkLoadLang('adminevent');	

	$event_title=pkGetLang('event_'.$eventkey);
	$event_subject=pkGetLang('eventmessage_'.$eventkey);

	$type=($type=='warning') ? 'warning' : 'default';	
	$site_header_script.='<link rel="stylesheet" href="fx/default/css/event.css" type="text/css">';	
	
	eval("\$site_body.=\"".pkTpl("eventmessage")."\";");
	}
#END function pkEvent


#function pkTpl ( string tplname [, string tplextension ] )
#return string
function pkTpl($tpl,$ext='')
	{
	global $pkTPLHASH;
	
	if(!array_key_exists($tpl,$pkTPLHASH))
		$pkTPLHASH[$tpl]=str_replace("\"","\\\"",implode('',file((pkFRONTEND=='setup' ? pkDIRSETUPTPL : pkDIRADMINTPL).$tpl.pkEXTTPL)));
	
	return $pkTPLHASH[$tpl];
	}
#END function pkTpl	


#function pkVersionCompare( string versionid )
#return bool
function pkVersionCompare($versionid)
	{
	if(empty($versionid) || $versionid==pkPHPKIT_VERSION)
		return false;

	$cversion=intval(str_replace('.','',substr($versionid,0,5)));	#checked version number - maybe a new one
	$iversion=intval(str_replace('.','',substr(pkPHPKIT_VERSION,0,5)));	#installed version number

	if($cversion<$iversion) #older
		return false;
	
	if($cversion>$iversion) #newer
		return true;
	
	#equal - maybe one is RC or pl version
	$cadd=strtolower(substr($versionid,6));
	$iadd=strtolower(substr(pkPHPKIT_VERSION,6));
	
	$cplv=substr($cadd,0,2)=='pl' ? intval(substr($cadd,2)) : 0; #level
	$crcv=substr($cadd,0,2)=='rc' ? intval(substr($cadd,2)) : 0;
	
	$iplv=substr($iadd,0,2)=='pl' ? intval(substr($iadd,2)) : 0; #level
	$ircv=substr($iadd,0,2)=='rc' ? intval(substr($iadd,2)) : 0;	


	#pl version & pl/major version
	#rc version & rc version
	#pl version & rc version
	#major & rc version	
	return ($cplv>$iplv || $crcv>$ircv || (!$crcv && $ircv) || ($cplv && $ircv)) ? true : false;
	}
#END function pkVersionCompare
?>